/**
 * Composant de navigation avec menu mobile et animations
 */
export class Navigation {
    constructor() {
        this.mobileMenuButton = document.querySelector('[data-mobile-menu-button]');
        this.mobileMenu = document.querySelector('[data-mobile-menu]');
        this.navLinks = document.querySelectorAll('[data-nav-link]');
        this.isMenuOpen = false;
        this.scrollThreshold = 100;
        this.lastScrollY = 0;
        this.isScrollingDown = false;
        
        this.init();
    }

    init() {
        this.setupMobileMenu();
        this.setupScrollBehavior();
        this.setupActiveLinks();
        this.bindEvents();
    }

    setupMobileMenu() {
        if (!this.mobileMenuButton || !this.mobileMenu) return;

        // Créer l'icône hamburger animée
        this.createHamburgerIcon();
        
        // État initial
        this.mobileMenu.classList.add('transform', 'transition-transform', 'duration-300', 'ease-in-out');
        this.closeMobileMenu();
    }

    createHamburgerIcon() {
        this.mobileMenuButton.innerHTML = `
            <div class="hamburger-icon w-6 h-6 flex flex-col justify-center items-center space-y-1 cursor-pointer">
                <span class="hamburger-line block w-6 h-0.5 bg-current transition-all duration-300 ease-in-out"></span>
                <span class="hamburger-line block w-6 h-0.5 bg-current transition-all duration-300 ease-in-out"></span>
                <span class="hamburger-line block w-6 h-0.5 bg-current transition-all duration-300 ease-in-out"></span>
            </div>
        `;

        this.hamburgerLines = this.mobileMenuButton.querySelectorAll('.hamburger-line');
    }

    setupScrollBehavior() {
        const navbar = document.querySelector('[data-navbar]');
        if (!navbar) return;

        this.navbar = navbar;
        
        // Ajouter les classes de transition
        this.navbar.classList.add('transition-all', 'duration-300', 'ease-in-out');
    }

