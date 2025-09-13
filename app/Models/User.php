<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'last_seen_at',
        'is_active',
        'login_count',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'login_count' => 'integer',
        ];
    }

    /**
     * Accesseur pour l'URL complète de l'avatar.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->avatar
                ? Storage::url($this->avatar)
                : $this->getDefaultAvatarUrl()
        );
    }

    /**
     * Accesseur pour les initiales de l'utilisateur.
     */
    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn () => strtoupper(substr($this->name, 0, 2))
        );
    }

    /**
     * Vérifie si l'utilisateur est en ligne (basé sur le cache).
     */
    public function isOnline(): bool
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    /**
     * Vérifie si l'utilisateur est récemment actif (basé sur last_seen_at).
     */
    public function isRecentlyActive(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }

    /**
     * Vérifie si l'utilisateur est actif dans les dernières 24h.
     */
    public function isActiveToday(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subDay());
    }

    /**
     * Met à jour la dernière activité de l'utilisateur.
     */
    public function updateLastSeen(): void
    {
        $this->last_seen_at = now();
        $this->save();

        // Mettre en cache le statut en ligne pour 5 minutes
        Cache::put('user-is-online-' . $this->id, true, now()->addMinutes(5));
    }

    /**
     * Enregistre une connexion.
     */
    public function recordLogin(): void
    {
        $this->increment('login_count');
        $this->last_login_at = now();
        $this->last_seen_at = now();
        $this->save();

        // Mettre en cache le statut en ligne
        Cache::put('user-is-online-' . $this->id, true, now()->addMinutes(5));
    }

    /**
     * Obtient l'URL de l'avatar par défaut.
     */
    public function getDefaultAvatarUrl(): string
    {
        // Utilise Gravatar ou un avatar par défaut
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=200";
    }

    /**
     * Obtient le statut d'activité formaté.
     */
    public function getStatusAttribute(): string
    {
        if (!($this->is_active ?? true)) {
            return 'Désactivé';
        }

        if ($this->isRecentlyActive()) {
            return 'En ligne';
        }

        if ($this->isActiveToday()) {
            return 'Actif aujourd\'hui';
        }

        if ($this->last_seen_at) {
            return 'Vu ' . $this->last_seen_at->diffForHumans();
        }

        return 'Jamais connecté';
    }

    /**
     * Obtient la classe CSS pour le statut.
     */
    public function getStatusClassAttribute(): string
    {
        if (!($this->is_active ?? true)) {
            return 'text-danger';
        }

        if ($this->isRecentlyActive()) {
            return 'text-success';
        }

        if ($this->isActiveToday()) {
            return 'text-warning';
        }

        return 'text-muted';
    }

    /**
     * Scope pour les utilisateurs en ligne (dernière activité < 5 min).
     */
    public function scopeOnline(Builder $query): Builder
    {
        return $query->where('last_seen_at', '>=', now()->subMinutes(5));
    }

    /**
     * Scope pour les utilisateurs récemment actifs (dernière activité < 24h).
     */
    public function scopeRecentlyActive(Builder $query): Builder
    {
        return $query->where('last_seen_at', '>=', now()->subDay());
    }

    /**
     * Scope pour les utilisateurs actifs (is_active = true).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les utilisateurs inactifs.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope pour les utilisateurs vérifiés.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope pour les utilisateurs non vérifiés.
     */
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Scope pour rechercher par nom ou email.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope pour filtrer par rôle.
     */
    public function scopeWithRole(Builder $query, string $role): Builder
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /**
     * Scope pour les utilisateurs avec des rôles.
     */
    public function scopeWithRoles(Builder $query): Builder
    {
        return $query->has('roles');
    }

    /**
     * Scope pour les utilisateurs sans rôles.
     */
    public function scopeWithoutRoles(Builder $query): Builder
    {
        return $query->doesntHave('roles');
    }

    /**
     * Obtient tous les utilisateurs en ligne via le cache.
     */
    public static function getOnlineUsers(): \Illuminate\Support\Collection
    {
        return Cache::remember('online_users', 60, function () {
            return static::where('last_seen_at', '>=', now()->subMinutes(5))
                        ->orderBy('last_seen_at', 'desc')
                        ->get();
        });
    }

    /**
     * Obtient le nombre d'utilisateurs en ligne.
     */
    public static function getOnlineCount(): int
    {
        return Cache::remember('online_users_count', 60, function () {
            return static::where('last_seen_at', '>=', now()->subMinutes(5))->count();
        });
    }

    /**
     * Vérifie si l'utilisateur peut être modifié par l'utilisateur connecté.
     */
    public function canBeEditedBy(User $user): bool
    {
        // Un utilisateur ne peut pas se modifier lui-même via cette interface
        if ($this->id === $user->id) {
            return false;
        }

        // Seuls les super-admins peuvent modifier d'autres super-admins
        if ($this->hasRole('super-admin') && !$user->hasRole('super-admin')) {
            return false;
        }

        return $user->can('update-user');
    }

    /**
     * Vérifie si l'utilisateur peut être supprimé par l'utilisateur connecté.
     */
    public function canBeDeletedBy(User $user): bool
    {
        // Un utilisateur ne peut pas se supprimer lui-même
        if ($this->id === $user->id) {
            return false;
        }

        // Seuls les super-admins peuvent supprimer d'autres super-admins
        if ($this->hasRole('super-admin') && !$user->hasRole('super-admin')) {
            return false;
        }

        return $user->can('delete-user');
    }

    /**
     * Obtient les informations de base pour l'API.
     */
    public function toBasicArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar_url' => $this->avatar_url,
            'initials' => $this->initials,
            'status' => $this->status,
            'is_online' => $this->isRecentlyActive(),
        ];
    }

    /**
     * Boot du modèle pour les événements.
     */
    protected static function boot()
    {
        parent::boot();

        // Supprimer l'avatar lors de la suppression de l'utilisateur
        static::deleting(function ($user) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
        });

        // Nettoyer le cache lors de la mise à jour
        static::updated(function ($user) {
            Cache::forget('online_users');
            Cache::forget('online_users_count');
            Cache::forget('user_stats');
        });
    }
}
