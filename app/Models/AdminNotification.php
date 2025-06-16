<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'action_text',
        'priority',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific priority
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'gray',
            'normal' => 'blue',
            'high' => 'yellow',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get priority icon for UI
     */
    public function getPriorityIconAttribute(): string
    {
        return match($this->priority) {
            'low' => 'information-circle',
            'normal' => 'bell',
            'high' => 'exclamation-triangle',
            'urgent' => 'exclamation-circle',
            default => 'bell'
        };
    }

    /**
     * Get type icon for UI
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'new_astuce' => 'lightbulb',
            'new_partenariat' => 'handshake',
            'security_alert' => 'shield-exclamation',
            'newsletter_signup' => 'envelope',
            'system_update' => 'cog',
            'backup_completed' => 'archive',
            'maintenance_mode' => 'wrench',
            default => 'bell'
        };
    }

    /**
     * Create a notification for all admin users
     */
    public static function createForAllAdmins(array $data): void
    {
        $admins = User::admins()->get();
        
        foreach ($admins as $admin) {
            static::create(array_merge($data, ['user_id' => $admin->id]));
        }
    }

    /**
     * Create a notification for specific user
     */
    public static function createForUser(int $userId, array $data): static
    {
        return static::create(array_merge($data, ['user_id' => $userId]));
    }

    /**
     * Create a system notification (no specific user)
     */
    public static function createSystem(array $data): static
    {
        return static::create(array_merge($data, ['user_id' => null]));
    }

    /**
     * Clean old notifications
     */
    public static function cleanOldNotifications(int $days = 30): int
    {
        return static::where('created_at', '<', now()->subDays($days))
            ->where('is_read', true)
            ->delete();
    }

    /**
     * Get notification statistics
     */
    public static function getStats(): array
    {
        return [
            'total' => static::count(),
            'unread' => static::unread()->count(),
            'read' => static::read()->count(),
            'urgent' => static::withPriority('urgent')->unread()->count(),
            'high' => static::withPriority('high')->unread()->count(),
            'today' => static::whereDate('created_at', today())->count(),
        ];
    }
}
