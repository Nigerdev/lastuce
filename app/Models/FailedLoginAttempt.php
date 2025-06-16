<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FailedLoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'type',
        'metadata',
        'attempted_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'attempted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for filtering by IP address
     */
    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope for filtering by email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope for filtering by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for recent attempts
     */
    public function scopeRecent($query, $minutes = 60)
    {
        return $query->where('attempted_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Scope for today's attempts
     */
    public function scopeToday($query)
    {
        return $query->whereDate('attempted_at', today());
    }

    /**
     * Get attempts count for IP in timeframe
     */
    public static function getIpAttempts($ip, $minutes = 60)
    {
        return static::byIp($ip)
            ->recent($minutes)
            ->count();
    }

    /**
     * Get attempts count for email in timeframe
     */
    public static function getEmailAttempts($email, $minutes = 60)
    {
        return static::byEmail($email)
            ->recent($minutes)
            ->count();
    }

    /**
     * Check if IP is blocked
     */
    public static function isIpBlocked($ip, $maxAttempts = 5, $minutes = 60)
    {
        return static::getIpAttempts($ip, $minutes) >= $maxAttempts;
    }

    /**
     * Check if email is blocked
     */
    public static function isEmailBlocked($email, $maxAttempts = 3, $minutes = 60)
    {
        return static::getEmailAttempts($email, $minutes) >= $maxAttempts;
    }

    /**
     * Log a failed attempt
     */
    public static function logAttempt($email, $ip, $userAgent, $type = 'login', $metadata = [])
    {
        return static::create([
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'type' => $type,
            'metadata' => $metadata,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Clean old attempts
     */
    public static function cleanOldAttempts($days = 30)
    {
        return static::where('attempted_at', '<', now()->subDays($days))->delete();
    }
}
