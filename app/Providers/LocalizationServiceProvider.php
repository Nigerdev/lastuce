<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\LocalizationHelper;

class LocalizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Enregistrer le helper comme singleton
        $this->app->singleton('localization', function () {
            return new LocalizationHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Directives Blade personnalisées
        $this->registerBladeDirectives();
        
        // View composers globaux
        $this->registerViewComposers();
    }

    /**
     * Enregistrer les directives Blade personnalisées
     */
    private function registerBladeDirectives(): void
    {
        // Directive pour les URLs localisées - Version ultra-simple et sûre
        Blade::directive('localizedRoute', function ($expression) {
            return "<?php echo route({$expression}, ['locale' => app()->getLocale()]); ?>";
        });

        // Directive encore plus simple pour les routes sans paramètres
        Blade::directive('lr', function ($expression) {
            return "<?php echo route({$expression}, ['locale' => app()->getLocale()]); ?>";
        });

        // Directive pour simplifier route() avec locale automatique
        Blade::directive('localeRoute', function ($expression) {
            return "<?php echo route({$expression}, ['locale' => app()->getLocale()]); ?>";
        });

        // Directive pour le switch de langue
        Blade::directive('languageSwitch', function () {
            return "<?php echo view('components.language-switch', ['languages' => App\Helpers\LocalizationHelper::getLanguageLinks()])->render(); ?>";
        });

        // Directive pour les URLs dans une autre langue
        Blade::directive('urlInLocale', function ($expression) {
            return "<?php echo App\Helpers\LocalizationHelper::getCurrentRouteInLocale($expression); ?>";
        });

        // Directive pour la traduction avec fallback
        Blade::directive('transWithFallback', function ($expression) {
            return "<?php echo App\Helpers\LocalizationHelper::trans($expression); ?>";
        });

        // Directive pour les dates localisées
        Blade::directive('localizedDate', function ($expression) {
            return "<?php echo App\Helpers\LocalizationHelper::formatDate($expression); ?>";
        });

        // Directive pour les dates relatives
        Blade::directive('dateForHumans', function ($expression) {
            return "<?php echo App\Helpers\LocalizationHelper::formatDateForHumans($expression); ?>";
        });

        // Directive pour vérifier la langue courante
        Blade::if('locale', function ($locale) {
            return LocalizationHelper::getCurrentLocale() === $locale;
        });

        // Directive pour vérifier si RTL
        Blade::if('rtl', function () {
            return LocalizationHelper::isRTL();
        });
    }

    /**
     * Enregistrer les view composers
     */
    private function registerViewComposers(): void
    {
        // Partager les données de localisation avec toutes les vues
        view()->composer('*', function ($view) {
            $view->with([
                'currentLocale' => LocalizationHelper::getCurrentLocale(),
                'supportedLocales' => LocalizationHelper::getSupportedLocales(),
                'languageLinks' => LocalizationHelper::getLanguageLinks(),
                'isRTL' => LocalizationHelper::isRTL(),
                'htmlLang' => LocalizationHelper::getHtmlLang(),
            ]);
        });

        // Composer pour le layout principal
        view()->composer('layouts.app', function ($view) {
            $currentLocale = LocalizationHelper::getCurrentLocale();
            
            $view->with([
                'seoKeywords' => LocalizationHelper::getSeoKeywords('home', $currentLocale),
                'localeDirection' => LocalizationHelper::isRTL() ? 'rtl' : 'ltr',
            ]);
        });
    }
} 