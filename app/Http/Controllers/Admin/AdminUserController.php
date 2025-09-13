<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminUserController extends Controller
{
    /**
     * Récupère les rôles d'un utilisateur pour l'affichage dans un modal.
     */
    public function getUserRoles(User $user): JsonResponse
    {
        if (!auth()->user()->can('read-user')) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $roles = $user->roles()->get(['id', 'name', 'display_name', 'description']);
        return response()->json($roles);
    }

    /**
     * Affiche la liste des utilisateurs avec filtres avancés.
     */
    public function index(Request $request): View
{
    if (!auth()->user()->can('read-user')) {
        abort(403, 'Accès non autorisé');
    }

    $query = User::with(['roles']);

    // Filtrage par recherche (nom, email)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%');
        });
    }

    // Filtrage par rôle - CORRECTION ICI
    if ($request->filled('role')) {
        $roleName = $request->role;
        $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    // Filtrage par statut de vérification
    if ($request->filled('verification_status')) {
        switch ($request->verification_status) {
            case 'verified':
                $query->whereNotNull('email_verified_at');
                break;
            case 'unverified':
                $query->whereNull('email_verified_at');
                break;
        }
    }

    // Filtrage par statut d'activité
    if ($request->filled('activity_status')) {
        switch ($request->activity_status) {
            case 'online':
                $query->where('last_seen_at', '>=', Carbon::now()->subMinutes(5));
                break;
            case 'recent':
                $query->whereBetween('last_seen_at', [
                    Carbon::now()->subHours(24),
                    Carbon::now()->subMinutes(5)
                ]);
                break;
            case 'offline':
                $query->where(function($q) {
                    $q->where('last_seen_at', '<', Carbon::now()->subHours(24))
                      ->orWhereNull('last_seen_at');
                });
                break;
        }
    }

    // Tri
    $sortBy = $request->get('sort_by', 'created_at');
    $sortDirection = $request->get('sort_direction', 'desc');

    switch ($sortBy) {
        case 'name':
            $query->orderBy('name', $sortDirection);
            break;
        case 'email':
            $query->orderBy('email', $sortDirection);
            break;
        case 'created_at':
            $query->orderBy('created_at', $sortDirection);
            break;
        case 'last_seen_at':
            $query->orderBy('last_seen_at', $sortDirection);
            break;
        case 'roles_count':
            $query->withCount('roles')->orderBy('roles_count', $sortDirection);
            break;
        default:
            $query->latest();
    }

    $perPage = $request->get('per_page', 15);
    $users = $query->paginate($perPage)->withQueryString();

    // Statistiques des utilisateurs avec cache
    $userStats = Cache::remember('user_stats', 300, function () {
        return [
            'total_users' => User::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
            'active_today' => User::where('last_seen_at', '>=', Carbon::now()->subDay())->count(),
            'new_this_week' => User::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'with_roles' => User::has('roles')->count(),
            'online_now' => User::where('last_seen_at', '>=', Carbon::now()->subMinutes(5))->count(),
        ];
    });

    // Récupérer tous les rôles pour le dropdown
    $roles = Role::orderBy('display_name')->get(['id', 'name', 'display_name', 'description']);

    // Si c'est une requête AJAX pour la recherche en temps réel
    if ($request->ajax()) {
        return response()->json([
            'html' => view('admin.users.partials.table', compact('users'))->render(),
            'pagination' => $users->links()->render(),
            'stats' => $userStats
        ]);
    }

    // Debug pour vérifier les paramètres de filtrage
    if (config('app.debug')) {
        \Log::info('User filtering parameters', [
            'search' => $request->search,
            'role' => $request->role,
            'activity_status' => $request->activity_status,
            'total_users' => $users->total(),
        ]);
    }

    return view('admin.users.index', compact('users', 'userStats', 'roles'));
}

    /**
     * Affiche le formulaire de création d'un nouvel utilisateur.
     */
    public function create(): View
    {
        if (!auth()->user()->can('create-user')) {
            abort(403, 'Accès non autorisé');
        }

        $roles = Role::all(['id', 'name', 'display_name', 'description']);
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!auth()->user()->can('create-user')) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'send_welcome_email' => 'nullable|boolean',
            'email_verified' => 'nullable|boolean',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => $request->email_verified ? now() : null,
                'phone' => $request->phone,
            ];

            // Gestion de l'avatar
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = $avatarPath;
            }

            $user = User::create($userData);

            // Assigner les rôles
            if (!empty($request->roles)) {
                $user->assignRole($request->roles);
            }

            // Envoyer email de bienvenue si demandé
            if ($request->send_welcome_email) {
                try {
                    // Mail::to($user)->send(new WelcomeEmail($user));
                    Log::info("Email de bienvenue envoyé à {$user->email}");
                } catch (\Exception $e) {
                    Log::error("Erreur envoi email bienvenue: " . $e->getMessage());
                }
            }

            DB::commit();

            // Invalider le cache des statistiques
            Cache::forget('user_stats');

            Log::info("Utilisateur créé: {$user->name} ({$user->email}) par " . auth()->user()->name);

            return redirect()->route('admin.users.index')
                           ->with('success', 'Utilisateur créé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur création utilisateur: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Affiche les détails d'un utilisateur.
     */
    public function show(User $user): View
    {
        if (!auth()->user()->can('read-user')) {
            abort(403, 'Accès non autorisé');
        }

        $user->load(['roles.permissions', 'permissions']);
        $availableRoles = Role::whereNotIn('id', $user->roles->pluck('id'))->get();

        // Statistiques de l'utilisateur
        $userStats = [
            'login_count' => $user->login_count ?? 0,
            'last_login' => $user->last_login_at,
            'created_at' => $user->created_at,
            'roles_count' => $user->roles->count(),
            'direct_permissions_count' => $user->permissions->count(),
            'total_permissions' => $user->getAllPermissions()->count(),
            'last_seen' => $user->last_seen_at,
            'is_online' => $user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5)),
        ];

        // Historique des connexions récentes (si vous avez une table de logs)
        // $recentLogins = $user->loginLogs()->latest()->limit(10)->get();

        return view('admin.users.show', compact('user', 'availableRoles', 'userStats'));
    }

    /**
     * Affiche le formulaire de modification d'un utilisateur.
     */
    public function edit(User $user): View
    {
        if (!auth()->user()->can('update-user')) {
            abort(403, 'Accès non autorisé');
        }

        // Empêcher la modification des super-admins par des non-super-admins
        if ($user->hasRole('super-admin') && !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Seul un Super Administrateur peut modifier un Super Administrateur.');
        }

        $roles = Role::all(['id', 'name', 'display_name', 'description']);
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Met à jour un utilisateur existant.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if (!auth()->user()->can('update-user')) {
            abort(403, 'Accès non autorisé');
        }

        // Protection super-admin
        if ($user->hasRole('super-admin') && !auth()->user()->hasRole('super-admin')) {
            return back()->with('error', 'Seul un Super Administrateur peut modifier un Super Administrateur.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'email_verified' => 'nullable|boolean',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->is_active = $request->boolean('is_active', true);

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Gestion de la vérification email
            if ($request->has('email_verified')) {
                $user->email_verified_at = $request->email_verified ? now() : null;
            }

            // Gestion de l'avatar
            if ($request->hasFile('avatar')) {
                // Supprimer l'ancien avatar s'il existe
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            $user->save();

            // Synchroniser les rôles
            if ($request->has('roles')) {
                $user->syncRoles($request->roles ?? []);
            }

            DB::commit();
            Cache::forget('user_stats');

            Log::info("Utilisateur modifié: {$user->name} par " . auth()->user()->name);

            return redirect()->route('admin.users.index')
                           ->with('success', 'Utilisateur mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur modification utilisateur: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la modification: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (!auth()->user()->can('delete-user')) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Protection super-admin
        if ($user->hasRole('super-admin') && !auth()->user()->hasRole('super-admin')) {
            return back()->with('error', 'Seul un Super Administrateur peut supprimer un Super Administrateur.');
        }

        try {
            // Sauvegarder des informations pour les logs
            $userName = $user->name;
            $userEmail = $user->email;

            // Supprimer l'avatar s'il existe
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();

            Cache::forget('user_stats');

            Log::info("Utilisateur supprimé: {$userName} ({$userEmail}) par " . auth()->user()->name);

            return redirect()->route('admin.users.index')
                           ->with('success', 'Utilisateur supprimé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur suppression utilisateur: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Gère les actions en lot (suppression, vérification, assignation de rôles, etc.)
     */
    public function bulkActions(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:delete,verify,unverify,assign-role,remove-role,replace-role,activate,deactivate',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'role_name' => 'nullable|string|exists:roles,name',
            'options' => 'nullable|array',
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;
        $options = $request->options ?? [];

        // Exclure l'utilisateur connecté des suppressions et désactivations
        if (in_array($action, ['delete', 'deactivate'])) {
            $userIds = array_filter($userIds, fn($id) => $id != auth()->id());
        }

        // Exclure les super-admins si demandé
        if (($options['skip_super_admins'] ?? false) && in_array($action, ['assign-role', 'remove-role', 'replace-role', 'delete'])) {
            $superAdminIds = User::role('super-admin')->pluck('id')->toArray();
            $userIds = array_diff($userIds, $superAdminIds);
        }

        if (empty($userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun utilisateur valide sélectionné.'
            ], 400);
        }

        $users = User::whereIn('id', $userIds);

        DB::beginTransaction();
        try {
            $count = 0;
            $message = '';

            switch ($action) {
                case 'delete':
                    if (!auth()->user()->can('delete-user')) {
                        throw new \Exception('Accès non autorisé pour supprimer des utilisateurs.');
                    }

                    $deletedUsers = $users->get(['name', 'email', 'avatar']);

                    // Supprimer les avatars
                    foreach ($deletedUsers as $deletedUser) {
                        if ($deletedUser->avatar && Storage::disk('public')->exists($deletedUser->avatar)) {
                            Storage::disk('public')->delete($deletedUser->avatar);
                        }
                        Log::info("Suppression en lot: {$deletedUser->name} ({$deletedUser->email}) par " . auth()->user()->name);
                    }

                    $count = $users->delete();
                    $message = "{$count} utilisateur(s) supprimé(s) avec succès.";
                    break;

                case 'verify':
                    if (!auth()->user()->can('update-user')) {
                        throw new \Exception('Accès non autorisé pour vérifier des utilisateurs.');
                    }

                    $count = $users->whereNull('email_verified_at')->update(['email_verified_at' => now()]);
                    $message = "{$count} utilisateur(s) vérifié(s) avec succès.";

                    // Envoyer notification si demandé
                    if ($options['send_notification'] ?? false) {
                        $verifiedUsers = User::whereIn('id', $userIds)->whereNotNull('email_verified_at')->get();
                        foreach ($verifiedUsers as $user) {
                            try {
                                // Mail::to($user)->send(new AccountVerifiedEmail($user));
                            } catch (\Exception $e) {
                                Log::error("Erreur envoi email vérification: " . $e->getMessage());
                            }
                        }
                    }
                    break;

                case 'unverify':
                    if (!auth()->user()->can('update-user')) {
                        throw new \Exception('Accès non autorisé pour dé-vérifier des utilisateurs.');
                    }

                    $count = $users->whereNotNull('email_verified_at')->update(['email_verified_at' => null]);
                    $message = "{$count} utilisateur(s) non-vérifié(s) avec succès.";
                    break;

                case 'activate':
                    if (!auth()->user()->can('update-user')) {
                        throw new \Exception('Accès non autorisé pour activer des utilisateurs.');
                    }

                    $count = $users->where('is_active', false)->update(['is_active' => true]);
                    $message = "{$count} utilisateur(s) activé(s) avec succès.";
                    break;

                case 'deactivate':
                    if (!auth()->user()->can('update-user')) {
                        throw new \Exception('Accès non autorisé pour désactiver des utilisateurs.');
                    }

                    $count = $users->where('is_active', true)->update(['is_active' => false]);
                    $message = "{$count} utilisateur(s) désactivé(s) avec succès.";
                    break;

                case 'assign-role':
                    if (!auth()->user()->can('update-user')) {
                        throw new \Exception('Accès non autorisé pour assigner des rôles.');
                    }

                    if (!$request->filled('role_name')) {
                        throw new \Exception('Le nom du rôle est requis pour l\'assignation.');
                    }

                    $role = Role::where('name', $request->role_name)->firstOrFail();
                    $targetUsers = $users->get();

                    foreach ($targetUsers as $user) {
                        if (!$user->hasRole($role->name)) {
                            $user->assignRole($role);
                            $count++;
                        }
                    }

                    $message = "Rôle '{$role->name}' assigné à {$count} utilisateur(s).";
                    break;

                case 'remove-role':
                    if (!auth()->user()->can('update-user')) {
                        throw new \Exception('Accès non autorisé pour retirer des rôles.');
                    }

                    if (!$request->filled('role_name')) {
                        throw new \Exception('Le nom du rôle est requis pour le retrait.');
                    }

                    $role = Role::where('name', $request->role_name)->firstOrFail();
                    $targetUsers = $users->get();

                    foreach ($targetUsers as $user) {
                        if ($user->hasRole($role->name)) {
                            $user->removeRole($role);
                            $count++;
                        }
                    }

                    $message = "Rôle '{$role->name}' retiré de {$count} utilisateur(s).";
                    break;

                case 'replace-role':
                    if (!auth()->user()->can('update-user')) {
                        throw new \Exception('Accès non autorisé pour remplacer des rôles.');
                    }

                    if (!$request->filled('role_name')) {
                        throw new \Exception('Le nom du rôle est requis pour le remplacement.');
                    }

                    $role = Role::where('name', $request->role_name)->firstOrFail();
                    $targetUsers = $users->get();

                    foreach ($targetUsers as $user) {
                        // Supprimer tous les rôles actuels puis assigner le nouveau
                        $user->syncRoles([$role->name]);
                        $count++;
                    }

                    $message = "Rôles remplacés par '{$role->name}' pour {$count} utilisateur(s).";
                    break;

                default:
                    throw new \Exception('Action non supportée: ' . $action);
            }

            DB::commit();
            Cache::forget('user_stats');

            Log::info("Action en lot '{$action}' effectuée sur {$count} utilisateur(s) par " . auth()->user()->name);

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur action en lot: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Importe des utilisateurs depuis un fichier CSV.
     */
    public function import(Request $request): RedirectResponse
    {
        if (!auth()->user()->can('create-user')) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'delimiter' => 'required|string|in:,,;,|',
            'encoding' => 'required|string|in:utf-8,iso-8859-1,windows-1252',
            'skip_header' => 'nullable|boolean',
            'generate_password' => 'nullable|boolean',
            'send_welcome_email' => 'nullable|boolean',
            'update_existing' => 'nullable|boolean',
            'default_role' => 'nullable|string|exists:roles,name',
        ]);

        $file = $request->file('csv_file');
        $delimiter = $request->delimiter;
        $encoding = $request->encoding;
        $skipHeader = $request->boolean('skip_header', true);
        $generatePassword = $request->boolean('generate_password', true);
        $sendWelcomeEmail = $request->boolean('send_welcome_email', false);
        $updateExisting = $request->boolean('update_existing', false);
        $defaultRole = $request->default_role;

        try {
            // Lire le fichier CSV
            $content = file_get_contents($file->getRealPath());

            // Conversion d'encodage si nécessaire
            if ($encoding !== 'utf-8') {
                $content = mb_convert_encoding($content, 'UTF-8', strtoupper($encoding));
            }

            $lines = array_filter(array_map('trim', explode("\n", $content)));

            if (empty($lines)) {
                throw new \Exception('Le fichier semble être vide.');
            }

            $imported = 0;
            $updated = 0;
            $errors = [];
            $maxErrors = 10; // Limiter les erreurs affichées

            DB::beginTransaction();

            $startIndex = $skipHeader ? 1 : 0;

            for ($i = $startIndex; $i < count($lines); $i++) {
                try {
                    $line = $lines[$i];
                    $rowNumber = $i + 1;

                    // Découper la ligne selon le délimiteur
                    $record = str_getcsv($line, $delimiter);

                    // Nettoyer les données
                    $record = array_map('trim', $record);

                    // Validation basique
                    if (count($record) < 2 || empty($record[0]) || empty($record[1])) {
                        throw new \Exception("Nom et email requis à la ligne {$rowNumber}");
                    }

                    $name = $record[0];
                    $email = filter_var($record[1], FILTER_VALIDATE_EMAIL);

                    if (!$email) {
                        throw new \Exception("Email invalide à la ligne {$rowNumber}: {$record[1]}");
                    }

                    $password = $record[2] ?? null;
                    $roles = !empty($record[3]) ? array_filter(explode('|', $record[3])) : [];
                    $phone = $record[4] ?? null;

                    // Générer un mot de passe si nécessaire
                    if (empty($password) && $generatePassword) {
                        $password = Str::random(12);
                    }

                    if (empty($password)) {
                        throw new \Exception("Mot de passe requis à la ligne {$rowNumber}");
                    }

                    // Vérifier si l'utilisateur existe
                    $existingUser = User::where('email', $email)->first();

                    if ($existingUser) {
                        if (!$updateExisting) {
                            throw new \Exception("Utilisateur existe déjà à la ligne {$rowNumber}: {$email}");
                        }

                        // Mettre à jour l'utilisateur existant
                        $existingUser->update([
                            'name' => $name,
                            'password' => Hash::make($password),
                            'phone' => $phone,
                        ]);

                        // Assigner les rôles
                        if (!empty($roles)) {
                            $validRoles = Role::whereIn('name', $roles)->pluck('name');
                            $existingUser->syncRoles($validRoles);
                        } elseif ($defaultRole) {
                            $existingUser->assignRole($defaultRole);
                        }

                        $updated++;

                    } else {
                        // Créer un nouvel utilisateur
                        $user = User::create([
                            'name' => $name,
                            'email' => $email,
                            'password' => Hash::make($password),
                            'phone' => $phone,
                            'email_verified_at' => now(), // Auto-vérifier les imports
                        ]);

                        // Assigner les rôles
                        if (!empty($roles)) {
                            $validRoles = Role::whereIn('name', $roles)->pluck('name');
                            if ($validRoles->count() > 0) {
                                $user->assignRole($validRoles->toArray());
                            }
                        } elseif ($defaultRole) {
                            $user->assignRole($defaultRole);
                        }

                        // Envoyer email de bienvenue si demandé
                        if ($sendWelcomeEmail) {
                            try {
                                // Mail::to($user)->send(new WelcomeEmail($user, $password));
                            } catch (\Exception $e) {
                                Log::error("Erreur envoi email bienvenue: " . $e->getMessage());
                            }
                        }

                        $imported++;
                    }

                } catch (\Exception $e) {
                    if (count($errors) < $maxErrors) {
                        $errors[] = $e->getMessage();
                    }
                    continue;
                }
            }

            if (($imported + $updated) === 0) {
                throw new \Exception('Aucun utilisateur n\'a pu être importé. Vérifiez le format du fichier.');
            }

            DB::commit();
            Cache::forget('user_stats');

            $message = "Import terminé: {$imported} créé(s), {$updated} mis à jour.";
            if (!empty($errors)) {
                $message .= " Erreurs: " . implode('; ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " (et " . (count($errors) - 3) . " autres...)";
                }
            }

            Log::info("Import utilisateurs: {$imported} créés, {$updated} mis à jour par " . auth()->user()->name);

            return redirect()->route('admin.users.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur import utilisateurs: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Exporte la liste des utilisateurs en CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        if (!auth()->user()->can('read-user')) {
            abort(403, 'Accès non autorisé');
        }

        try {
            $query = User::with('roles');

            // Appliquer les mêmes filtres que l'index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }

            if ($request->filled('role')) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }

            if ($request->filled('verification_status')) {
                switch ($request->verification_status) {
                    case 'verified':
                        $query->whereNotNull('email_verified_at');
                        break;
                    case 'unverified':
                        $query->whereNull('email_verified_at');
                        break;
                }
            }

            $users = $query->orderBy('created_at', 'desc')->get();

            $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');

                // BOM pour Excel UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // En-têtes CSV
                fputcsv($file, [
                    'ID',
                    'Nom',
                    'Email',
                    'Téléphone',
                    'Vérifié',
                    'Actif',
                    'Rôles',
                    'Dernière connexion',
                    'Date de création'
                ], ';');

                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->phone ?? '',
                        $user->email_verified_at ? 'Oui' : 'Non',
                        ($user->is_active ?? true) ? 'Oui' : 'Non',
                        $user->roles->pluck('name')->implode('|'),
                        $user->last_seen_at ? $user->last_seen_at->format('d/m/Y H:i') : 'Jamais',
                        $user->created_at->format('d/m/Y H:i'),
                    ], ';');
                }

                fclose($file);
            };

            Log::info("Export utilisateurs par " . auth()->user()->name);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Erreur export utilisateurs: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Réinitialise le mot de passe d'un utilisateur.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        if (!auth()->user()->can('update-user')) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->hasRole('super-admin') && !auth()->user()->hasRole('super-admin')) {
            return back()->with('error', 'Seul un Super Administrateur peut réinitialiser le mot de passe d\'un Super Administrateur.');
        }

        try {
            $newPassword = Str::random(12);
            $user->password = Hash::make($newPassword);
            $user->save();

            // Envoyer le nouveau mot de passe par email
            try {
                // Mail::to($user)->send(new PasswordResetEmail($user, $newPassword));
                $message = 'Mot de passe réinitialisé et envoyé par email à l\'utilisateur.';
            } catch (\Exception $e) {
                Log::error("Erreur envoi email reset password: " . $e->getMessage());
                $message = "Mot de passe réinitialisé. Nouveau mot de passe: {$newPassword}";
            }

            Log::info("Mot de passe réinitialisé pour {$user->name} par " . auth()->user()->name);

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erreur réinitialisation mot de passe: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la réinitialisation du mot de passe.');
        }
    }

    /**
     * Active/désactive un utilisateur.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if (!auth()->user()->can('update-user')) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        if ($user->hasRole('super-admin') && !auth()->user()->hasRole('super-admin')) {
            return back()->with('error', 'Seul un Super Administrateur peut modifier le statut d\'un Super Administrateur.');
        }

        try {
            $user->is_active = !($user->is_active ?? true);
            $user->save();

            $status = $user->is_active ? 'activé' : 'désactivé';

            Log::info("Utilisateur {$status}: {$user->name} par " . auth()->user()->name);

            return back()->with('success', "Utilisateur {$status} avec succès.");

        } catch (\Exception $e) {
            Log::error('Erreur changement statut utilisateur: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du changement de statut.');
        }
    }

    /**
     * Force la vérification d'un utilisateur.
     */
    public function forceVerification(User $user): RedirectResponse
    {
        if (!auth()->user()->can('update-user')) {
            abort(403, 'Accès non autorisé');
        }

        try {
            if ($user->email_verified_at) {
                return back()->with('info', 'L\'utilisateur est déjà vérifié.');
            }

            $user->email_verified_at = now();
            $user->save();

            Log::info("Vérification forcée pour {$user->name} par " . auth()->user()->name);

            return back()->with('success', 'Utilisateur vérifié avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur vérification utilisateur: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la vérification.');
        }
    }

    /**
     * Envoie un email de vérification à un utilisateur.
     */
    public function sendVerificationEmail(User $user): RedirectResponse
    {
        if (!auth()->user()->can('update-user')) {
            abort(403, 'Accès non autorisé');
        }

        try {
            if ($user->email_verified_at) {
                return back()->with('info', 'L\'utilisateur est déjà vérifié.');
            }

            // $user->sendEmailVerificationNotification();

            Log::info("Email de vérification envoyé à {$user->name} par " . auth()->user()->name);

            return back()->with('success', 'Email de vérification envoyé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur envoi email vérification: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'envoi de l\'email.');
        }
    }

    /**
     * Affiche les statistiques détaillées des utilisateurs.
     */
    public function statistics(): View
    {
        if (!auth()->user()->can('read-user')) {
            abort(403, 'Accès non autorisé');
        }

        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'active_users' => User::where('last_seen_at', '>=', Carbon::now()->subDays(30))->count(),
            'new_users_this_month' => User::whereMonth('created_at', Carbon::now()->month)
                                          ->whereYear('created_at', Carbon::now()->year)
                                          ->count(),
            'new_users_this_week' => User::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'users_by_role' => Role::withCount('users')->get(),
            'top_roles' => Role::withCount('users')->orderBy('users_count', 'desc')->limit(5)->get(),
            'inactive_users' => User::where('last_seen_at', '<', Carbon::now()->subDays(90))->count(),
            'never_logged_users' => User::whereNull('last_seen_at')->count(),
        ];

        // Tendance des inscriptions (30 derniers jours)
        $stats['registration_trend'] = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                          ->where('created_at', '>=', Carbon::now()->subDays(30))
                                          ->groupBy('date')
                                          ->orderBy('date')
                                          ->get()
                                          ->map(function ($item) {
                                              return [
                                                  'date' => Carbon::parse($item->date)->format('d/m'),
                                                  'count' => $item->count
                                              ];
                                          });

        // Statistiques d'activité par mois (12 derniers mois)
        $stats['monthly_activity'] = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $stats['monthly_activity']->push([
                'month' => $date->format('M Y'),
                'new_users' => User::whereYear('created_at', $date->year)
                                  ->whereMonth('created_at', $date->month)
                                  ->count(),
                'active_users' => User::whereYear('last_seen_at', $date->year)
                                     ->whereMonth('last_seen_at', $date->month)
                                     ->count(),
            ]);
        }

        return view('admin.users.statistics', compact('stats'));
    }

    /**
     * Affiche l'historique d'activité d'un utilisateur.
     */
    public function activityLog(User $user): View
    {
        if (!auth()->user()->can('read-user')) {
            abort(403, 'Accès non autorisé');
        }

        // Si vous avez un système de logs d'activité
        // $activities = $user->activities()->latest()->paginate(20);

        $userInfo = [
            'user' => $user->load('roles'),
            'login_history' => [], // Remplacez par vos données réelles
            'role_changes' => [], // Historique des changements de rôles
            'profile_changes' => [], // Historique des modifications de profil
        ];

        return view('admin.users.activity-log', compact('user', 'userInfo'));
    }

    /**
     * Génère un rapport d'utilisateurs personnalisé.
     */
    public function generateReport(Request $request): View
    {
        if (!auth()->user()->can('read-user')) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'include_roles' => 'nullable|array',
            'include_roles.*' => 'exists:roles,name',
            'status_filter' => 'nullable|string|in:all,active,inactive,verified,unverified',
        ]);

        $query = User::with('roles');

        // Filtrage par dates
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Filtrage par rôles
        if ($request->filled('include_roles')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->whereIn('name', $request->include_roles);
            });
        }

        // Filtrage par statut
        switch ($request->status_filter) {
            case 'active':
                $query->where('is_active', true);
                break;
            case 'inactive':
                $query->where('is_active', false);
                break;
            case 'verified':
                $query->whereNotNull('email_verified_at');
                break;
            case 'unverified':
                $query->whereNull('email_verified_at');
                break;
        }

        $users = $query->orderBy('created_at', 'desc')->get();
        $roles = Role::all();

        $reportData = [
            'users' => $users,
            'filters' => $request->all(),
            'summary' => [
                'total' => $users->count(),
                'verified' => $users->where('email_verified_at', '!=', null)->count(),
                'active' => $users->where('is_active', true)->count(),
                'with_roles' => $users->filter(fn($u) => $u->roles->count() > 0)->count(),
            ]
        ];

        return view('admin.users.report', compact('reportData', 'roles'));
    }

    /**
     * Sauvegarde en masse les utilisateurs (pour les modifications en lot).
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        if (!auth()->user()->can('update-user')) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'updates' => 'required|array',
            'updates.*.user_id' => 'required|exists:users,id',
            'updates.*.field' => 'required|string|in:name,email,phone,is_active',
            'updates.*.value' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $updated = 0;
            $errors = [];

            foreach ($request->updates as $update) {
                try {
                    $user = User::find($update['user_id']);

                    if (!$user) {
                        continue;
                    }

                    // Protection super-admin
                    if ($user->hasRole('super-admin') && !auth()->user()->hasRole('super-admin')) {
                        $errors[] = "Impossible de modifier {$user->name} (Super Admin)";
                        continue;
                    }

                    // Validation spécifique par champ
                    switch ($update['field']) {
                        case 'email':
                            if (!filter_var($update['value'], FILTER_VALIDATE_EMAIL)) {
                                $errors[] = "Email invalide pour {$user->name}";
                                continue 2;
                            }
                            if (User::where('email', $update['value'])->where('id', '!=', $user->id)->exists()) {
                                $errors[] = "Email déjà utilisé: {$update['value']}";
                                continue 2;
                            }
                            break;
                        case 'is_active':
                            $update['value'] = (bool) $update['value'];
                            break;
                    }

                    $user->{$update['field']} = $update['value'];
                    $user->save();
                    $updated++;

                } catch (\Exception $e) {
                    $errors[] = "Erreur pour {$user->name}: " . $e->getMessage();
                }
            }

            DB::commit();
            Cache::forget('user_stats');

            return response()->json([
                'success' => true,
                'updated' => $updated,
                'errors' => $errors,
                'message' => "{$updated} utilisateur(s) mis à jour."
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur mise à jour en lot: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recherche d'utilisateurs avec autocomplétion.
     */
    public function search(Request $request): JsonResponse
    {
        if (!auth()->user()->can('read-user')) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'q' => 'required|string|min:2|max:50',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $query = $request->q;
        $limit = $request->get('limit', 10);

        $users = User::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->limit($limit)
                    ->get(['id', 'name', 'email', 'avatar'])
                    ->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'avatar' => $user->avatar ? Storage::url($user->avatar) : null,
                            'label' => $user->name . ' (' . $user->email . ')',
                        ];
                    });

        return response()->json($users);
    }

    /**
     * Récupère les informations rapides d'un utilisateur pour les tooltips.
     */
    public function quickInfo(User $user): JsonResponse
    {
        if (!auth()->user()->can('read-user')) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $info = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar ? Storage::url($user->avatar) : null,
            'verified' => (bool) $user->email_verified_at,
            'active' => $user->is_active ?? true,
            'roles' => $user->roles->pluck('name'),
            'created_at' => $user->created_at->format('d/m/Y'),
            'last_seen' => $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Jamais connecté',
            'is_online' => $user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5)),
        ];

        return response()->json($info);
    }

    /**
     * Assigne un rôle à un utilisateur.
     */
   /**
     * Assigne un rôle à un utilisateur.
     */
    public function assignRole(Request $request, User $user): JsonResponse
    {
        if (!auth()->user()->can('update-user')) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        try {
            $roleName = $request->role;

            // Vérifier si l'utilisateur a déjà ce rôle
            if ($user->hasRole($roleName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'L\'utilisateur possède déjà ce rôle.'
                ], 422);
            }

            // Vérifier les permissions pour les super-admins
            if ($roleName === 'super-admin' && !auth()->user()->hasRole('super-admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seul un super-administrateur peut assigner le rôle de super-administrateur.'
                ], 403);
            }

            // Assigner le rôle
            $user->assignRole($roleName);

            // Log de l'action
            Log::info("Rôle '{$roleName}' assigné à {$user->name} par " . auth()->user()->name);

            return response()->json([
                'success' => true,
                'message' => "Le rôle '{$roleName}' a été assigné avec succès à {$user->name}."
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'assignation du rôle: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'assignation du rôle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retire un rôle d'un utilisateur.
     */
    public function removeRole(Request $request, User $user): JsonResponse
    {
        if (!auth()->user()->can('update-user')) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        try {
            $roleName = $request->role;
            $currentUser = auth()->user();

            // Vérifier si l'utilisateur a ce rôle
            if (!$user->hasRole($roleName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'L\'utilisateur ne possède pas ce rôle.'
                ], 422);
            }

            // Vérifications de sécurité pour les super-admins
            if ($roleName === 'super-admin') {
                // Seul un super-admin peut retirer le rôle super-admin
                if (!$currentUser->hasRole('super-admin')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Seul un super-administrateur peut retirer le rôle de super-administrateur.'
                    ], 403);
                }

                // Empêcher la suppression du dernier super-admin
                $superAdminsCount = User::role('super-admin')->count();
                if ($superAdminsCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de retirer le dernier super-administrateur.'
                    ], 422);
                }
            }

            // Empêcher à un utilisateur de se retirer ses propres droits admin
            if ($user->id === $currentUser->id && in_array($roleName, ['super-admin', 'admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas retirer vos propres droits d\'administration.'
                ], 422);
            }

            // Retirer le rôle
            $user->removeRole($roleName);

            // Log de l'action
            Log::info("Rôle '{$roleName}' retiré de {$user->name} par " . $currentUser->name);

            return response()->json([
                'success' => true,
                'message' => "Le rôle '{$roleName}' a été retiré avec succès de {$user->name}."
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du rôle: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du rôle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assigne un rôle à un groupe d'utilisateurs.
     */
    public function bulkAssignRole(Request $request): JsonResponse
    {
        if (!auth()->user()->can('assign-role')) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
            'role_name' => 'required|string|exists:roles,name',
        ]);

        try {
            $users = User::whereIn('id', $request->user_ids)->get();
            $assignedCount = 0;
            foreach ($users as $user) {
                if (!$user->hasRole($request->role_name)) {
                    $user->assignRole($request->role_name);
                    $assignedCount++;
                }
            }
            return response()->json(['success' => true, 'message' => "{$assignedCount} rôles assignés avec succès aux utilisateurs sélectionnés."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'assignation en masse des rôles.'], 500);
        }
    }

    /**
     * Récupère les informations rapides d'un utilisateur pour les tooltips.
     */


    /**
     * Gère l'importation d'utilisateurs.
     */

}
