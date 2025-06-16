# Guide JavaScript - L'Astuce

Ce guide présente toutes les fonctionnalités JavaScript développées pour le site L'Astuce avec Vite et ES6+.

## 🚀 Installation et Configuration

### Prérequis
- Node.js 16+
- NPM ou Yarn
- Laravel 10+

### Installation
```bash
npm install
```

### Configuration
Créez un fichier `.env` avec les variables suivantes :
```env
VITE_YOUTUBE_API_KEY=your_youtube_api_key
VITE_GA_ID=G-XXXXXXXXXX
```

### Développement
```bash
npm run dev
```

### Production
```bash
npm run build
```

## 📁 Structure des Fichiers

```
resources/js/
├── app.js                 # Point d'entrée principal
├── bootstrap.js           # Configuration Axios et Laravel
├── examples.js            # Exemples d'utilisation
├── components/
│   ├── YouTubePlayer.js   # Player YouTube intégré
│   ├── FormHandler.js     # Formulaires interactifs
│   ├── Navigation.js      # Navigation mobile
│   ├── DynamicComponents.js # Carrousels, modals, filtres
│   └── Integrations.js    # Newsletter, réseaux sociaux, GA
└── utils/
    ├── LazyLoader.js      # Lazy loading des images
    ├── SmoothScroll.js    # Navigation fluide
    └── BackToTop.js       # Bouton retour en haut
```

## 🎯 Fonctionnalités Principales

### 1. Player YouTube Intégré

**Fonctionnalités :**
- API YouTube pour contrôles personnalisés
- Design responsive
- Préchargement des vidéos suggérées
- Contrôles tactiles
- Indicateur de progression

**HTML :**
```html
<div data-youtube-player 
     data-video-id="dQw4w9WgXcQ" 
     data-autoplay="false"
     data-suggested-videos='["abc123", "def456"]'>
</div>
```

**JavaScript :**
```javascript
// Accès au player
const player = window.LastuceApp.components['youtube-player-id'];

// Contrôles
player.play();
player.pause();
player.seekTo(30);
player.setVolume(50);

// Événements
player.element.addEventListener('youtube:ready', (e) => {
    console.log('Player prêt', e.detail.player);
});
```

### 2. Formulaires Interactifs

**Fonctionnalités :**
- Validation côté client en temps réel
- Upload de fichiers avec drag & drop
- Messages de confirmation/erreur
- Soumission AJAX
- Indicateurs de progression

**HTML :**
```html
<form data-ajax action="/contact" method="POST">
    <input type="email" name="email" required 
           data-validation='{"email": true, "required": true}'>
    
    <input type="file" name="attachment" 
           accept="image/*,.pdf" 
           data-max-size="5242880" 
           multiple>
    
    <button type="submit">Envoyer</button>
</form>
```

**Règles de validation disponibles :**
- `required` : Champ obligatoire
- `email` : Format email
- `url` : Format URL
- `minLength` / `maxLength` : Longueur
- `min` / `max` : Valeurs numériques
- `pattern` : Expression régulière
- `phone` : Numéro de téléphone
- `password` : Mot de passe sécurisé
- `confirmPassword` : Confirmation de mot de passe

### 3. Navigation Mobile

**Fonctionnalités :**
- Menu hamburger animé
- Overlay avec fermeture au clic
- Navigation au clavier (Escape)
- Smooth scrolling vers les sections
- Indicateur de section active

**HTML :**
```html
<nav data-navbar>
    <button data-mobile-menu-button></button>
    <div data-mobile-menu class="hidden">
        <a href="#home" data-nav-link>Accueil</a>
        <a href="#about" data-nav-link>À propos</a>
    </div>
</nav>
```

### 4. Carrousel d'Épisodes

**Fonctionnalités :**
- Basé sur Swiper.js
- Configuration responsive
- Autoplay avec pause au survol
- Navigation tactile et clavier
- Pagination dynamique

**HTML :**
```html
<div class="swiper" data-carousel 
     data-slides-per-view="3" 
     data-space-between="20"
     data-autoplay="true" 
     data-navigation="true" 
     data-pagination="true">
    <div class="swiper-wrapper">
        <div class="swiper-slide">Slide 1</div>
        <div class="swiper-slide">Slide 2</div>
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
</div>
```

**Options de configuration :**
- `data-slides-per-view` : Nombre de slides visibles
- `data-space-between` : Espacement entre slides
- `data-autoplay` : Lecture automatique
- `data-autoplay-delay` : Délai en millisecondes
- `data-speed` : Vitesse de transition
- `data-effect` : Effet de transition (slide, fade)

### 5. Modal de Partage

**Fonctionnalités :**
- Partage sur réseaux sociaux
- Copie de lien dans le presse-papiers
- Animation d'ouverture/fermeture
- Fermeture au clic extérieur ou Escape

**HTML :**
```html
<!-- Déclencheur -->
<button data-share 
        data-share-url="https://example.com" 
        data-share-title="Titre"
        data-share-description="Description">
    Partager
</button>

<!-- Modal personnalisé -->
<button data-modal-trigger="custom-modal">Ouvrir Modal</button>
<div id="custom-modal" class="modal hidden" data-modal-title="Titre">
    <p>Contenu du modal</p>
</div>
```