    setupActiveLinks() {
        // Marquer le lien actif basé sur l'URL actuelle
        const currentPath = window.location.pathname;
        
        this.navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPath || (currentPath === '/' && href === '/')) {
                link.classList.add('active');
            }
        });
    }

    bindEvents() {
        // Menu mobile
        if (this.mobileMenuButton) {
            this.mobileMenuButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleMobileMenu();
            });
        }

        // Fermer le menu en cliquant sur un lien
        this.navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (this.isMenuOpen) {
                    this.closeMobileMenu();
                }
            });
        });

        // Fermer le menu en cliquant à l'extérieur
        document.addEventListener('click', (e) => {
            if (this.isMenuOpen && 
                !this.mobileMenu.contains(e.target) && 
                !this.mobileMenuButton.contains(e.target)) {
                this.closeMobileMenu();
            }
        });

        // Gestion du scroll
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

        // Fermer le menu mobile sur redimensionnement
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768 && this.isMenuOpen) {
                this.closeMobileMenu();
            }
        });

        // Gestion des touches clavier
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isMenuOpen) {
                this.closeMobileMenu();
            }
        });
    }

    toggleMobileMenu() {
        if (this.isMenuOpen) {
            this.closeMobileMenu();
        } else {
            this.openMobileMenu();
        }
    }

    openMobileMenu() {
        this.isMenuOpen = true;
        
        // Animer l'icône hamburger
        this.animateHamburgerToX();
        
        // Afficher le menu
        this.mobileMenu.classList.remove('-translate-x-full', 'translate-x-full');
        this.mobileMenu.classList.add('translate-x-0');
        
        // Empêcher le scroll du body
        document.body.style.overflow = 'hidden';
        
        // Ajouter l'overlay
        this.createOverlay();
        
        // Animation des liens du menu
        this.animateMenuLinks(true);

        // Événement personnalisé
        document.dispatchEvent(new CustomEvent('navigation:menuOpen'));
    }

    closeMobileMenu() {
        this.isMenuOpen = false;
        
        // Animer l'icône hamburger
        this.animateHamburgerToNormal();
        
        // Masquer le menu
        this.mobileMenu.classList.remove('translate-x-0');
        this.mobileMenu.classList.add('-translate-x-full');
        
        // Restaurer le scroll du body
        document.body.style.overflow = '';
        
        // Supprimer l'overlay
        this.removeOverlay();
        
        // Animation des liens du menu
        this.animateMenuLinks(false);

        // Événement personnalisé
        document.dispatchEvent(new CustomEvent('navigation:menuClose'));
    }

    animateHamburgerToX() {
        if (!this.hamburgerLines) return;
        
        const [line1, line2, line3] = this.hamburgerLines;
        
        // Première ligne: rotation et translation
        line1.style.transform = 'rotate(45deg) translate(6px, 6px)';
        
        // Deuxième ligne: disparition
        line2.style.opacity = '0';
        line2.style.transform = 'scale(0)';
        
        // Troisième ligne: rotation et translation
        line3.style.transform = 'rotate(-45deg) translate(6px, -6px)';
    }

    animateHamburgerToNormal() {
        if (!this.hamburgerLines) return;
        
        const [line1, line2, line3] = this.hamburgerLines;
        
        // Réinitialiser toutes les transformations
        line1.style.transform = '';
        line2.style.opacity = '';
        line2.style.transform = '';
        line3.style.transform = '';
    }

    createOverlay() {
        if (document.querySelector('.mobile-menu-overlay')) return;
        
        const overlay = document.createElement('div');
        overlay.className = 'mobile-menu-overlay fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300';
        overlay.style.opacity = '0';
        
        document.body.appendChild(overlay);
        
        // Animation d'apparition
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 10);
        
        // Fermer le menu en cliquant sur l'overlay
        overlay.addEventListener('click', () => {
            this.closeMobileMenu();
        });
    }

    removeOverlay() {
        const overlay = document.querySelector('.mobile-menu-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.remove();
            }, 300);
        }
    }

    animateMenuLinks(show) {
        const links = this.mobileMenu.querySelectorAll('a, button');
        
        links.forEach((link, index) => {
            if (show) {
                link.style.opacity = '0';
                link.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    link.style.transition = 'all 0.3s ease-in-out';
                    link.style.opacity = '1';
                    link.style.transform = 'translateY(0)';
                }, index * 50);
            } else {
                link.style.transition = '';
                link.style.opacity = '';
                link.style.transform = '';
            }
        });
    }

    handleScroll() {
        const currentScrollY = window.scrollY;
        
        // Déterminer la direction du scroll
        this.isScrollingDown = currentScrollY > this.lastScrollY;
        this.lastScrollY = currentScrollY;
        
        // Gestion de la navbar
        if (this.navbar) {
            this.updateNavbarOnScroll(currentScrollY);
        }
        
        // Mettre à jour les liens actifs pour les sections
        this.updateActiveSection();
    }

    updateNavbarOnScroll(scrollY) {
        if (scrollY > this.scrollThreshold) {
            // Navbar scrollée
            this.navbar.classList.add('navbar-scrolled');
            
            if (this.isScrollingDown) {
                // Masquer la navbar en scrollant vers le bas
                this.navbar.style.transform = 'translateY(-100%)';
            } else {
                // Afficher la navbar en scrollant vers le haut
                this.navbar.style.transform = 'translateY(0)';
            }
        } else {
            // Navbar en haut de page
            this.navbar.classList.remove('navbar-scrolled');
            this.navbar.style.transform = 'translateY(0)';
        }
    }

    updateActiveSection() {
        const sections = document.querySelectorAll('section[id]');
        const scrollPosition = window.scrollY + 100; // Offset pour l'activation
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                // Mettre à jour les liens de navigation
                this.navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${sectionId}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }

    // Méthodes pour le smooth scrolling
    scrollToSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (!section) return;
        
        const offsetTop = section.offsetTop - (this.navbar ? this.navbar.offsetHeight : 0);
        
        window.scrollTo({
            top: offsetTop,
            behavior: 'smooth'
        });
    }

    // API publique
    openMenu() {
        if (!this.isMenuOpen) {
            this.openMobileMenu();
        }
    }

    closeMenu() {
        if (this.isMenuOpen) {
            this.closeMobileMenu();
        }
    }

    isOpen() {
        return this.isMenuOpen;
    }

    setActiveLink(href) {
        this.navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === href) {
                link.classList.add('active');
            }
        });
    }

    destroy() {
        // Nettoyer les event listeners
        document.body.style.overflow = '';
        this.removeOverlay();
        
        if (this.isMenuOpen) {
            this.closeMobileMenu();
        }
    }
} 