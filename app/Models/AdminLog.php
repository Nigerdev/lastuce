<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'description',
        'severity',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was affected
     */
    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Scope for filtering by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get formatted description
     */
    public function getFormattedDescriptionAttribute()
    {
        $user = $this->user ? $this->user->name : 'Système';
        $model = $this->model_type ? class_basename($this->model_type) : '';
        
        return "{$user} a {$this->action} {$model} #{$this->model_id}";
    }

    /**
     * Get severity color for UI
     */
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'info' => 'blue',
            'warning' => 'yellow',
            'error' => 'red',
            'critical' => 'purple',
            default => 'gray'
        };
    }
}
