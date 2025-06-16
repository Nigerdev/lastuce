<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
        'permissions',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'permissions' => 'array',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'two_factor_recovery_codes' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Admin logs relationship
     */
    public function adminLogs(): HasMany
    {
        return $this->hasMany(AdminLog::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return false;
        }
        
        return in_array($permission, $this->permissions);
    }

    /**
     * Check if user account is locked
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Lock user account
     */
    public function lockAccount(int $minutes = 30): void
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Unlock user account
     */
    public function unlockAccount(): void
    {
        $this->update([
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_login_attempts');
        
        // Lock account after 5 failed attempts
        if ($this->failed_login_attempts >= 5) {
            $this->lockAccount();
        }
    }

    /**
     * Reset failed login attempts
     */
    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Update last login info
     */
    public function updateLastLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    /**
     * Scope for users with specific role
     */
    public function scopeWithRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for locked users
     */
    public function scopeLocked($query)
    {
        return $query->where('locked_until', '>', now());
    }

    /**
     * Get user's display role
     */
    public function getDisplayRoleAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrateur',
            'moderator' => 'Modérateur',
            'user' => 'Utilisateur',
            default => 'Inconnu'
        };
    }

    /**
     * Get available permissions
     */
    public static function getAvailablePermissions(): array
    {
        return [
            'episodes.create' => 'Créer des épisodes',
            'episodes.edit' => 'Modifier des épisodes',
            'episodes.delete' => 'Supprimer des épisodes',
            'astuces.moderate' => 'Modérer les astuces',
            'astuces.delete' => 'Supprimer les astuces',
            'partenariats.manage' => 'Gérer les partenariats',
            'newsletter.manage' => 'Gérer la newsletter',
            'blog.create' => 'Créer des articles de blog',
            'blog.edit' => 'Modifier des articles de blog',
            'blog.delete' => 'Supprimer des articles de blog',
            'users.manage' => 'Gérer les utilisateurs',
            'logs.view' => 'Voir les logs',
            'settings.manage' => 'Gérer les paramètres',
        ];
    }

    /**
     * Get available roles
     */
    public static function getAvailableRoles(): array
    {
        return [
            'admin' => 'Administrateur',
            'moderator' => 'Modérateur',
            'user' => 'Utilisateur',
        ];
    }
}
