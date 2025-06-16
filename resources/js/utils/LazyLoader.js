/**
 * LazyLoader - Chargement paresseux des images et contenus
 */
export class LazyLoader {
    constructor() {
        this.observer = null;
        this.init();
    }

    init() {
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver(
                this.handleIntersection.bind(this),
                {
                    rootMargin: '50px 0px',
                    threshold: 0.01
                }
            );
            
            this.observeElements();
        } else {
            // Fallback pour les navigateurs plus anciens
            this.loadAllImages();
        }
    }

    observeElements() {
        const lazyImages = document.querySelectorAll('img[data-src], [data-lazy]');
        lazyImages.forEach(img => {
            this.observer.observe(img);
        });
    }

    handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                this.loadElement(entry.target);
                this.observer.unobserve(entry.target);
            }
        });
    }

    loadElement(element) {
        if (element.tagName === 'IMG') {
            this.loadImage(element);
        } else {
            this.loadContent(element);
        }
    }

    loadImage(img) {
        const src = img.dataset.src;
        if (src) {
            img.src = src;
            img.classList.add('lazy-loaded');
            img.removeAttribute('data-src');
        }
    }

    loadContent(element) {
        const content = element.dataset.lazy;
        if (content) {
            element.innerHTML = content;
            element.classList.add('lazy-loaded');
            element.removeAttribute('data-lazy');
        }
    }

    loadAllImages() {
        const lazyImages = document.querySelectorAll('img[data-src], [data-lazy]');
        lazyImages.forEach(element => {
            this.loadElement(element);
        });
    }

    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
} 