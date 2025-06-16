/**
 * Exemples d'utilisation des composants JavaScript L'Astuce
 * 
 * Ce fichier contient des exemples d'utilisation de tous les composants
 * développés pour le site L'Astuce.
 */

// ===== EXEMPLES D'UTILISATION =====

// 1. YOUTUBE PLAYER
// HTML requis :
/*
<div data-youtube-player 
     data-video-id="dQw4w9WgXcQ" 
     data-autoplay="false"
     data-suggested-videos='["abc123", "def456"]'>
</div>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    // Accéder au player YouTube
    const player = window.LastuceApp.components['youtube-player-id'];
    
    // Contrôler le player
    player.play();
    player.pause();
    player.seekTo(30); // Aller à 30 secondes
    player.setVolume(50); // Volume à 50%
    
    // Écouter les événements
    player.element.addEventListener('youtube:ready', (e) => {
        console.log('Player YouTube prêt', e.detail.player);
    });
    
    player.element.addEventListener('youtube:statechange', (e) => {
        console.log('État changé', e.detail.state);
    });
});

// 2. FORMULAIRES INTERACTIFS
// HTML requis :
/*
<form data-ajax action="/contact" method="POST">
    <div class="form-group">
        <input type="email" name="email" required 
               data-validation='{"email": true, "required": true}'>
    </div>
    
    <div class="form-group">
        <input type="file" name="attachment" 
               accept="image/*,.pdf" 
               data-max-size="5242880" 
               multiple>
    </div>
    
    <button type="submit" data-original-text="Envoyer">Envoyer</button>
</form>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    // Accéder au gestionnaire de formulaire
    const form = document.querySelector('form[data-ajax]');
    const handler = window.LastuceApp.components[`form-${form.id}`];
    
    // Valider manuellement
    const isValid = handler.validate();
    
    // Soumettre manuellement
    handler.submit();
    
    // Réinitialiser
    handler.reset();
    
    // Écouter les événements
    form.addEventListener('form:success', (e) => {
        console.log('Formulaire envoyé avec succès', e.detail);
    });
    
    form.addEventListener('form:error', (e) => {
        console.log('Erreur formulaire', e.detail);
    });
});

// 3. NAVIGATION MOBILE
// HTML requis :
/*
<nav data-navbar>
    <button data-mobile-menu-button></button>
    <div data-mobile-menu class="hidden">
        <a href="#home" data-nav-link>Accueil</a>
        <a href="#about" data-nav-link>À propos</a>
    </div>
</nav>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    const nav = window.LastuceApp.components.navigation;
    
    // Contrôler le menu
    nav.openMenu();
    nav.closeMenu();
    nav.toggle();
    
    // Vérifier l'état
    console.log('Menu ouvert:', nav.isOpen());
    
    // Changer le lien actif
    nav.setActiveLink('#about');
    
    // Écouter les événements
    document.addEventListener('navigation:menuOpen', () => {
        console.log('Menu mobile ouvert');
    });
    
    document.addEventListener('navigation:menuClose', () => {
        console.log('Menu mobile fermé');
    });
});

// 4. CARROUSEL D'ÉPISODES
// HTML requis :
/*
<div class="swiper" data-carousel 
     data-slides-per-view="3" 
     data-space-between="20"
     data-autoplay="true" 
     data-autoplay-delay="3000"
     data-navigation="true" 
     data-pagination="true">
    <div class="swiper-wrapper">
        <div class="swiper-slide">Slide 1</div>
        <div class="swiper-slide">Slide 2</div>
        <div class="swiper-slide">Slide 3</div>
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
</div>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    const dynamic = window.LastuceApp.components.dynamic;
    const carouselElement = document.querySelector('[data-carousel]');
    const swiper = dynamic.getCarousel(carouselElement);
    
    // Contrôler le carrousel
    swiper.slideNext();
    swiper.slidePrev();
    swiper.slideTo(2); // Aller au slide 2
    
    // Écouter les événements
    carouselElement.addEventListener('carousel:slideChange', (e) => {
        console.log('Slide actif:', e.detail.activeIndex);
    });
});

// 5. MODAL DE PARTAGE
// HTML requis :
/*
<button data-modal-trigger="share-modal">Partager</button>

<div id="share-modal" class="modal hidden" data-modal-title="Partager">
    <p>Contenu du modal</p>
</div>

<!-- Ou utiliser le bouton de partage direct -->
<button data-share 
        data-share-url="https://example.com" 
        data-share-title="Titre à partager"
        data-share-description="Description">
    Partager
</button>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    const dynamic = window.LastuceApp.components.dynamic;
    
    // Ouvrir un modal
    dynamic.openModalById('share-modal');
    
    // Fermer tous les modals
    dynamic.closeAllModals();
});

// 6. FILTRES EN TEMPS RÉEL
// HTML requis :
/*
<div data-filter-container data-filter-target=".episode-item">
    <button data-filter="all" class="active">Tous</button>
    <button data-filter="tech">Tech</button>
    <button data-filter="lifestyle">Lifestyle</button>
</div>

<div class="episode-item" data-filter-category="tech">Episode Tech</div>
<div class="episode-item" data-filter-category="lifestyle">Episode Lifestyle</div>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    const dynamic = window.LastuceApp.components.dynamic;
    
    // Effacer tous les filtres
    dynamic.clearAllFilters();
    
    // Écouter les changements de filtre
    const filterContainer = document.querySelector('[data-filter-container]');
    filterContainer.addEventListener('filter:applied', (e) => {
        console.log('Filtres actifs:', e.detail.activeFilters);
    });
});

// 7. RECHERCHE INSTANTANÉE
// HTML requis :
/*
<div class="search-container">
    <input type="text" data-search 
           data-search-target=".searchable-item"
           data-search-fields="title,content"
           placeholder="Rechercher...">
    <button data-search-clear>×</button>
</div>

<div class="searchable-item">
    <h3 class="title">Titre de l'épisode</h3>
    <p class="content">Contenu de l'épisode...</p>
</div>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    const dynamic = window.LastuceApp.components.dynamic;
    
    // Effacer toutes les recherches
    dynamic.clearAllSearches();
    
    // Écouter les résultats de recherche
    const searchInput = document.querySelector('[data-search]');
    searchInput.addEventListener('search:performed', (e) => {
        console.log(`Recherche: "${e.detail.query}", ${e.detail.resultsCount} résultats`);
    });
});

// 8. NEWSLETTER SIGNUP
// HTML requis :
/*
<form data-newsletter-form>
    <input type="email" placeholder="Votre email" required>
    <button type="submit" data-original-text="S'inscrire">S'inscrire</button>
</form>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    const integrations = window.LastuceApp.components.integrations;
    
    // Inscrire un email manuellement
    integrations.subscribeToNewsletter('user@example.com');
    
    // Écouter les événements
    const newsletterForm = document.querySelector('[data-newsletter-form]');
    newsletterForm.addEventListener('newsletter:success', (e) => {
        console.log('Inscription réussie:', e.detail);
    });
    
    newsletterForm.addEventListener('newsletter:error', (e) => {
        console.log('Erreur inscription:', e.detail);
    });
});

// 9. PARTAGE RÉSEAUX SOCIAUX
// HTML requis :
/*
<button data-social-share="facebook" 
        data-share-url="https://example.com"
        data-share-title="Titre"
        data-share-description="Description">
    Partager sur Facebook
</button>

<button data-social-share="twitter">Partager sur Twitter</button>
<button data-social-share="linkedin">Partager sur LinkedIn</button>

<!-- Partage flottant -->
<div data-floating-share class="fixed right-4 top-1/2 transform translate-x-full">
    <button data-social-share="facebook">FB</button>
    <button data-social-share="twitter">TW</button>
</div>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    const integrations = window.LastuceApp.components.integrations;
    
    // Partager manuellement
    integrations.shareContent('facebook', 'https://example.com', 'Titre', 'Description');
    
    // Copier un lien
    integrations.copyToClipboard('https://example.com');
});

// 10. LAZY LOADING
// HTML requis :
/*
<img class="lazy" data-src="image.jpg" alt="Image">
<div class="lazy" data-bg="background.jpg"></div>
<iframe class="lazy" data-src="https://youtube.com/embed/..."></iframe>

<!-- Contenu lazy -->
<div data-lazy-content="/api/content/123"></div>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    const lazyLoader = window.LastuceApp.utils.lazyLoader;
    
    // Ajouter des éléments dynamiquement
    const newImg = document.createElement('img');
    newImg.dataset.src = 'new-image.jpg';
    lazyLoader.addElement(newImg);
    
    // Forcer le chargement de tous les éléments
    lazyLoader.loadAll();
    
    // Précharger des images importantes
    lazyLoader.preloadImages([
        '/images/hero.jpg',
        '/images/logo.png'
    ]);
    
    // Obtenir les statistiques
    const stats = lazyLoader.getStats();
    console.log(`Progression: ${stats.loadingProgress}%`);
    
    // Écouter les événements
    document.addEventListener('lazy:allLoaded', (e) => {
        console.log(`${e.detail.loadedCount} éléments chargés`);
    });
});

// 11. SMOOTH SCROLL
// HTML requis :
/*
<a href="#section1">Aller à la section 1</a>
<button data-scroll-to="section2">Aller à la section 2</button>

<section id="section1">Section 1</section>
<section id="section2">Section 2</section>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    const smoothScroll = window.LastuceApp.utils.smoothScroll;
    
    // Scroller vers un élément
    smoothScroll.scrollTo('section1');
    smoothScroll.scrollTo(document.getElementById('section2'));
    smoothScroll.scrollTo(500); // Position en pixels
    
    // Scroller vers le haut/bas
    smoothScroll.scrollToTop();
    smoothScroll.scrollToBottom();
    
    // Configuration
    smoothScroll.setDuration(1000); // 1 seconde
    smoothScroll.setEasing('easeInOutQuad');
    smoothScroll.setOffset(100); // Offset de 100px
    
    // Révéler un élément
    const element = document.getElementById('section1');
    smoothScroll.revealElement(element, {
        offset: 50,
        duration: 800,
        callback: () => console.log('Élément révélé')
    });
    
    // Écouter les événements
    document.addEventListener('smoothScroll:complete', (e) => {
        console.log('Scroll terminé à la position:', e.detail.position);
    });
});

// 12. BOUTON RETOUR EN HAUT
// HTML requis (optionnel, créé automatiquement) :
/*
<button data-back-to-top></button>
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    const backToTop = window.LastuceApp.utils.backToTop;
    
    // Contrôler le bouton
    backToTop.show();
    backToTop.hide();
    backToTop.toggle();
    
    // Configuration
    backToTop.setThreshold(500); // Apparaît après 500px de scroll
    backToTop.setPosition('bottom-left'); // Position du bouton
    
    // Obtenir des informations
    console.log('Bouton visible:', backToTop.isButtonVisible());
    console.log('Progression du scroll:', backToTop.getScrollProgress());
    
    // Écouter les événements
    const button = document.querySelector('[data-back-to-top]');
    button.addEventListener('backToTop:show', () => {
        console.log('Bouton retour en haut affiché');
    });
    
    button.addEventListener('backToTop:hide', () => {
        console.log('Bouton retour en haut masqué');
    });
});

// 13. GOOGLE ANALYTICS
// Configuration dans .env :
/*
VITE_GA_ID=G-XXXXXXXXXX
*/

