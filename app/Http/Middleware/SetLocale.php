<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = array_keys(config('app.supported_locales', ['fr', 'en']));
        $defaultLocale = config('app.locale', 'fr');
        
        // 1. Vérifier si la langue est dans l'URL
        $locale = $request->segment(1);
        
        if (in_array($locale, $supportedLocales)) {
            // Langue valide dans l'URL
            App::setLocale($locale);
            Session::put('locale', $locale);
        } else {
            // Pas de langue dans l'URL, déterminer la langue à utiliser
            $locale = $this->detectLocale($request, $supportedLocales, $defaultLocale);
            App::setLocale($locale);
            
            // Rediriger vers l'URL avec la langue si on est sur la racine
            if ($request->is('/')) {
                return redirect("/$locale");
            }
        }
        
        // Définir la locale pour Carbon (dates)
        if ($locale === 'fr') {
            \Carbon\Carbon::setLocale('fr');
        } else {
            \Carbon\Carbon::setLocale('en');
        }
        
        return $next($request);
    }
    
    /**
     * Détecter la langue préférée
     */
    private function detectLocale(Request $request, array $supportedLocales, string $defaultLocale): string
    {
        // 1. Vérifier la session
        if (Session::has('locale') && in_array(Session::get('locale'), $supportedLocales)) {
            return Session::get('locale');
        }
        
        // 2. Vérifier les préférences du navigateur
        $browserLang = $request->getPreferredLanguage($supportedLocales);
        if ($browserLang && in_array($browserLang, $supportedLocales)) {
            return $browserLang;
        }
        
        // 3. Utiliser la langue par défaut
        return $defaultLocale;
    }
}
