import { Swiper } from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

/**
 * Composants dynamiques : carrousels, modals, filtres, recherche
 */
export class DynamicComponents {
    constructor() {
        this.carousels = [];
        this.modals = [];
        this.filters = [];
        this.searchInstances = [];
        
        this.init();
    }

    init() {
        this.initCarousels();
        this.initModals();
        this.initFilters();
        this.initSearch();
    }

    // === CARROUSELS ===
    initCarousels() {
        const carouselElements = document.querySelectorAll('[data-carousel]');
        carouselElements.forEach(element => {
            this.createCarousel(element);
        });
    }

    createCarousel(element) {
        const config = this.parseCarouselConfig(element);
        
        const swiper = new Swiper(element, {
            modules: [Navigation, Pagination, Autoplay, EffectFade],
            ...config,
            on: {
                init: () => {
                    element.dispatchEvent(new CustomEvent('carousel:init'));
                },
                slideChange: (swiper) => {
                    element.dispatchEvent(new CustomEvent('carousel:slideChange', {
                        detail: { activeIndex: swiper.activeIndex }
                    }));
                },
            },
        });

        this.carousels.push({ element, swiper, config });
        return swiper;
    }

    parseCarouselConfig(element) {
        const dataset = element.dataset;
        
        const config = {
            slidesPerView: parseInt(dataset.slidesPerView) || 1,
            spaceBetween: parseInt(dataset.spaceBetween) || 20,
            loop: dataset.loop !== 'false',
            autoplay: dataset.autoplay === 'true' ? {
                delay: parseInt(dataset.autoplayDelay) || 3000,
                disableOnInteraction: false,
            } : false,
            speed: parseInt(dataset.speed) || 300,
            effect: dataset.effect || 'slide',
        };

        // Navigation
        if (dataset.navigation === 'true') {
            config.navigation = {
                nextEl: element.querySelector('.swiper-button-next'),
                prevEl: element.querySelector('.swiper-button-prev'),
            };
        }

        // Pagination
        if (dataset.pagination === 'true') {
            config.pagination = {
                el: element.querySelector('.swiper-pagination'),
                clickable: true,
                dynamicBullets: dataset.dynamicBullets === 'true',
            };
        }

        // Responsive breakpoints
        if (dataset.breakpoints) {
            try {
                config.breakpoints = JSON.parse(dataset.breakpoints);
            } catch (e) {
                console.warn('Configuration breakpoints invalide:', dataset.breakpoints);
            }
        } else {
            // Configuration responsive par défaut
            config.breakpoints = {
                640: {
                    slidesPerView: Math.min(2, config.slidesPerView),
                },
                768: {
                    slidesPerView: Math.min(3, config.slidesPerView),
                },
                1024: {
                    slidesPerView: config.slidesPerView,
                },
            };
        }

        return config;
    }

    // === MODALS ===
    initModals() {
        const modalTriggers = document.querySelectorAll('[data-modal-trigger]');
        modalTriggers.forEach(trigger => {
            this.bindModalTrigger(trigger);
        });

        // Modal de partage spécifique
        this.initShareModal();
    }

    bindModalTrigger(trigger) {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = trigger.dataset.modalTrigger;
            this.openModal(modalId);
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Créer l'overlay s'il n'existe pas
        if (!modal.querySelector('.modal-overlay')) {
            this.createModalStructure(modal);
        }

        // Afficher le modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Animation d'entrée
        setTimeout(() => {
            modal.classList.add('modal-open');
        }, 10);

        // Empêcher le scroll du body
        document.body.style.overflow = 'hidden';

        // Bind events
        this.bindModalEvents(modal);

        // Événement personnalisé
        modal.dispatchEvent(new CustomEvent('modal:open'));
    }

    createModalStructure(modal) {
        const content = modal.innerHTML;
        modal.innerHTML = `
            <div class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
                <div class="modal-content bg-white rounded-lg shadow-xl max-w-lg w-full max-h-screen overflow-y-auto transform scale-95 transition-transform duration-300">
                    <div class="modal-header flex items-center justify-between p-6 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">
                            ${modal.dataset.modalTitle || 'Modal'}
                        </h3>
                        <button class="modal-close text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body p-6">
                        ${content}
                    </div>
                </div>
            </div>
        `;
    }