// Utilisation programmatique :
document.addEventListener('lastuce:ready', () => {
    const integrations = window.LastuceApp.components.integrations;
    
    // Tracker des événements personnalisés
    integrations.trackCustomEvent('video_play', {
        category: 'engagement',
        label: 'episode_123',
        value: 1
    });
    
    // Tracker un changement de page (SPA)
    integrations.trackPageChange('Nouvelle page', '/nouvelle-page');
});

// ===== ÉVÉNEMENTS GLOBAUX =====

// Application prête
document.addEventListener('lastuce:ready', () => {
    console.log('Application L\'Astuce prête !');
    console.log('Composants disponibles:', Object.keys(window.LastuceApp.components));
    console.log('Utilitaires disponibles:', Object.keys(window.LastuceApp.utils));
});

// Contenu mis à jour (pour les SPA)
document.addEventListener('content:updated', () => {
    // Réinitialiser les composants pour le nouveau contenu
    window.LastuceApp.utils.lazyLoader.update();
});

// ===== CONFIGURATION GLOBALE =====

// Configuration dans .env :
/*
VITE_YOUTUBE_API_KEY=your_youtube_api_key
VITE_GA_ID=G-XXXXXXXXXX
*/

// Accès à la configuration :
console.log('Configuration:', window.LastuceApp.config);

// ===== NETTOYAGE =====

// Nettoyer tous les composants (utile pour les SPA)
function cleanupLastuceApp() {
    Object.values(window.LastuceApp.components).forEach(component => {
        if (component.destroy) component.destroy();
    });
    
    Object.values(window.LastuceApp.utils).forEach(util => {
        if (util.destroy) util.destroy();
    });
} 