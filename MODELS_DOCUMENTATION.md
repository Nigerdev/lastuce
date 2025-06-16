# Documentation des Modèles Laravel - L'Astuce

## Vue d'ensemble

Cette documentation présente les modèles Laravel créés pour le site **L'Astuce**, incluant leurs relations, scopes, validation et fonctionnalités avancées.

## Modèles disponibles

### 1. Episode

Le modèle principal pour gérer les épisodes de l'émission L'Astuce.

#### Fonctionnalités

- **Types** : `episode`, `coulisse`, `bonus`
- **Statuts** : publié/brouillon, programmé
- **Integration YouTube** : extraction automatique des IDs vidéo
- **Gestion des médias** : via Spatie Media Library
- **SEO-friendly** : génération automatique des slugs

#### Scopes disponibles

```php
// Filtres par statut
Episode::published()->get();
Episode::draft()->get();
Episode::scheduled()->get();

// Filtres par type
Episode::episodes()->get();
Episode::coulisses()->get();
Episode::bonus()->get();

// Filtres par date
Episode::recent()->get();
Episode::aireddate->get();

// Recherche
Episode::search('cuisine')->get();
```

#### Accessors utiles

```php
$episode = Episode::first();

// URLs YouTube
$episode->youtube_video_id;        // ID de la vidéo
$episode->youtube_thumbnail;       // URL de la miniature
$episode->youtube_embed_url;       // URL d'embed

// Statuts
$episode->is_aired;                // Déjà diffusé
$episode->is_scheduled;            // Programmé
$episode->formatted_type;          // Type formaté en français
$episode->status;                  // Statut global
```

#### Validation

```php
$rules = Episode::rules();
// Utilisation dans un Controller ou FormRequest
```

### 2. AstucesSoumise

Gestion des astuces soumises par les utilisateurs.

#### Statuts disponibles

- `en_attente` : En attente de validation
- `approuve` : Astuce approuvée
- `rejete` : Astuce rejetée

#### Scopes

```php
// Par statut
AstucesSoumise::enAttente()->get();
AstucesSoumise::approuve()->get();
AstucesSoumise::rejete()->get();

// Recherche
AstucesSoumise::search('cuisine')->get();
AstucesSoumise::byEmail('user@example.com')->get();
```

#### Méthodes de gestion

```php
$astuce = AstucesSoumise::first();

// Changer le statut
$astuce->approuver('Excellente astuce !');
$astuce->rejeter('Déjà traitée dans un épisode précédent');
$astuce->remettrEnAttente();

// Vérifications
$astuce->is_en_attente;
$astuce->is_approuve;
$astuce->hasAttachments();
```

#### Statistiques

```php
// Compter par statut
$stats = AstucesSoumise::countByStatus();

// Obtenir les soumissions récentes
$recent = AstucesSoumise::getRecentSubmissions(10);

// Nombre en attente
$pending = AstucesSoumise::getPendingCount();
```

### 3. Partenariat

Gestion des demandes de partenariat.

#### Statuts

- `nouveau` : Nouvelle demande
- `en_cours` : En cours de traitement
- `accepte` : Partenariat accepté
- `refuse` : Partenariat refusé

#### Scopes et méthodes

```php
// Par statut
Partenariat::nouveau()->get();
Partenariat::active()->get();  // nouveau + en_cours
Partenariat::traite()->get();  // accepte + refuse

// Gestion des statuts
$partenariat = Partenariat::first();
$partenariat->marquerEnCours('Étude en cours');
$partenariat->accepter('Accord trouvé');
$partenariat->refuser('Budget insuffisant');

// Notes internes
$partenariat->ajouterNotes('Nouvelle information importante');
```

#### Recherche avancée

```php
$results = Partenariat::searchAdvanced([
    'status' => 'en_cours',
    'entreprise' => 'TechStart',
    'date_from' => '2024-01-01',
    'date_to' => '2024-12-31'
]);
```

### 4. NewsletterAbonne

Gestion des abonnés à la newsletter.

#### Statuts

- `actif` : Abonné actif
- `inactif` : Abonné inactif
- `desabonne` : Désabonné

#### Méthodes de gestion

```php
// Inscription
$abonne = NewsletterAbonne::abonnerEmail('user@example.com');

// Désabonnement par token
$abonne = NewsletterAbonne::desabonnerParToken($token);

// Gestion manuelle
$abonne = NewsletterAbonne::first();
$abonne->activer();
$abonne->desactiver();
$abonne->desabonner();

// Vérifications
$abonne->peutRecevoirNewsletter();
$abonne->estRecentementInscrit(7); // 7 jours
```

#### Statistiques avancées

```php
// Statistiques générales
$stats = NewsletterAbonne::getStatistiques();
// Retourne: total, actifs, taux_actifs, nouveaux_ce_mois, etc.

// Croissance mensuelle
$croissance = NewsletterAbonne::getCroissanceParMois(12);
```

#### Accessors utiles

```php
$abonne = NewsletterAbonne::first();

$abonne->unsubscribe_url;           // URL de désabonnement
$abonne->duree_abonnement;          // "2 ans", "3 mois", etc.
$abonne->formatted_date_inscription; // Date formatée
```

