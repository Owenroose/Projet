<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminRoleController extends Controller
{
    public function index()
    {
        // Vérifier la permission au niveau de la méthode
        if (!auth()->user()->can('read-role')) {
            abort(403, 'Accès non autorisé');
        }

        $roles = Role::with(['permissions', 'users'])
            ->withCount(['users', 'permissions'])
            ->paginate(10);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        if (!auth()->user()->can('create-role')) {
            abort(403, 'Accès non autorisé');
        }

        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create-role')) {
            abort(403, 'Accès non autorisé');
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
            'users' => ['nullable', 'array'],
            'users.*' => ['exists:users,id'],
        ]);

        DB::transaction(function () use ($validatedData) {
            $role = Role::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'] ?? null,
            ]);

            // Assigner les permissions
            $role->syncPermissions($validatedData['permissions'] ?? []);

            // Assigner les utilisateurs
            if (!empty($validatedData['users'])) {
                $users = User::whereIn('id', $validatedData['users'])->get();
                foreach ($users as $user) {
                    $user->assignRole($role);
                }
            }
        });

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rôle créé avec succès avec ' . count($validatedData['users'] ?? []) . ' utilisateur(s) assigné(s).');
    }

    public function show(Role $role)
    {
        if (!auth()->user()->can('read-role')) {
            abort(403, 'Accès non autorisé');
        }

        $role->load(['permissions', 'users']);
        $availableUsers = User::whereDoesntHave('roles', function ($query) use ($role) {
            $query->where('roles.id', $role->id);
        })->get();

        $availablePermissions = Permission::whereNotIn('id',
            $role->permissions->pluck('id')
        )->get();

        $roleStats = [
            'users_count' => $role->users->count(),
            'permissions_count' => $role->permissions->count(),
            'created_days_ago' => $role->created_at->diffInDays(now()),
            'last_updated' => $role->updated_at->diffForHumans(),
        ];

        return view('admin.roles.show', compact('role', 'availableUsers', 'availablePermissions', 'roleStats'));
    }

    public function edit(Role $role)
    {
        if (!auth()->user()->can('update-role')) {
            abort(403, 'Accès non autorisé');
        }

        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $users = User::all();
        $roleUsers = $role->users->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions', 'users', 'roleUsers'));
    }

    public function update(Request $request, Role $role)
    {
        if (!auth()->user()->can('update-role')) {
            abort(403, 'Accès non autorisé');
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
            'users' => ['nullable', 'array'],
            'users.*' => ['exists:users,id'],
        ]);

        DB::transaction(function () use ($validatedData, $role) {
            // Mettre à jour le rôle
            $role->update([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'] ?? null,
            ]);

            // Synchroniser les permissions
            $role->syncPermissions($validatedData['permissions'] ?? []);

            // Synchroniser les utilisateurs
            $currentUserIds = $role->users->pluck('id')->toArray();
            $newUserIds = $validatedData['users'] ?? [];

            // Retirer le rôle aux utilisateurs qui ne sont plus dans la liste
            $usersToRemove = array_diff($currentUserIds, $newUserIds);
            if (!empty($usersToRemove)) {
                $users = User::whereIn('id', $usersToRemove)->get();
                foreach ($users as $user) {
                    $user->removeRole($role);
                }
            }

            // Ajouter le rôle aux nouveaux utilisateurs
            $usersToAdd = array_diff($newUserIds, $currentUserIds);
            if (!empty($usersToAdd)) {
                $users = User::whereIn('id', $usersToAdd)->get();
                foreach ($users as $user) {
                    $user->assignRole($role);
                }
            }
        });

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(Role $role)
    {
        if (!Auth::user()->can('delete-role')) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier si le rôle est utilisé
        if ($role->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer ce rôle car il est assigné à ' . $role->users()->count() . ' utilisateur(s).');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')
            ->with('success', 'Rôle supprimé avec succès.');
    }

    /**
     * Assigner un utilisateur à un rôle via AJAX
     */
    public function assignUser(Request $request, Role $role)
    {
        if (!auth()->user()->can('update-role')) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->hasRole($role)) {
            return response()->json([
                'error' => 'L\'utilisateur a déjà ce rôle.'
            ], 422);
        }

        $user->assignRole($role);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur assigné au rôle avec succès.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    /**
     * Retirer un utilisateur d'un rôle via AJAX
     */
    public function removeUser(Request $request, Role $role)
    {
        if (!auth()->user()->can('update-role')) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($request->user_id);

        if (!$user->hasRole($role)) {
            return response()->json([
                'error' => 'L\'utilisateur n\'a pas ce rôle.'
            ], 422);
        }

        $user->removeRole($role);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur retiré du rôle avec succès.'
        ]);
    }

    /**
     * Assigner une permission à un rôle via AJAX
     */
    public function assignPermission(Request $request, Role $role)
    {
        if (!auth()->user()->can('update-role')) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'permission_name' => ['required', 'exists:permissions,name'],
        ]);

        $permission = Permission::where('name', $request->permission_name)->first();

        if ($role->hasPermissionTo($permission)) {
            return response()->json([
                'error' => 'Le rôle a déjà cette permission.'
            ], 422);
        }

        $role->givePermissionTo($permission);

        return response()->json([
            'success' => true,
            'message' => 'Permission assignée au rôle avec succès.',
            'permission' => [
                'id' => $permission->id,
                'name' => $permission->name,
            ]
        ]);
    }

    /**
     * Retirer une permission d'un rôle via AJAX
     */
    public function removePermission(Request $request, Role $role)
    {
        if (!auth()->user()->can('update-role')) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'permission_name' => ['required', 'exists:permissions,name'],
        ]);

        $permission = Permission::where('name', $request->permission_name)->first();

        if (!$role->hasPermissionTo($permission)) {
            return response()->json([
                'error' => 'Le rôle n\'a pas cette permission.'
            ], 422);
        }

        $role->revokePermissionTo($permission);

        return response()->json([
            'success' => true,
            'message' => 'Permission retirée du rôle avec succès.'
        ]);
    }
}
