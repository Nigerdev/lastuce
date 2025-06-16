/**
 * Intégrations : Newsletter, Réseaux sociaux, Google Analytics
 */
export class Integrations {
    constructor() {
        this.newsletterForms = [];
        this.socialShares = [];
        this.analyticsInitialized = false;
        
        this.init();
    }

    init() {
        this.initGoogleAnalytics();
        this.initNewsletterSignup();
        this.initSocialSharing();
        this.initScrollTracking();
    }

    // === GOOGLE ANALYTICS ===
    initGoogleAnalytics() {
        const gaId = window.LastuceApp?.config?.googleAnalyticsId;
        if (!gaId) return;

        try {
            // Charger gtag
            const script1 = document.createElement('script');
            script1.async = true;
            script1.src = `https://www.googletagmanager.com/gtag/js?id=${gaId}`;
            document.head.appendChild(script1);

            // Initialiser gtag
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            window.gtag = gtag;
            
            gtag('js', new Date());
            gtag('config', gaId, {
                page_title: document.title,
                page_location: window.location.href,
            });

            this.analyticsInitialized = true;
            console.log('Google Analytics initialisé:', gaId);
        } catch (error) {
            console.error('Erreur lors de l\'initialisation de Google Analytics:', error);
        }
    }

    trackEvent(eventName, parameters = {}) {
        if (!this.analyticsInitialized || !window.gtag) return;

        try {
            window.gtag('event', eventName, {
                event_category: parameters.category || 'engagement',
                event_label: parameters.label || '',
                value: parameters.value || 0,
                ...parameters,
            });
        } catch (error) {
            console.error('Erreur lors du tracking d\'événement:', error);
        }
    }

    trackPageView(page_title, page_location) {
        if (!this.analyticsInitialized || !window.gtag) return;

        try {
            window.gtag('config', window.LastuceApp.config.googleAnalyticsId, {
                page_title,
                page_location,
            });
        } catch (error) {
            console.error('Erreur lors du tracking de page:', error);
        }
    }

    // === NEWSLETTER SIGNUP ===
    initNewsletterSignup() {
        const newsletterForms = document.querySelectorAll('[data-newsletter-form]');
        newsletterForms.forEach(form => {
            this.setupNewsletterForm(form);
        });
    }

    setupNewsletterForm(form) {
        const emailInput = form.querySelector('input[type="email"]');
        const submitButton = form.querySelector('button[type="submit"]');
        const messageContainer = this.createMessageContainer(form);

        const newsletter = {
            form,
            emailInput,
            submitButton,
            messageContainer,
            isSubmitting: false,
        };

        this.bindNewsletterEvents(newsletter);
        this.newsletterForms.push(newsletter);
    }

    createMessageContainer(form) {
        let container = form.querySelector('.newsletter-message');
        if (!container) {
            container = document.createElement('div');
            container.className = 'newsletter-message mt-3 text-sm';
            form.appendChild(container);
        }
        return container;
    }