### 5. BlogArticle

Articles de blog pour les actualités coulisses.

#### Statuts et visibilité

```php
// Scopes
BlogArticle::published()->get();
BlogArticle::draft()->get();
BlogArticle::scheduled()->get();
BlogArticle::publishedAndVisible()->get(); // Publiés ET visibles

// Par date
BlogArticle::recent()->get();
BlogArticle::byYear(2024)->get();
BlogArticle::byMonth(2024, 6)->get();
```

#### Gestion de publication

```php
$article = BlogArticle::first();

// Publication
$article->publier();                    // Publier maintenant
$article->publier(new DateTime('2024-12-25')); // Publier à une date
$article->programmer(new DateTime('2024-12-25')); // Programmer
$article->depublier();                  // Dépublier

// Vérifications
$article->is_visible;      // Visible par le public
$article->is_scheduled;    // Programmé
$article->is_draft;        // Brouillon
```

#### Fonctionnalités avancées

```php
$article = BlogArticle::first();

// SEO
$article->getSeoTitle();
$article->getSeoDescription();
$article->getSeoKeywords();

// Lecture
$article->reading_time;     // Temps de lecture en minutes
$article->word_count;       // Nombre de mots

// Médias
$article->hasFeaturedImage();
$article->getFeaturedImage('thumb');
```

#### Archives et recherche

```php
// Archives par mois/année
$archives = BlogArticle::getArchives();

// Articles populaires
$popular = BlogArticle::getPopularArticles(5);

// Recherche avancée
$results = BlogArticle::searchAdvanced([
    'status' => 'published',
    'search' => 'coulisses',
    'year' => 2024,
    'month' => 6
]);
```

## Installation et configuration

### 1. Exécuter les migrations

```bash
php artisan migrate
```

### 2. Peupler avec des données de test

```bash
php artisan db:seed
```

### 3. Configuration de Spatie Media Library

Les modèles `Episode`, `AstucesSoumise` et `BlogArticle` utilisent Spatie Media Library. Assurez-vous d'avoir configuré le package :

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
php artisan migrate
```

## Exemples d'utilisation

### Dashboard admin

```php
// Statistiques rapides
$stats = [
    'episodes_published' => Episode::published()->count(),
    'astuces_pending' => AstucesSoumise::getPendingCount(),
    'partenariats_new' => Partenariat::getNewCount(),
    'newsletter_active' => NewsletterAbonne::getActiveCount(),
    'blog_scheduled' => BlogArticle::getScheduledCount(),
];
```

### Page d'accueil

```php
// Derniers épisodes
$episodes = Episode::published()->recent()->limit(6)->get();

// Articles de blog récents
$articles = BlogArticle::getRecentArticles(3);

// Statistiques newsletter
$newsletterStats = NewsletterAbonne::getStatistiques();
```

### API endpoints

```php
// API episodes avec filtres
Route::get('/api/episodes', function (Request $request) {
    $query = Episode::published();
    
    if ($request->type) {
        $query->byType($request->type);
    }
    
    if ($request->search) {
        $query = Episode::search($request->search);
    }
    
    return $query->recent()->paginate(12);
});
```

### Gestion des médias

```php
// Ajouter une image à un épisode
$episode = Episode::first();
$episode->addMediaFromRequest('image')
    ->toMediaCollection('thumbnails');

// Récupérer l'image
$thumbnail = $episode->getFirstMediaUrl('thumbnails', 'thumb');
```

## Validation et règles

Tous les modèles incluent des méthodes statiques pour la validation :

```php
// Dans un FormRequest
public function rules()
{
    return Episode::rules();
}

// Pour les mises à jour (gestion des uniques)
public function rules()
{
    return Episode::updateRules($this->episode->id);
}
```

## Events et Notifications

Les modèles incluent des hooks pour les événements :

```php
// Dans les méthodes boot() des modèles, vous pouvez déclencher des events :

// Nouvelle astuce soumise
event(new NewAstuceSubmitted($astuce));

// Changement de statut partenariat
event(new PartnershipStatusChanged($partenariat));

// Nouvel abonné newsletter
event(new NewsletterSubscribed($abonne));
```

## Commandes utiles

```bash
# Peupler la base avec des données de test
php artisan db:seed

# Tester les modèles en console
php artisan tinker
>>> Episode::published()->recent()->count()
>>> AstucesSoumise::enAttente()->first()
>>> NewsletterAbonne::getStatistiques()

# Réinitialiser et repeupler
php artisan migrate:fresh --seed
```

## Relations entre modèles

### Relations potentielles à ajouter

```php
// User (si vous avez un système d'authentification)
// Dans User.php
public function astucesSoumises()
{
    return $this->hasMany(AstucesSoumise::class, 'email', 'email');
}

public function newsletterAbonnement()
{
    return $this->hasOne(NewsletterAbonne::class, 'email', 'email');
}

// Relations inverses dans les modèles correspondants existent déjà
```

Cette documentation couvre l'essentiel des fonctionnalités. N'hésitez pas à explorer le code des modèles pour découvrir d'autres méthodes et fonctionnalités spécialisées ! 