### 6. Filtres en Temps Réel

**Fonctionnalités :**
- Filtrage instantané
- Filtres multiples
- Animation des éléments
- Compteur de résultats

**HTML :**
```html
<div data-filter-container data-filter-target=".episode-item">
    <button data-filter="all" class="active">Tous</button>
    <button data-filter="tech">Tech</button>
    <button data-filter="lifestyle">Lifestyle</button>
</div>

<div class="episode-item" data-filter-category="tech,web">Episode Tech</div>
<div class="episode-item" data-filter-category="lifestyle">Episode Lifestyle</div>
```

### 7. Recherche Instantanée

**Fonctionnalités :**
- Recherche en temps réel avec debounce
- Recherche dans plusieurs champs
- Mise en évidence des résultats
- Bouton de réinitialisation

**HTML :**
```html
<div class="search-container">
    <input type="text" data-search 
           data-search-target=".searchable-item"
           data-search-fields="title,content,description"
           placeholder="Rechercher...">
    <button data-search-clear>×</button>
</div>

<div class="searchable-item">
    <h3 class="title">Titre de l'épisode</h3>
    <p class="content">Contenu de l'épisode...</p>
    <p class="description">Description...</p>
</div>
```

### 8. Newsletter Signup

**Fonctionnalités :**
- Validation email en temps réel
- Soumission AJAX
- Messages de succès/erreur
- Animation de confirmation
- Protection CSRF

**HTML :**
```html
<form data-newsletter-form>
    <input type="email" placeholder="Votre email" required>
    <button type="submit" data-original-text="S'inscrire">S'inscrire</button>
</form>
```

**Endpoint Laravel requis :**
```php
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
```

### 9. Partage Réseaux Sociaux

**Fonctionnalités :**
- Partage sur Facebook, Twitter, LinkedIn
- WhatsApp et Telegram
- Partage par email
- Boutons flottants
- Tracking des partages

**HTML :**
```html
<button data-social-share="facebook" 
        data-share-url="https://example.com"
        data-share-title="Titre"
        data-share-description="Description">
    Partager sur Facebook
</button>

<!-- Partage flottant -->
<div data-floating-share class="fixed right-4 top-1/2">
    <button data-social-share="facebook">FB</button>
    <button data-social-share="twitter">TW</button>
</div>
```

### 10. Lazy Loading

**Fonctionnalités :**
- Chargement paresseux des images
- Support des iframes et vidéos
- Chargement de contenu dynamique
- Effet shimmer pendant le chargement
- Images de fallback

**HTML :**
```html
<!-- Images -->
<img class="lazy" data-src="image.jpg" alt="Image">

<!-- Arrière-plans -->
<div class="lazy" data-bg="background.jpg"></div>

<!-- Iframes -->
<iframe class="lazy" data-src="https://youtube.com/embed/..."></iframe>

<!-- Contenu dynamique -->
<div data-lazy-content="/api/content/123"></div>
```

### 11. Smooth Scroll

**Fonctionnalités :**
- Navigation fluide vers les sections
- Plusieurs fonctions d'easing
- Offset personnalisable
- Parallax scrolling
- Scroll spy pour navigation active

**HTML :**
```html
<a href="#section1">Aller à la section 1</a>
<button data-scroll-to="section2">Aller à la section 2</button>

<!-- Parallax -->
<div data-parallax="0.5">Élément parallax</div>
```

### 12. Bouton Retour en Haut

**Fonctionnalités :**
- Apparition automatique après scroll
- Indicateur de progression circulaire
- Animation de rebond
- Masquage automatique après inactivité
- Position personnalisable

**HTML (optionnel) :**
```html
<button data-back-to-top></button>
```

### 13. Google Analytics

**Fonctionnalités :**
- Tracking automatique des pages
- Événements personnalisés
- Tracking du scroll depth
- Tracking des interactions

**Configuration :**
```javascript
// Tracker un événement
integrations.trackCustomEvent('video_play', {
    category: 'engagement',
    label: 'episode_123',
    value: 1
});

// Tracker un changement de page
integrations.trackPageChange('Nouvelle page', '/nouvelle-page');
```

## 🎨 Styles CSS Requis

### Classes Tailwind Utilisées
```css
/* Navigation */
.navbar-scrolled { /* Navbar après scroll */ }

/* Formulaires */
.form-group { /* Conteneur de champ */ }
.field-error { /* Message d'erreur */ }

/* Lazy Loading */
.lazy-loading { /* Pendant le chargement */ }
.lazy-loaded { /* Après chargement */ }
.lazy-error { /* En cas d'erreur */ }

/* Filtres */
.filtered-out { /* Élément filtré */ }
.filtered-in { /* Élément visible */ }

/* Recherche */
.search-hidden { /* Élément masqué par recherche */ }
```

### Animations Personnalisées
```css
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.shimmer-effect {
    animation: shimmer 1.5s infinite;
}
```

