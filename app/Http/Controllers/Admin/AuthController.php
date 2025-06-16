<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\FailedLoginAttempt;
use App\Models\AdminLog;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion admin
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Traiter la connexion admin
     */
    public function login(Request $request)
    {
        $ip = $request->ip();
        $email = $request->input('email');

        // Vérifier le rate limiting
        $rateLimitKey = 'admin_login:' . $ip;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            throw ValidationException::withMessages([
                'email' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
            ]);
        }

        // Vérifier si l'IP est bloquée
        if (FailedLoginAttempt::isIpBlocked($ip, 5, 60)) {
            throw ValidationException::withMessages([
                'email' => 'IP temporairement bloquée pour trop de tentatives.',
            ]);
        }

        // Validation des données
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Vérifier si l'utilisateur existe et est admin
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user || !$user->isAdmin()) {
            $this->handleFailedLogin($request, $email, 'invalid_admin');
            throw ValidationException::withMessages([
                'email' => 'Ces identifiants ne correspondent à aucun compte administrateur.',
            ]);
        }

        // Vérifier si le compte est verrouillé
        if ($user->isLocked()) {
            $this->handleFailedLogin($request, $email, 'account_locked');
            throw ValidationException::withMessages([
                'email' => 'Ce compte est temporairement verrouillé.',
            ]);
        }

        // Tentative de connexion
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Réinitialiser les tentatives échouées
            $user->resetFailedAttempts();
            RateLimiter::clear($rateLimitKey);
            
            // Mettre à jour les informations de connexion
            $user->updateLastLogin($ip);
            
            // Logger la connexion réussie
            AdminLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => 'POST',
                'description' => "{$user->name} s'est connecté à l'administration",
                'severity' => 'info',
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        // Échec de la connexion
        $this->handleFailedLogin($request, $email, 'invalid_credentials');
        $user->incrementFailedAttempts();

        throw ValidationException::withMessages([
            'email' => 'Ces identifiants sont incorrects.',
        ]);
    }

    /**
     * Déconnexion admin
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            // Logger la déconnexion
            AdminLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => 'POST',
                'description' => "{$user->name} s'est déconnecté de l'administration",
                'severity' => 'info',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Afficher le formulaire de réinitialisation de mot de passe
     */
    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Envoyer le lien de réinitialisation
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user || !$user->isAdmin()) {
            // Ne pas révéler si l'email existe ou non
            return back()->with('status', 'Si cet email correspond à un compte administrateur, un lien de réinitialisation a été envoyé.');
        }

        // Ici, vous pourriez implémenter l'envoi d'email de réinitialisation
        // Pour la sécurité, on ne révèle pas si l'email existe

        // Logger la tentative de réinitialisation
        AdminLog::create([
            'user_id' => $user->id,
            'action' => 'password_reset_request',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => 'POST',
            'description' => "Demande de réinitialisation de mot de passe pour {$user->name}",
            'severity' => 'warning',
        ]);

        return back()->with('status', 'Si cet email correspond à un compte administrateur, un lien de réinitialisation a été envoyé.');
    }

    /**
     * Gérer les échecs de connexion
     */
    private function handleFailedLogin(Request $request, ?string $email, string $reason): void
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // Logger la tentative échouée
        FailedLoginAttempt::logAttempt(
            $email,
            $ip,
            $userAgent,
            'admin_login',
            ['reason' => $reason]
        );

        // Incrémenter le rate limiting
        RateLimiter::hit('admin_login:' . $ip, 900); // 15 minutes
    }

    /**
     * Vérifier l'état de sécurité du compte
     */
    public function checkSecurityStatus(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isAdmin()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        return response()->json([
            'two_factor_enabled' => $user->two_factor_enabled,
            'last_login' => $user->last_login_at?->format('d/m/Y H:i'),
            'failed_attempts' => $user->failed_login_attempts,
            'is_locked' => $user->isLocked(),
            'permissions' => $user->permissions ?? [],
        ]);
    }

    /**
     * Activer/désactiver l'authentification à deux facteurs
     */
    public function toggleTwoFactor(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isAdmin()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $user->update([
            'two_factor_enabled' => $request->boolean('enabled'),
        ]);

        // Logger l'action
        AdminLog::create([
            'user_id' => $user->id,
            'action' => $request->boolean('enabled') ? 'enable_2fa' : 'disable_2fa',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => $user->name . ' a ' . ($request->boolean('enabled') ? 'activé' : 'désactivé') . ' l\'authentification à deux facteurs',
            'severity' => 'warning',
        ]);

        return response()->json([
            'success' => true,
            'two_factor_enabled' => $user->two_factor_enabled,
        ]);
    }
}
