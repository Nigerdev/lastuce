import './bootstrap';

// Import des modules principaux
import { YouTubePlayer } from './components/YouTubePlayer';
import { FormHandler } from './components/FormHandler';
import { Navigation } from './components/Navigation';
import DynamicComponents from './components/DynamicComponents';
import { Integrations } from './components/Integrations';
import { LazyLoader } from './utils/LazyLoader';
import { SmoothScroll } from './utils/SmoothScroll';
import { BackToTop } from './utils/BackToTop';

// Configuration globale
window.LastuceApp = {
    config: {
        youtubeApiKey: import.meta.env.VITE_YOUTUBE_API_KEY || '',
        googleAnalyticsId: import.meta.env.VITE_GA_ID || '',
        debug: import.meta.env.DEV || false,
    },
    components: {},
    utils: {},
};

// Initialisation de l'application
class LastuceApp {
    constructor() {
        this.init();
    }

    async init() {
        try {
            // Attendre que le DOM soit prêt
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.initComponents());
            } else {
                this.initComponents();
            }
        } catch (error) {
            console.error('Erreur lors de l\'initialisation de l\'application:', error);
        }
    }

    initComponents() {
        // Initialisation des utilitaires
        this.initUtils();
        
        // Initialisation des composants
        this.initYouTubePlayer();
        this.initFormHandlers();
        this.initNavigation();
        this.initDynamicComponents();
        this.initIntegrations();

        // Événement personnalisé pour signaler que l'app est prête
        document.dispatchEvent(new CustomEvent('lastuce:ready'));
    }

    initUtils() {
        window.LastuceApp.utils.lazyLoader = new LazyLoader();
        window.LastuceApp.utils.smoothScroll = new SmoothScroll();
        window.LastuceApp.utils.backToTop = new BackToTop();
    }

    initYouTubePlayer() {
        const playerElements = document.querySelectorAll('[data-youtube-player]');
        playerElements.forEach(element => {
            const player = new YouTubePlayer(element);
            window.LastuceApp.components[`youtube-${element.id || Date.now()}`] = player;
        });
    }

    initFormHandlers() {
        const forms = document.querySelectorAll('form[data-ajax]');
        forms.forEach(form => {
            const handler = new FormHandler(form);
            window.LastuceApp.components[`form-${form.id || Date.now()}`] = handler;
        });
    }

    initNavigation() {
        const nav = new Navigation();
        window.LastuceApp.components.navigation = nav;
    }

    initDynamicComponents() {
        const dynamicComponents = new DynamicComponents();
        window.LastuceApp.components.dynamic = dynamicComponents;
    }

    initIntegrations() {
        const integrations = new Integrations();
        window.LastuceApp.components.integrations = integrations;
    }
}

// Démarrage de l'application
new LastuceApp();