## 🔧 API et Événements

### Événements Globaux
```javascript
// Application prête
document.addEventListener('lastuce:ready', () => {
    console.log('Application prête !');
});

// Contenu mis à jour
document.addEventListener('content:updated', () => {
    // Réinitialiser les composants
});
```

### Événements Spécifiques
```javascript
// YouTube Player
element.addEventListener('youtube:ready', (e) => {});
element.addEventListener('youtube:statechange', (e) => {});

// Formulaires
form.addEventListener('form:success', (e) => {});
form.addEventListener('form:error', (e) => {});

// Navigation
document.addEventListener('navigation:menuOpen', () => {});
document.addEventListener('navigation:menuClose', () => {});

// Carrousel
element.addEventListener('carousel:slideChange', (e) => {});

// Modal
modal.addEventListener('modal:open', () => {});
modal.addEventListener('modal:close', () => {});

// Newsletter
form.addEventListener('newsletter:success', (e) => {});
form.addEventListener('newsletter:error', (e) => {});

// Lazy Loading
element.addEventListener('lazy:loaded', () => {});
document.addEventListener('lazy:allLoaded', (e) => {});

// Smooth Scroll
document.addEventListener('smoothScroll:complete', (e) => {});

// Bouton retour en haut
button.addEventListener('backToTop:show', () => {});
button.addEventListener('backToTop:hide', () => {});
```

## 🚀 Optimisations Performances

### Vite Configuration
- Code splitting automatique
- Tree shaking
- Minification avec Terser
- Optimisation des dépendances

### Lazy Loading
- Intersection Observer API
- Préchargement intelligent
- Images responsive

### Debouncing et Throttling
- Événements de scroll optimisés
- Recherche avec debounce
- Redimensionnement avec throttle

### Memory Management
- Nettoyage automatique des event listeners
- Destruction des composants
- Gestion des timeouts

## 🐛 Débogage

### Mode Debug
```javascript
// Activer le mode debug
window.LastuceApp.config.debug = true;

// Logs détaillés disponibles dans la console
```

### Outils de Développement
```javascript
// Accès aux composants
console.log(window.LastuceApp.components);
console.log(window.LastuceApp.utils);

// Statistiques lazy loading
console.log(window.LastuceApp.utils.lazyLoader.getStats());

// Progression du scroll
console.log(window.LastuceApp.utils.backToTop.getScrollProgress());
```

## 📱 Responsive Design

Tous les composants sont conçus pour être responsive :

- **Mobile First** : Optimisé pour mobile d'abord
- **Touch Friendly** : Interactions tactiles
- **Adaptive** : S'adapte à toutes les tailles d'écran
- **Performance** : Optimisé pour les connexions lentes

## 🔒 Sécurité

- **CSRF Protection** : Tokens automatiques
- **XSS Prevention** : Échappement des données
- **Content Security Policy** : Compatible CSP
- **Input Validation** : Validation côté client et serveur

## 📈 Analytics et Tracking

### Événements Trackés Automatiquement
- Clics sur les boutons de partage
- Inscriptions newsletter
- Utilisation du player YouTube
- Scroll depth
- Interactions avec les formulaires

### Événements Personnalisés
```javascript
// Tracker un événement personnalisé
window.LastuceApp.components.integrations.trackCustomEvent('custom_event', {
    category: 'engagement',
    label: 'custom_label',
    value: 1
});
```

## 🚀 Déploiement

### Build de Production
```bash
npm run build
```

### Variables d'Environnement
```env
# Production
VITE_YOUTUBE_API_KEY=your_production_key
VITE_GA_ID=G-PRODUCTION-ID

# Développement
VITE_YOUTUBE_API_KEY=your_dev_key
VITE_GA_ID=G-DEV-ID
```

### Optimisations Serveur
- Compression Gzip/Brotli
- Cache des assets
- CDN pour les ressources statiques
- HTTP/2 Push

## 🤝 Contribution

### Structure du Code
- **ES6+ Modules** : Import/Export
- **Classes ES6** : Programmation orientée objet
- **Async/Await** : Gestion asynchrone
- **JSDoc** : Documentation du code

### Standards de Code
- **ESLint** : Linting JavaScript
- **Prettier** : Formatage du code
- **Conventional Commits** : Messages de commit

### Tests
```bash
# Tests unitaires (à implémenter)
npm run test

# Tests E2E (à implémenter)
npm run test:e2e
```

## 📚 Ressources

- [Documentation Vite](https://vitejs.dev/)
- [Swiper.js](https://swiperjs.com/)
- [Vanilla LazyLoad](https://github.com/verlok/vanilla-lazyload)
- [GSAP](https://greensock.com/gsap/)
- [YouTube IFrame API](https://developers.google.com/youtube/iframe_api_reference)

## 🆘 Support

Pour toute question ou problème :

1. Consultez les exemples dans `resources/js/examples.js`
2. Vérifiez la console pour les erreurs
3. Activez le mode debug
4. Consultez la documentation des dépendances

---

**Développé avec ❤️ pour L'Astuce** 