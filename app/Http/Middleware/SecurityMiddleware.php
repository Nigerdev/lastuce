<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\FailedLoginAttempt;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type = 'general'): Response
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // Protection contre les bots malveillants
        if ($this->isSuspiciousBot($userAgent)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        // Rate limiting basé sur le type
        $rateLimitKey = $this->getRateLimitKey($request, $type);
        $maxAttempts = $this->getMaxAttempts($type);
        $decayMinutes = $this->getDecayMinutes($type);

        if (RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            
            // Logger la tentative bloquée
            $this->logBlockedAttempt($request, $type);
            
            return response()->json([
                'error' => 'Trop de tentatives. Réessayez dans ' . $seconds . ' secondes.',
                'retry_after' => $seconds
            ], 429);
        }

        // Vérifier si l'IP est bloquée pour les tentatives de connexion
        if ($type === 'login' && FailedLoginAttempt::isIpBlocked($ip)) {
            return response()->json([
                'error' => 'IP temporairement bloquée pour trop de tentatives de connexion.'
            ], 429);
        }

        // Protection contre les attaques par injection
        if ($this->hasInjectionAttempt($request)) {
            $this->logSecurityThreat($request, 'injection_attempt');
            return response()->json(['error' => 'Requête invalide'], 400);
        }

        // Incrémenter le compteur de rate limiting
        RateLimiter::hit($rateLimitKey, $decayMinutes * 60);

        return $next($request);
    }

    /**
     * Détecter les bots suspects
     */
    private function isSuspiciousBot(?string $userAgent): bool
    {
        if (!$userAgent) {
            return true;
        }

        $suspiciousBots = [
            'sqlmap',
            'nikto',
            'nmap',
            'masscan',
            'nessus',
            'openvas',
            'w3af',
            'skipfish',
            'burp',
            'owasp',
            'python-requests',
            'curl',
            'wget',
            'scrapy',
        ];

        $userAgentLower = strtolower($userAgent);
        
        foreach ($suspiciousBots as $bot) {
            if (str_contains($userAgentLower, $bot)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Générer la clé de rate limiting
     */
    private function getRateLimitKey(Request $request, string $type): string
    {
        $ip = $request->ip();
        return "rate_limit:{$type}:{$ip}";
    }

    /**
     * Obtenir le nombre maximum de tentatives selon le type
     */
    private function getMaxAttempts(string $type): int
    {
        return match($type) {
            'login' => 5,
            'contact' => 3,
            'newsletter' => 5,
            'upload' => 10,
            'api' => 60,
            default => 20
        };
    }

    /**
     * Obtenir la durée de blocage en minutes selon le type
     */
    private function getDecayMinutes(string $type): int
    {
        return match($type) {
            'login' => 15,
            'contact' => 60,
            'newsletter' => 10,
            'upload' => 30,
            'api' => 1,
            default => 5
        };
    }

    /**
     * Détecter les tentatives d'injection
     */
    private function hasInjectionAttempt(Request $request): bool
    {
        $suspiciousPatterns = [
            '/union\s+select/i',
            '/drop\s+table/i',
            '/insert\s+into/i',
            '/delete\s+from/i',
            '/update\s+set/i',
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/\.\.\//i',
            '/etc\/passwd/i',
            '/proc\/self\/environ/i',
        ];

        $allInput = json_encode($request->all());
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $allInput)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Logger une tentative bloquée
     */
    private function logBlockedAttempt(Request $request, string $type): void
    {
        Cache::put(
            "blocked_attempt:{$request->ip()}:" . time(),
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'type' => $type,
                'timestamp' => now(),
            ],
            now()->addHours(24)
        );
    }

    /**
     * Logger une menace de sécurité
     */
    private function logSecurityThreat(Request $request, string $threatType): void
    {
        Cache::put(
            "security_threat:{$request->ip()}:" . time(),
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'input' => $request->all(),
                'threat_type' => $threatType,
                'timestamp' => now(),
            ],
            now()->addDays(7)
        );
    }
}
