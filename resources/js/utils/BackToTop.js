/**
 * Bouton retour en haut avec animations et fonctionnalités avancées
 */
export class BackToTop {
    constructor() {
        this.button = null;
        this.isVisible = false;
        this.threshold = 300;
        this.scrollProgress = 0;
        this.hideTimeout = null;
        
        this.init();
    }

    init() {
        this.createButton();
        this.bindEvents();
        this.updateVisibility();
    }

    createButton() {
        // Vérifier s'il existe déjà un bouton
        this.button = document.querySelector('[data-back-to-top]');
        
        if (!this.button) {
            this.button = this.createDefaultButton();
            document.body.appendChild(this.button);
        }

        this.setupButton();
    }

    createDefaultButton() {
        const button = document.createElement('button');
        button.setAttribute('data-back-to-top', '');
        button.className = `
            fixed bottom-6 right-6 z-50 w-12 h-12 bg-red-600 hover:bg-red-700 
            text-white rounded-full shadow-lg transition-all duration-300 ease-in-out
            transform translate-y-16 opacity-0 hover:scale-110 focus:outline-none 
            focus:ring-2 focus:ring-red-500 focus:ring-offset-2
        `.replace(/\s+/g, ' ').trim();
        
        button.innerHTML = `
            <div class="relative w-full h-full flex items-center justify-center">
                <!-- Icône flèche -->
                <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                
                <!-- Indicateur de progression circulaire -->
                <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 48 48">
                    <circle 
                        cx="24" 
                        cy="24" 
                        r="20" 
                        fill="none" 
                        stroke="rgba(255,255,255,0.2)" 
                        stroke-width="2"
                    />
                    <circle 
                        cx="24" 
                        cy="24" 
                        r="20" 
                        fill="none" 
                        stroke="rgba(255,255,255,0.8)" 
                        stroke-width="2" 
                        stroke-dasharray="125.6" 
                        stroke-dashoffset="125.6"
                        class="progress-circle transition-all duration-300 ease-out"
                    />
                </svg>
            </div>
        `;

        button.setAttribute('aria-label', 'Retour en haut de page');
        button.setAttribute('title', 'Retour en haut');
        
        return button;
    }

    setupButton() {
        // Ajouter les classes de base si elles n'existent pas
        if (!this.button.classList.contains('fixed')) {
            this.button.classList.add(
                'fixed', 'bottom-6', 'right-6', 'z-50',
                'transition-all', 'duration-300', 'ease-in-out'
            );
        }

        // S'assurer que le bouton est masqué initialement
        this.hideButton(false);
    }

