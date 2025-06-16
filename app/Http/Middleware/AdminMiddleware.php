<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // Vérifier si le compte est verrouillé
        if ($user->isLocked()) {
            Auth::logout();
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Compte verrouillé'], 423);
            }
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Votre compte est temporairement verrouillé.']);
        }

        // Vérifier si l'utilisateur est admin
        if (!$user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Accès refusé'], 403);
            }
            abort(403, 'Accès refusé');
        }

        // Vérifier la permission spécifique si fournie
        if ($permission && !$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Permission insuffisante'], 403);
            }
            abort(403, 'Permission insuffisante');
        }

        return $next($request);
    }
}