    bindModalEvents(modal) {
        const overlay = modal.querySelector('.modal-overlay');
        const closeBtn = modal.querySelector('.modal-close');

        // Fermer en cliquant sur l'overlay
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                this.closeModal(modal);
            }
        });

        // Fermer avec le bouton
        closeBtn.addEventListener('click', () => {
            this.closeModal(modal);
        });

        // Fermer avec Escape
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                this.closeModal(modal);
                document.removeEventListener('keydown', handleEscape);
            }
        };
        document.addEventListener('keydown', handleEscape);
    }

    closeModal(modal) {
        const overlay = modal.querySelector('.modal-overlay');
        
        // Animation de sortie
        overlay.classList.remove('opacity-100');
        overlay.classList.add('opacity-0');
        
        const content = modal.querySelector('.modal-content');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex', 'modal-open');
            document.body.style.overflow = '';
        }, 300);

        // Événement personnalisé
        modal.dispatchEvent(new CustomEvent('modal:close'));
    }

    initShareModal() {
        const shareButtons = document.querySelectorAll('[data-share]');
        shareButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.openShareModal(button);
            });
        });
    }

    openShareModal(trigger) {
        const url = trigger.dataset.shareUrl || window.location.href;
        const title = trigger.dataset.shareTitle || document.title;
        const description = trigger.dataset.shareDescription || '';

        const shareModal = this.createShareModalContent(url, title, description);
        document.body.appendChild(shareModal);
        
        setTimeout(() => {
            this.openModal(shareModal.id);
        }, 10);
    }

    createShareModalContent(url, title, description) {
        const modalId = `share-modal-${Date.now()}`;
        const encodedUrl = encodeURIComponent(url);
        const encodedTitle = encodeURIComponent(title);
        const encodedDescription = encodeURIComponent(description);

        const modal = document.createElement('div');
        modal.id = modalId;
        modal.className = 'modal hidden fixed inset-0 z-50';
        modal.dataset.modalTitle = 'Partager';

        modal.innerHTML = `
            <div class="share-content">
                <p class="text-gray-600 mb-6">Partagez ce contenu sur vos réseaux sociaux</p>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}" 
                       target="_blank" 
                       class="share-button bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>Facebook</span>
                    </a>
                    
                    <a href="https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}" 
                       target="_blank"
                       class="share-button bg-blue-400 hover:bg-blue-500 text-white p-3 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                        <span>Twitter</span>
                    </a>
                    
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}" 
                       target="_blank"
                       class="share-button bg-blue-700 hover:bg-blue-800 text-white p-3 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                        <span>LinkedIn</span>
                    </a>
                    
                    <button class="share-button copy-link bg-gray-600 hover:bg-gray-700 text-white p-3 rounded-lg flex items-center justify-center space-x-2 transition-colors"
                            data-url="${url}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span>Copier le lien</span>
                    </button>
                </div>
                
                <div class="border-t pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lien à partager :</label>
                    <div class="flex">
                        <input type="text" value="${url}" readonly 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50 text-sm">
                        <button class="copy-url-btn px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-r-md transition-colors">
                            Copier
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Bind copy functionality
        const copyButtons = modal.querySelectorAll('.copy-link, .copy-url-btn');
        copyButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                navigator.clipboard.writeText(url).then(() => {
                    this.showToast('Lien copié dans le presse-papiers !');
                });
            });
        });

        return modal;
    }

    // === FILTRES ===
    initFilters() {
        const filterContainers = document.querySelectorAll('[data-filter-container]');
        filterContainers.forEach(container => {
            this.createFilter(container);
        });
    }

    createFilter(container) {
        const targetSelector = container.dataset.filterTarget;
        const filterType = container.dataset.filterType || 'category';
        
        const filter = {
            container,
            targetSelector,
            filterType,
            activeFilters: new Set(),
            items: document.querySelectorAll(targetSelector),
        };

        this.bindFilterEvents(filter);
        this.filters.push(filter);
    }

    bindFilterEvents(filter) {
        const buttons = filter.container.querySelectorAll('[data-filter]');
        
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const filterValue = button.dataset.filter;
                
                if (filterValue === 'all') {
                    filter.activeFilters.clear();
                    buttons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                } else {
                    // Toggle filter
                    if (filter.activeFilters.has(filterValue)) {
                        filter.activeFilters.delete(filterValue);
                        button.classList.remove('active');
                    } else {
                        filter.activeFilters.add(filterValue);
                        button.classList.add('active');
                    }
                    
                    // Remove 'all' button active state
                    const allButton = filter.container.querySelector('[data-filter="all"]');
                    if (allButton) allButton.classList.remove('active');
                }
                
                this.applyFilter(filter);
            });
        });
    }

    applyFilter(filter) {
        filter.items.forEach(item => {
            const shouldShow = this.shouldShowItem(item, filter);
            
            if (shouldShow) {
                item.style.display = '';
                item.classList.remove('filtered-out');
                item.classList.add('filtered-in');
            } else {
                item.style.display = 'none';
                item.classList.add('filtered-out');
                item.classList.remove('filtered-in');
            }
        });

        // Événement personnalisé
        filter.container.dispatchEvent(new CustomEvent('filter:applied', {
            detail: { activeFilters: Array.from(filter.activeFilters) }
        }));
    }

    shouldShowItem(item, filter) {
        if (filter.activeFilters.size === 0) return true;
        
        const itemCategories = item.dataset.filterCategory?.split(',') || [];
        const itemTags = item.dataset.filterTags?.split(',') || [];
        
        return Array.from(filter.activeFilters).some(filterValue => {
            return itemCategories.includes(filterValue) || itemTags.includes(filterValue);
        });
    }

    // === RECHERCHE INSTANTANÉE ===
    initSearch() {
        const searchInputs = document.querySelectorAll('[data-search]');
        searchInputs.forEach(input => {
            this.createSearch(input);
        });
    }

    createSearch(input) {
        const targetSelector = input.dataset.searchTarget;
        const searchFields = input.dataset.searchFields?.split(',') || ['title', 'content'];
        
        const search = {
            input,
            targetSelector,
            searchFields,
            items: document.querySelectorAll(targetSelector),
            debounceTimer: null,
        };

        this.bindSearchEvents(search);
        this.searchInstances.push(search);
    }

    bindSearchEvents(search) {
        search.input.addEventListener('input', (e) => {
            clearTimeout(search.debounceTimer);
            search.debounceTimer = setTimeout(() => {
                this.performSearch(search, e.target.value);
            }, 300);
        });

        // Clear search
        const clearButton = search.input.parentElement.querySelector('[data-search-clear]');
        if (clearButton) {
            clearButton.addEventListener('click', () => {
                search.input.value = '';
                this.performSearch(search, '');
            });
        }
    }

    performSearch(search, query) {
        const normalizedQuery = query.toLowerCase().trim();
        
        if (normalizedQuery === '') {
            // Afficher tous les éléments
            search.items.forEach(item => {
                item.style.display = '';
                item.classList.remove('search-hidden');
            });
        } else {
            search.items.forEach(item => {
                const shouldShow = this.matchesSearch(item, normalizedQuery, search.searchFields);
                
                if (shouldShow) {
                    item.style.display = '';
                    item.classList.remove('search-hidden');
                } else {
                    item.style.display = 'none';
                    item.classList.add('search-hidden');
                }
            });
        }

        // Événement personnalisé
        search.input.dispatchEvent(new CustomEvent('search:performed', {
            detail: { query, resultsCount: this.getVisibleItemsCount(search.items) }
        }));
    }

    matchesSearch(item, query, searchFields) {
        return searchFields.some(field => {
            let content = '';
            
            switch (field) {
                case 'title':
                    content = item.querySelector('h1, h2, h3, h4, h5, h6, .title')?.textContent || '';
                    break;
                case 'content':
                    content = item.textContent || '';
                    break;
                case 'description':
                    content = item.querySelector('.description, .excerpt')?.textContent || '';
                    break;
                default:
                    content = item.dataset[field] || '';
            }
            
            return content.toLowerCase().includes(query);
        });
    }

    getVisibleItemsCount(items) {
        return Array.from(items).filter(item => item.style.display !== 'none').length;
    }

    // === UTILITAIRES ===
    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast fixed bottom-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform translate-y-full transition-transform duration-300`;
        
        const bgColor = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500',
        }[type] || 'bg-blue-500';
        
        toast.classList.add(bgColor);
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.remove('translate-y-full');
        }, 100);
        
        setTimeout(() => {
            toast.classList.add('translate-y-full');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // === API PUBLIQUE ===
    getCarousel(element) {
        const carousel = this.carousels.find(c => c.element === element);
        return carousel ? carousel.swiper : null;
    }

    openModalById(modalId) {
        this.openModal(modalId);
    }

    closeAllModals() {
        const openModals = document.querySelectorAll('.modal:not(.hidden)');
        openModals.forEach(modal => this.closeModal(modal));
    }

    clearAllFilters() {
        this.filters.forEach(filter => {
            filter.activeFilters.clear();
            this.applyFilter(filter);
        });
    }

    clearAllSearches() {
        this.searchInstances.forEach(search => {
            search.input.value = '';
            this.performSearch(search, '');
        });
    }

    destroy() {
        // Nettoyer les carrousels
        this.carousels.forEach(carousel => {
            if (carousel.swiper) {
                carousel.swiper.destroy();
            }
        });

        // Fermer les modals
        this.closeAllModals();
        
        // Nettoyer les timers
        this.searchInstances.forEach(search => {
            if (search.debounceTimer) {
                clearTimeout(search.debounceTimer);
            }
        });
    }
}

// Export par défaut
export default DynamicComponents; 