    bindEvents() {
        // Clic sur le bouton
        this.button.addEventListener('click', (e) => {
            e.preventDefault();
            this.scrollToTop();
        });

        // Gestion du scroll avec throttling
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    this.handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Gestion du redimensionnement
        window.addEventListener('resize', this.debounce(() => {
            this.updateVisibility();
        }, 250));

        // Hover effects
        this.button.addEventListener('mouseenter', () => {
            this.onButtonHover();
        });

        this.button.addEventListener('mouseleave', () => {
            this.onButtonLeave();
        });

        // Gestion du clavier
        this.button.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.scrollToTop();
            }
        });
    }

    handleScroll() {
        const scrollTop = window.pageYOffset;
        const documentHeight = Math.max(
            document.body.scrollHeight,
            document.body.offsetHeight,
            document.documentElement.clientHeight,
            document.documentElement.scrollHeight,
            document.documentElement.offsetHeight
        );
        const windowHeight = window.innerHeight;
        
        // Calculer le pourcentage de scroll
        this.scrollProgress = Math.min(
            (scrollTop / (documentHeight - windowHeight)) * 100,
            100
        );

        // Mettre à jour l'indicateur de progression
        this.updateProgressIndicator();

        // Gérer la visibilité
        this.updateVisibility();

        // Auto-hide après inactivité
        this.scheduleAutoHide();
    }

    updateProgressIndicator() {
        const progressCircle = this.button.querySelector('.progress-circle');
        if (!progressCircle) return;

        const circumference = 2 * Math.PI * 20; // r = 20
        const offset = circumference - (this.scrollProgress / 100) * circumference;
        
        progressCircle.style.strokeDashoffset = offset;
    }

    updateVisibility() {
        const scrollTop = window.pageYOffset;
        const shouldShow = scrollTop > this.threshold;

        if (shouldShow && !this.isVisible) {
            this.showButton();
        } else if (!shouldShow && this.isVisible) {
            this.hideButton();
        }
    }

    showButton(animate = true) {
        if (this.isVisible) return;

        this.isVisible = true;
        this.button.style.pointerEvents = 'auto';
        
        if (animate) {
            // Animation d'entrée
            this.button.style.transform = 'translateY(0) scale(1)';
            this.button.style.opacity = '1';
            
            // Animation de rebond
            setTimeout(() => {
                this.button.style.transform = 'translateY(0) scale(1.1)';
                setTimeout(() => {
                    this.button.style.transform = 'translateY(0) scale(1)';
                }, 150);
            }, 100);
        } else {
            this.button.style.transform = 'translateY(0) scale(1)';
            this.button.style.opacity = '1';
        }

        // Événement personnalisé
        this.button.dispatchEvent(new CustomEvent('backToTop:show'));
    }

    hideButton(animate = true) {
        if (!this.isVisible) return;

        this.isVisible = false;
        
        if (animate) {
            this.button.style.transform = 'translateY(100%) scale(0.8)';
            this.button.style.opacity = '0';
            
            setTimeout(() => {
                this.button.style.pointerEvents = 'none';
            }, 300);
        } else {
            this.button.style.transform = 'translateY(100%) scale(0.8)';
            this.button.style.opacity = '0';
            this.button.style.pointerEvents = 'none';
        }

        // Événement personnalisé
        this.button.dispatchEvent(new CustomEvent('backToTop:hide'));
    }

    scheduleAutoHide() {
        // Annuler le timeout précédent
        if (this.hideTimeout) {
            clearTimeout(this.hideTimeout);
        }

        // Programmer le masquage automatique après 3 secondes d'inactivité
        this.hideTimeout = setTimeout(() => {
            if (this.isVisible && window.pageYOffset > this.threshold) {
                this.button.style.opacity = '0.6';
            }
        }, 3000);
    }

    onButtonHover() {
        // Annuler l'auto-hide au survol
        if (this.hideTimeout) {
            clearTimeout(this.hideTimeout);
        }
        
        this.button.style.opacity = '1';
        this.button.style.transform = 'translateY(0) scale(1.1)';
    }

    onButtonLeave() {
        this.button.style.transform = 'translateY(0) scale(1)';
        this.scheduleAutoHide();
    }

    scrollToTop() {
        // Utiliser le smooth scroll si disponible
        if (window.LastuceApp?.utils?.smoothScroll) {
            window.LastuceApp.utils.smoothScroll.scrollToTop();
        } else {
            // Fallback avec animation personnalisée
            this.animateScrollToTop();
        }

        // Tracking Analytics si disponible
        if (window.LastuceApp?.components?.integrations) {
            window.LastuceApp.components.integrations.trackEvent('back_to_top', {
                category: 'navigation',
                label: 'button_click',
                value: Math.round(this.scrollProgress),
            });
        }

        // Animation du bouton
        this.animateButtonClick();
    }

    animateScrollToTop() {
        const startPosition = window.pageYOffset;
        const duration = 800;
        const startTime = performance.now();

        const easeInOutCubic = (t) => {
            return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
        };

        const animateScroll = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easedProgress = easeInOutCubic(progress);
            const currentPosition = startPosition * (1 - easedProgress);

            window.scrollTo(0, currentPosition);

            if (progress < 1) {
                requestAnimationFrame(animateScroll);
            }
        };

        requestAnimationFrame(animateScroll);
    }

    animateButtonClick() {
        // Animation de clic
        this.button.style.transform = 'translateY(0) scale(0.9)';
        
        setTimeout(() => {
            this.button.style.transform = 'translateY(0) scale(1)';
        }, 150);

        // Effet de pulsation
        this.button.classList.add('animate-pulse');
        setTimeout(() => {
            this.button.classList.remove('animate-pulse');
        }, 600);
    }

    // Configuration
    setThreshold(threshold) {
        this.threshold = Math.max(0, threshold);
        this.updateVisibility();
    }

    setPosition(position) {
        const positions = {
            'bottom-right': { bottom: '1.5rem', right: '1.5rem', left: 'auto', top: 'auto' },
            'bottom-left': { bottom: '1.5rem', left: '1.5rem', right: 'auto', top: 'auto' },
            'top-right': { top: '1.5rem', right: '1.5rem', left: 'auto', bottom: 'auto' },
            'top-left': { top: '1.5rem', left: '1.5rem', right: 'auto', bottom: 'auto' },
        };

        const pos = positions[position] || positions['bottom-right'];
        Object.assign(this.button.style, pos);
    }

    // Méthodes utilitaires
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // API publique
    show() {
        this.showButton();
    }

    hide() {
        this.hideButton();
    }

    toggle() {
        if (this.isVisible) {
            this.hideButton();
        } else {
            this.showButton();
        }
    }

    isButtonVisible() {
        return this.isVisible;
    }

    getScrollProgress() {
        return this.scrollProgress;
    }

    destroy() {
        // Nettoyer les timeouts
        if (this.hideTimeout) {
            clearTimeout(this.hideTimeout);
        }

        // Supprimer le bouton s'il a été créé automatiquement
        if (this.button && !document.querySelector('[data-back-to-top]')) {
            this.button.remove();
        }
    }
} 