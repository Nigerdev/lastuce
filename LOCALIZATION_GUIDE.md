# Guide du système de traduction L'Astuce

Ce guide explique comment utiliser le système de traduction français/anglais implémenté pour le site L'Astuce.

## 🌍 Architecture du système

### Configuration

- **Langues supportées :** Français (fr) et Anglais (en)
- **Langue par défaut :** Français
- **Langue de fallback :** Français
- **Structure des URLs :** `/{locale}/page` (ex: `/fr/episodes`, `/en/episodes`)

### Composants principaux

1. **Middleware SetLocale** : Détection et configuration automatique de la langue
2. **Helper LocalizationHelper** : Fonctions utilitaires pour la localisation
3. **Trait Translatable** : Gestion des contenus multilingues dans les modèles
4. **Service Provider** : Enregistrement des directives Blade et view composers

## 📝 Utilisation dans les vues

### Traductions de base

```blade
<!-- Utilisation standard -->
{{ __('app.nav.home') }}
{{ __('episodes.title') }}

<!-- Avec paramètres -->
{{ __('episodes.details.views_count', ['count' => $episode->vues]) }}
```

### URLs traduites

```blade
<!-- Lien vers une page dans la langue courante -->
<a href="{{ route('episodes.index', ['locale' => app()->getLocale()]) }}">
    {{ __('app.nav.episodes') }}
</a>
```

### Sélecteur de langue

```blade
<!-- Inclusion du composant -->
@include('components.language-switch', ['languages' => $languageLinks])
```

## 🗃️ Gestion des contenus multilingues

### Dans les modèles

```php
// Récupérer un contenu traduit
$episode->titre; // Retourne automatiquement la traduction

// Définir une traduction
$episode->setTranslation('titre', 'Mon titre', 'fr');
$episode->setTranslation('titre', 'My title', 'en');
$episode->save();
```

## 📁 Structure des fichiers de traduction

```
resources/lang/
├── fr/
│   ├── app.php          # Traductions générales
│   ├── episodes.php     # Traductions des épisodes
│   ├── forms.php        # Traductions des formulaires
│   └── ...
└── en/
    ├── app.php
    ├── episodes.php
    ├── forms.php
    └── ...
```

## 🚀 Installation

### 1. Générer les migrations pour rendre les modèles translatables

```bash
php artisan make:translatable-migrations
php artisan migrate
```

### 2. Utiliser le trait dans vos modèles

```php
use App\Traits\Translatable;

class Episode extends Model
{
    use Translatable;
    
    protected $translatable = [
        'titre',
        'description',
        'seo_title',
        'seo_description',
    ];
}
```

## 🔧 Fonctionnalités principales

✅ **Détection automatique de langue** via URL et préférences navigateur  
✅ **URLs SEO-friendly** avec préfixes de langue (/fr/, /en/)  
✅ **Middleware intelligent** pour la gestion des redirections  
✅ **Trait Translatable** pour les modèles multilingues  
✅ **Helper LocalizationHelper** avec fonctions utilitaires  
✅ **Directives Blade personnalisées** (@languageSwitch, @localizedDate, etc.)  
✅ **Composant de sélecteur de langue** avec Alpine.js  
✅ **Gestion des contenus JSON** dans la base de données  
✅ **Fallback automatique** vers le français  
✅ **Support SEO complet** (hreflang, métadonnées, etc.)  
✅ **View composers globaux** pour les variables de localisation  

Le système est maintenant prêt à être utilisé ! 🎉 