    bindNewsletterEvents(newsletter) {
        const { form, emailInput, submitButton } = newsletter;

        // Validation en temps réel
        emailInput.addEventListener('input', () => {
            this.validateNewsletterEmail(newsletter);
        });

        emailInput.addEventListener('blur', () => {
            this.validateNewsletterEmail(newsletter);
        });

        // Soumission du formulaire
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleNewsletterSubmit(newsletter);
        });

        // Animation du bouton au focus
        emailInput.addEventListener('focus', () => {
            submitButton.classList.add('scale-105');
        });

        emailInput.addEventListener('blur', () => {
            submitButton.classList.remove('scale-105');
        });
    }

    validateNewsletterEmail(newsletter) {
        const { emailInput, messageContainer } = newsletter;
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email === '') {
            this.clearNewsletterMessage(newsletter);
            emailInput.classList.remove('border-red-500', 'border-green-500');
            return true;
        }

        if (emailRegex.test(email)) {
            emailInput.classList.remove('border-red-500');
            emailInput.classList.add('border-green-500');
            this.clearNewsletterMessage(newsletter);
            return true;
        } else {
            emailInput.classList.remove('border-green-500');
            emailInput.classList.add('border-red-500');
            this.showNewsletterMessage(newsletter, 'Veuillez saisir une adresse email valide', 'error');
            return false;
        }
    }

    async handleNewsletterSubmit(newsletter) {
        const { form, emailInput, submitButton } = newsletter;

        if (newsletter.isSubmitting) return;

        // Valider l'email
        if (!this.validateNewsletterEmail(newsletter)) {
            return;
        }

        const email = emailInput.value.trim();
        if (!email) {
            this.showNewsletterMessage(newsletter, 'Veuillez saisir votre adresse email', 'error');
            return;
        }

        newsletter.isSubmitting = true;
        this.setNewsletterLoading(newsletter, true);

        try {
            const formData = new FormData();
            formData.append('email', email);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

            const response = await fetch('/newsletter/subscribe', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const result = await response.json();

            if (response.ok) {
                this.handleNewsletterSuccess(newsletter, result);
            } else {
                this.handleNewsletterError(newsletter, result);
            }
        } catch (error) {
            console.error('Erreur lors de l\'inscription à la newsletter:', error);
            this.showNewsletterMessage(newsletter, 'Une erreur est survenue. Veuillez réessayer.', 'error');
        } finally {
            newsletter.isSubmitting = false;
            this.setNewsletterLoading(newsletter, false);
        }
    }

    handleNewsletterSuccess(newsletter, result) {
        const { emailInput } = newsletter;
        
        this.showNewsletterMessage(newsletter, result.message || 'Inscription réussie ! Merci de votre confiance.', 'success');
        emailInput.value = '';
        emailInput.classList.remove('border-green-500');

        // Tracking Analytics
        this.trackEvent('newsletter_signup', {
            category: 'newsletter',
            label: 'success',
        });

        // Animation de succès
        this.animateNewsletterSuccess(newsletter);

        // Événement personnalisé
        newsletter.form.dispatchEvent(new CustomEvent('newsletter:success', {
            detail: result
        }));
    }

    handleNewsletterError(newsletter, result) {
        const message = result.message || 'Une erreur est survenue lors de l\'inscription.';
        this.showNewsletterMessage(newsletter, message, 'error');

        // Tracking Analytics
        this.trackEvent('newsletter_signup', {
            category: 'newsletter',
            label: 'error',
        });

        // Événement personnalisé
        newsletter.form.dispatchEvent(new CustomEvent('newsletter:error', {
            detail: result
        }));
    }

    setNewsletterLoading(newsletter, isLoading) {
        const { submitButton } = newsletter;
        
        if (isLoading) {
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Inscription...
            `;
        } else {
            submitButton.disabled = false;
            submitButton.innerHTML = submitButton.dataset.originalText || 'S\'inscrire';
        }
    }

    showNewsletterMessage(newsletter, message, type) {
        const { messageContainer } = newsletter;
        
        const typeClasses = {
            success: 'text-green-600 bg-green-50 border-green-200',
            error: 'text-red-600 bg-red-50 border-red-200',
            info: 'text-blue-600 bg-blue-50 border-blue-200',
        };

        messageContainer.className = `newsletter-message mt-3 text-sm p-3 rounded-lg border ${typeClasses[type] || typeClasses.info}`;
        messageContainer.textContent = message;
        messageContainer.style.display = 'block';
    }

    clearNewsletterMessage(newsletter) {
        const { messageContainer } = newsletter;
        messageContainer.style.display = 'none';
        messageContainer.textContent = '';
    }

    animateNewsletterSuccess(newsletter) {
        const { form } = newsletter;
        
        // Animation de succès
        form.classList.add('animate-pulse');
        setTimeout(() => {
            form.classList.remove('animate-pulse');
        }, 1000);
    }

    // === PARTAGE RÉSEAUX SOCIAUX ===
    initSocialSharing() {
        const shareButtons = document.querySelectorAll('[data-social-share]');
        shareButtons.forEach(button => {
            this.setupSocialShare(button);
        });

        // Boutons de partage flottants
        this.initFloatingSocialShare();
    }

    setupSocialShare(button) {
        const platform = button.dataset.socialShare;
        const url = button.dataset.shareUrl || window.location.href;
        const title = button.dataset.shareTitle || document.title;
        const description = button.dataset.shareDescription || '';

        button.addEventListener('click', (e) => {
            e.preventDefault();
            this.shareOnPlatform(platform, url, title, description);
        });

        this.socialShares.push({ button, platform, url, title, description });
    }

    shareOnPlatform(platform, url, title, description) {
        const encodedUrl = encodeURIComponent(url);
        const encodedTitle = encodeURIComponent(title);
        const encodedDescription = encodeURIComponent(description);

        const shareUrls = {
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
            twitter: `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`,
            linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`,
            whatsapp: `https://wa.me/?text=${encodedTitle}%20${encodedUrl}`,
            telegram: `https://t.me/share/url?url=${encodedUrl}&text=${encodedTitle}`,
            email: `mailto:?subject=${encodedTitle}&body=${encodedDescription}%0A%0A${encodedUrl}`,
        };

        const shareUrl = shareUrls[platform];
        if (!shareUrl) {
            console.warn('Plateforme de partage non supportée:', platform);
            return;
        }

        // Ouvrir la fenêtre de partage
        if (platform === 'email') {
            window.location.href = shareUrl;
        } else {
            const popup = window.open(
                shareUrl,
                'share',
                'width=600,height=400,scrollbars=yes,resizable=yes'
            );

            // Tracking Analytics
            this.trackEvent('social_share', {
                category: 'social',
                label: platform,
                value: 1,
            });

            // Focus sur la popup
            if (popup) popup.focus();
        }
    }

    initFloatingSocialShare() {
        const floatingShare = document.querySelector('[data-floating-share]');
        if (!floatingShare) return;

        // Afficher/masquer selon le scroll
        let isVisible = false;
        const threshold = 300;

        window.addEventListener('scroll', () => {
            const shouldShow = window.scrollY > threshold;
            
            if (shouldShow && !isVisible) {
                floatingShare.classList.remove('translate-x-full');
                floatingShare.classList.add('translate-x-0');
                isVisible = true;
            } else if (!shouldShow && isVisible) {
                floatingShare.classList.remove('translate-x-0');
                floatingShare.classList.add('translate-x-full');
                isVisible = false;
            }
        });
    }

    // === TRACKING DU SCROLL ===
    initScrollTracking() {
        let scrollDepths = [25, 50, 75, 90];
        let trackedDepths = new Set();

        window.addEventListener('scroll', () => {
            const scrollPercent = Math.round(
                (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100
            );

            scrollDepths.forEach(depth => {
                if (scrollPercent >= depth && !trackedDepths.has(depth)) {
                    trackedDepths.add(depth);
                    this.trackEvent('scroll_depth', {
                        category: 'engagement',
                        label: `${depth}%`,
                        value: depth,
                    });
                }
            });
        });
    }

    // === UTILITAIRES ===
    copyToClipboard(text) {
        return navigator.clipboard.writeText(text).then(() => {
            this.showToast('Copié dans le presse-papiers !');
            
            // Tracking Analytics
            this.trackEvent('copy_link', {
                category: 'engagement',
                label: 'clipboard',
            });
        }).catch(err => {
            console.error('Erreur lors de la copie:', err);
            this.showToast('Erreur lors de la copie', 'error');
        });
    }

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
    subscribeToNewsletter(email) {
        const form = this.newsletterForms[0];
        if (form) {
            form.emailInput.value = email;
            this.handleNewsletterSubmit(form);
        }
    }

    shareContent(platform, url, title, description) {
        this.shareOnPlatform(platform, url, title, description);
    }

    trackCustomEvent(eventName, parameters) {
        this.trackEvent(eventName, parameters);
    }

    trackPageChange(title, url) {
        this.trackPageView(title, url);
    }

    // === NETTOYAGE ===
    destroy() {
        // Nettoyer les event listeners et les ressources
        this.newsletterForms.forEach(newsletter => {
            if (newsletter.isSubmitting) {
                newsletter.isSubmitting = false;
            }
        });
    }
} 