/**
 * Gestionnaire de smooth scrolling pour la navigation
 */
export class SmoothScroll {
    constructor() {
        this.isScrolling = false;
        this.scrollDuration = 800;
        this.easing = 'easeInOutCubic';
        this.offset = 80; // Offset pour la navbar fixe
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupScrollBehavior();
    }

    bindEvents() {
        // Liens d'ancrage
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                this.handleAnchorClick(e, link);
            });
        });

        // Boutons de scroll personnalisés
        const scrollButtons = document.querySelectorAll('[data-scroll-to]');
        scrollButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const target = button.dataset.scrollTo;
                this.scrollToElement(target);
            });
        });
    }

    setupScrollBehavior() {
        // Désactiver le smooth scroll natif pour avoir un contrôle total
        document.documentElement.style.scrollBehavior = 'auto';
    }

    handleAnchorClick(event, link) {
        const href = link.getAttribute('href');
        
        // Ignorer les liens externes ou non-anchor
        if (!href || !href.startsWith('#') || href === '#') {
            return;
        }

        event.preventDefault();
        
        const targetId = href.substring(1);
        this.scrollToElement(targetId);
        
        // Mettre à jour l'URL sans déclencher de scroll
        if (history.pushState) {
            history.pushState(null, null, href);
        }
    }

    scrollToElement(targetId) {
        const target = document.getElementById(targetId);
        if (!target) {
            console.warn(`Élément avec l'ID "${targetId}" non trouvé`);
            return;
        }

        const targetPosition = this.getTargetPosition(target);
        this.scrollToPosition(targetPosition);
    }

    getTargetPosition(element) {
        const elementTop = element.getBoundingClientRect().top + window.pageYOffset;
        return Math.max(0, elementTop - this.offset);
    }

    scrollToPosition(targetPosition) {
        if (this.isScrolling) return;

        const startPosition = window.pageYOffset;
        const distance = targetPosition - startPosition;
        
        if (Math.abs(distance) < 5) return; // Déjà à la position

        this.isScrolling = true;
        const startTime = performance.now();

        const animateScroll = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / this.scrollDuration, 1);
            
            const easedProgress = this.applyEasing(progress);
            const currentPosition = startPosition + (distance * easedProgress);
            
            window.scrollTo(0, currentPosition);
            
            if (progress < 1) {
                requestAnimationFrame(animateScroll);
            } else {
                this.isScrolling = false;
                this.onScrollComplete(targetPosition);
            }
        };

        requestAnimationFrame(animateScroll);
    }

    applyEasing(t) {
        switch (this.easing) {
            case 'linear':
                return t;
            case 'easeInQuad':
                return t * t;
            case 'easeOutQuad':
                return t * (2 - t);
            case 'easeInOutQuad':
                return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
            case 'easeInCubic':
                return t * t * t;
            case 'easeOutCubic':
                return (--t) * t * t + 1;
            case 'easeInOutCubic':
                return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
            case 'easeInQuart':
                return t * t * t * t;
            case 'easeOutQuart':
                return 1 - (--t) * t * t * t;
            case 'easeInOutQuart':
                return t < 0.5 ? 8 * t * t * t * t : 1 - 8 * (--t) * t * t * t;
            default:
                return this.applyEasing('easeInOutCubic');
        }
    }

    onScrollComplete(position) {
        // Événement personnalisé
        document.dispatchEvent(new CustomEvent('smoothScroll:complete', {
            detail: { position }
        }));

        // Tracking Analytics si disponible
        if (window.LastuceApp?.components?.integrations) {
            window.LastuceApp.components.integrations.trackEvent('smooth_scroll', {
                category: 'navigation',
                label: 'scroll_complete',
            });
        }
    }

    // Méthodes de scroll spécialisées
    scrollToTop() {
        this.scrollToPosition(0);
    }

    scrollToBottom() {
        const documentHeight = Math.max(
            document.body.scrollHeight,
            document.body.offsetHeight,
            document.documentElement.clientHeight,
            document.documentElement.scrollHeight,
            document.documentElement.offsetHeight
        );
        this.scrollToPosition(documentHeight - window.innerHeight);
    }

    scrollToSection(sectionName) {
        const section = document.querySelector(`[data-section="${sectionName}"]`) ||
                      document.querySelector(`section.${sectionName}`) ||
                      document.getElementById(sectionName);
        
        if (section) {
            const position = this.getTargetPosition(section);
            this.scrollToPosition(position);
        }
    }

    // Scroll avec offset personnalisé
    scrollToElementWithOffset(targetId, customOffset) {
        const target = document.getElementById(targetId);
        if (!target) return;

        const elementTop = target.getBoundingClientRect().top + window.pageYOffset;
        const targetPosition = Math.max(0, elementTop - customOffset);
        this.scrollToPosition(targetPosition);
    }

    // Scroll progressif pour révéler du contenu
    revealElement(element, options = {}) {
        const {
            offset = this.offset,
            duration = this.scrollDuration,
            callback = null
        } = options;

        const elementRect = element.getBoundingClientRect();
        const elementTop = elementRect.top + window.pageYOffset;
        const elementBottom = elementTop + elementRect.height;
        const viewportTop = window.pageYOffset;
        const viewportBottom = viewportTop + window.innerHeight;

        // Vérifier si l'élément est déjà visible
        if (elementTop >= viewportTop + offset && elementBottom <= viewportBottom - offset) {
            if (callback) callback();
            return;
        }

        // Calculer la position optimale
        let targetPosition;
        if (elementRect.height > window.innerHeight - (offset * 2)) {
            // Élément plus grand que la viewport : aligner le haut
            targetPosition = elementTop - offset;
        } else {
            // Centrer l'élément dans la viewport
            targetPosition = elementTop - (window.innerHeight - elementRect.height) / 2;
        }

        targetPosition = Math.max(0, targetPosition);

        // Sauvegarder la durée actuelle et la restaurer après
        const originalDuration = this.scrollDuration;
        this.scrollDuration = duration;

        this.scrollToPosition(targetPosition);

        // Restaurer la durée et exécuter le callback
        setTimeout(() => {
            this.scrollDuration = originalDuration;
            if (callback) callback();
        }, duration);
    }

    // Parallax scroll pour les éléments
    initParallaxElements() {
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        if (parallaxElements.length === 0) return;

        let ticking = false;

        const updateParallax = () => {
            const scrollTop = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const speed = parseFloat(element.dataset.parallax) || 0.5;
                const yPos = -(scrollTop * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
            
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        });
    }

    // Scroll spy pour mettre à jour la navigation
    initScrollSpy() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('nav a[href^="#"]');
        
        if (sections.length === 0 || navLinks.length === 0) return;

        let ticking = false;

        const updateActiveSection = () => {
            const scrollPosition = window.pageYOffset + this.offset + 50;
            
            let activeSection = null;
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    activeSection = section;
                }
            });

            // Mettre à jour les liens de navigation
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (activeSection && link.getAttribute('href') === `#${activeSection.id}`) {
                    link.classList.add('active');
                }
            });
            
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateActiveSection);
                ticking = true;
            }
        });

        // Initialiser l'état actif
        updateActiveSection();
    }

    // Configuration
    setDuration(duration) {
        this.scrollDuration = Math.max(100, duration);
    }

    setEasing(easing) {
        this.easing = easing;
    }

    setOffset(offset) {
        this.offset = offset;
    }

    // Utilitaires
    isElementInViewport(element, threshold = 0) {
        const rect = element.getBoundingClientRect();
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;
        
        return (
            rect.top >= -threshold &&
            rect.bottom <= windowHeight + threshold
        );
    }

    getScrollProgress() {
        const scrollTop = window.pageYOffset;
        const documentHeight = document.documentElement.scrollHeight - window.innerHeight;
        return documentHeight > 0 ? (scrollTop / documentHeight) * 100 : 0;
    }

    // API publique
    scrollTo(target, options = {}) {
        if (typeof target === 'string') {
            this.scrollToElement(target);
        } else if (typeof target === 'number') {
            this.scrollToPosition(target);
        } else if (target instanceof Element) {
            const position = this.getTargetPosition(target);
            this.scrollToPosition(position);
        }
    }

    stop() {
        this.isScrolling = false;
    }

    destroy() {
        this.stop();
        document.documentElement.style.scrollBehavior = '';
    }
} 