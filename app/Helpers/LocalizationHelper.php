<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class LocalizationHelper
{
    /**
     * Obtenir toutes les langues supportées
     */
    public static function getSupportedLocales(): array
    {
        return config('app.supported_locales', []);
    }

    /**
     * Obtenir la langue courante
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Vérifier si une langue est supportée
     */
    public static function isSupportedLocale(string $locale): bool
    {
        return array_key_exists($locale, self::getSupportedLocales());
    }

    /**
     * Obtenir l'URL traduite pour une route donnée
     */
    public static function getLocalizedRoute(string $routeName, string $locale = null, array $parameters = []): string
    {
        if (!$locale) {
            $locale = self::getCurrentLocale();
        }

        // Ajouter la locale aux paramètres
        $parameters = array_merge(['locale' => $locale], $parameters);

        try {
            $url = route($routeName, $parameters);
            return is_string($url) ? $url : '';
        } catch (\Exception $e) {
            // Fallback vers la route avec la locale par défaut
            try {
                $parameters['locale'] = config('app.locale', 'fr');
                $url = route($routeName, $parameters);
                return is_string($url) ? $url : '';
            } catch (\Exception $e2) {
                // Dernier fallback vers l'accueil
                return route('home', ['locale' => $locale]);
            }
        }
    }

    /**
     * Obtenir l'URL de la page courante dans une autre langue
     */
    public static function getCurrentRouteInLocale(string $locale): string
    {
        $route = Route::current();
        
        if (!$route) {
            return self::getLocalizedRoute('home', $locale);
        }

        $routeName = $route->getName();
        $parameters = $route->parameters();
        
        // Remplacer la locale dans les paramètres
        $parameters['locale'] = $locale;

        try {
            return route($routeName, $parameters);
        } catch (\Exception $e) {
            // Si la route n'existe pas dans cette langue, rediriger vers l'accueil
            return self::getLocalizedRoute('home', $locale);
        }
    }

    /**
     * Obtenir le nom de la langue dans sa langue native
     */
    public static function getLocaleName(string $locale): string
    {
        $locales = self::getSupportedLocales();
        return $locales[$locale]['name'] ?? $locale;
    }

    /**
     * Obtenir le drapeau de la langue
     */
    public static function getLocaleFlag(string $locale): string
    {
        $locales = self::getSupportedLocales();
        return $locales[$locale]['flag'] ?? '';
    }

    /**
     * Déterminer la direction du texte pour une langue
     */
    public static function isRTL(string $locale = null): bool
    {
        if (!$locale) {
            $locale = self::getCurrentLocale();
        }

        $locales = self::getSupportedLocales();
        return $locales[$locale]['rtl'] ?? false;
    }

    /**
     * Obtenir les liens de langue pour le switch de langue
     */
    public static function getLanguageLinks(): array
    {
        $links = [];
        $currentRoute = Route::current();
        
        foreach (self::getSupportedLocales() as $locale => $config) {
            $links[] = [
                'locale' => $locale,
                'name' => $config['name'],
                'flag' => $config['flag'],
                'url' => self::getCurrentRouteInLocale($locale),
                'active' => $locale === self::getCurrentLocale()
            ];
        }

        return $links;
    }

    /**
     * Traduire une clé avec fallback
     */
    public static function trans(string $key, array $replace = [], string $locale = null): string
    {
        if (!$locale) {
            $locale = self::getCurrentLocale();
        }

        $translation = __($key, $replace, $locale);
        
        // Si la traduction n'existe pas et qu'on n'est pas sur la langue de fallback
        if ($translation === $key && $locale !== config('app.fallback_locale')) {
            return __($key, $replace, config('app.fallback_locale'));
        }

        return $translation;
    }

    /**
     * Obtenir une URL absolue avec locale
     */
    public static function localizedUrl(string $path = '', string $locale = null): string
    {
        if (!$locale) {
            $locale = self::getCurrentLocale();
        }

        $path = ltrim($path, '/');
        return url("/{$locale}/{$path}");
    }

    /**
     * Rediriger vers une URL localisée
     */
    public static function redirectToLocalized(string $path = '', string $locale = null, int $status = 302)
    {
        return redirect(self::localizedUrl($path, $locale), $status);
    }

    /**
     * Obtenir le code de langue pour HTML lang attribute
     */
    public static function getHtmlLang(string $locale = null): string
    {
        if (!$locale) {
            $locale = self::getCurrentLocale();
        }

        // Convertir les codes de langue pour HTML
        $htmlLangMap = [
            'fr' => 'fr-FR',
            'en' => 'en-US',
        ];

        return $htmlLangMap[$locale] ?? $locale;
    }

    /**
     * Formater une date selon la locale
     */
    public static function formatDate($date, string $format = null, string $locale = null): string
    {
        if (!$locale) {
            $locale = self::getCurrentLocale();
        }

        if (!$date instanceof \Carbon\Carbon) {
            $date = \Carbon\Carbon::parse($date);
        }

        // Formats par défaut selon la langue
        if (!$format) {
            $format = $locale === 'fr' ? 'd/m/Y' : 'm/d/Y';
        }

        return $date->locale($locale)->format($format);
    }

    /**
     * Formater une date de manière human-readable
     */
    public static function formatDateForHumans($date, string $locale = null): string
    {
        if (!$locale) {
            $locale = self::getCurrentLocale();
        }

        if (!$date instanceof \Carbon\Carbon) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->locale($locale)->diffForHumans();
    }

    /**
     * Obtenir les mots-clés SEO traduits
     */
    public static function getSeoKeywords(string $page, string $locale = null): string
    {
        if (!$locale) {
            $locale = self::getCurrentLocale();
        }

        $keywords = [
            'fr' => [
                'home' => 'astuces, conseils, lifestyle, émission, youtube, france',
                'episodes' => 'épisodes, vidéos, astuces pratiques, conseils quotidien',
                'blog' => 'blog, articles, conseils, actualités, lifestyle',
                'contact' => 'contact, équipe, collaboration, partenariat'
            ],
            'en' => [
                'home' => 'tips, advice, lifestyle, show, youtube, france',
                'episodes' => 'episodes, videos, practical tips, daily advice',
                'blog' => 'blog, articles, advice, news, lifestyle',
                'contact' => 'contact, team, collaboration, partnership'
            ]
        ];

        return $keywords[$locale][$page] ?? '';
    }
} 