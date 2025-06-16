/**
 * Gestionnaire de formulaires interactifs avec validation et upload
 */
export class FormHandler {
    constructor(form) {
        this.form = form;
        this.fields = {};
        this.validators = {};
        this.isSubmitting = false;
        this.uploadZones = [];
        
        this.init();
    }

    init() {
        this.setupFields();
        this.setupValidation();
        this.setupFileUpload();
        this.setupSubmission();
        this.bindEvents();
    }

    setupFields() {
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            const fieldName = input.name || input.id;
            if (fieldName) {
                this.fields[fieldName] = {
                    element: input,
                    isValid: true,
                    errors: [],
                    rules: this.parseValidationRules(input),
                };
            }
        });
    }

    parseValidationRules(input) {
        const rules = {};
        
        // Règles HTML5
        if (input.required) rules.required = true;
        if (input.type === 'email') rules.email = true;
        if (input.type === 'url') rules.url = true;
        if (input.minLength) rules.minLength = input.minLength;
        if (input.maxLength) rules.maxLength = input.maxLength;
        if (input.min) rules.min = parseFloat(input.min);
        if (input.max) rules.max = parseFloat(input.max);
        if (input.pattern) rules.pattern = new RegExp(input.pattern);

        // Règles personnalisées via data attributes
        const customRules = input.dataset.validation;
        if (customRules) {
            try {
                Object.assign(rules, JSON.parse(customRules));
            } catch (e) {
                console.warn('Règles de validation invalides:', customRules);
            }
        }

        return rules;
    }

    setupValidation() {
        this.validators = {
            required: (value) => value.trim() !== '',
            email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
            url: (value) => {
                try {
                    new URL(value);
                    return true;
                } catch {
                    return false;
                }
            },
            minLength: (value, min) => value.length >= min,
            maxLength: (value, max) => value.length <= max,
            min: (value, min) => parseFloat(value) >= min,
            max: (value, max) => parseFloat(value) <= max,
            pattern: (value, pattern) => pattern.test(value),
            phone: (value) => /^[\+]?[1-9][\d]{0,15}$/.test(value.replace(/\s/g, '')),
            password: (value) => {
                // Au moins 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre
                return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/.test(value);
            },
            confirmPassword: (value, originalField) => {
                const originalValue = this.fields[originalField]?.element.value;
                return value === originalValue;
            },
        };
    }

    setupFileUpload() {
        const fileInputs = this.form.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            this.createUploadZone(input);
        });
    }

    createUploadZone(input) {
        const container = input.parentElement;
        const allowedTypes = input.accept ? input.accept.split(',').map(t => t.trim()) : [];
        const maxSize = parseInt(input.dataset.maxSize) || 10 * 1024 * 1024; // 10MB par défaut
        const multiple = input.multiple;

        // Créer la zone de drop
        const dropZone = document.createElement('div');
        dropZone.className = 'upload-drop-zone border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors duration-200 hover:border-blue-400 hover:bg-blue-50';
        dropZone.innerHTML = `
            <div class="upload-content">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="mt-4">
                    <label class="cursor-pointer">
                        <span class="mt-2 block text-sm font-medium text-gray-900">
                            Cliquez pour sélectionner ${multiple ? 'des fichiers' : 'un fichier'}
                        </span>
                        <span class="mt-1 block text-xs text-gray-500">
                            ou glissez-déposez ${multiple ? 'vos fichiers' : 'votre fichier'} ici
                        </span>
                    </label>
                </div>
                ${allowedTypes.length > 0 ? `
                    <p class="mt-2 text-xs text-gray-500">
                        Types acceptés: ${allowedTypes.join(', ')}
                    </p>
                ` : ''}
                <p class="text-xs text-gray-500">
                    Taille max: ${this.formatFileSize(maxSize)}
                </p>
            </div>
            <div class="upload-progress hidden">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="mt-2 text-sm text-gray-600">Upload en cours...</p>
            </div>
        `;

        // Masquer l'input original
        input.style.display = 'none';
        container.appendChild(dropZone);

        // Zone de prévisualisation
        const previewZone = document.createElement('div');
        previewZone.className = 'upload-preview mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 hidden';
        container.appendChild(previewZone);

        const uploadZone = {
            input,
            dropZone,
            previewZone,
            allowedTypes,
            maxSize,
            multiple,
            files: [],
        };

        this.uploadZones.push(uploadZone);
        this.bindUploadEvents(uploadZone);
    }

    bindUploadEvents(uploadZone) {
        const { input, dropZone } = uploadZone;

        // Événements de drag & drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('border-blue-500', 'bg-blue-50');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            });
        });

        dropZone.addEventListener('drop', (e) => {
            const files = Array.from(e.dataTransfer.files);
            this.handleFiles(uploadZone, files);
        });

        // Clic sur la zone
        dropZone.addEventListener('click', () => {
            input.click();
        });

        // Sélection de fichiers
        input.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            this.handleFiles(uploadZone, files);
        });
    }

    handleFiles(uploadZone, files) {
        const validFiles = [];
        const errors = [];

        files.forEach(file => {
            const validation = this.validateFile(file, uploadZone);
            if (validation.isValid) {
                validFiles.push(file);
            } else {
                errors.push(`${file.name}: ${validation.error}`);
            }
        });

        if (errors.length > 0) {
            this.showMessage(errors.join('\n'), 'error');
            return;
        }

        if (!uploadZone.multiple) {
            uploadZone.files = validFiles.slice(0, 1);
        } else {
            uploadZone.files = [...uploadZone.files, ...validFiles];
        }

        this.updateFilePreview(uploadZone);
        this.updateFormData(uploadZone);
    }

    validateFile(file, uploadZone) {
        const { allowedTypes, maxSize } = uploadZone;

        // Vérifier la taille
        if (file.size > maxSize) {
            return {
                isValid: false,
                error: `Fichier trop volumineux (max: ${this.formatFileSize(maxSize)})`
            };
        }

        // Vérifier le type
        if (allowedTypes.length > 0) {
            const isValidType = allowedTypes.some(type => {
                if (type.startsWith('.')) {
                    return file.name.toLowerCase().endsWith(type.toLowerCase());
                }
                return file.type.match(type.replace('*', '.*'));
            });

            if (!isValidType) {
                return {
                    isValid: false,
                    error: `Type de fichier non autorisé`
                };
            }
        }

        return { isValid: true };
    }

    updateFilePreview(uploadZone) {
        const { previewZone, files } = uploadZone;
        
        if (files.length === 0) {
            previewZone.classList.add('hidden');
            return;
        }

        previewZone.classList.remove('hidden');
        previewZone.innerHTML = '';

        files.forEach((file, index) => {
            const preview = document.createElement('div');
            preview.className = 'relative bg-white border border-gray-200 rounded-lg p-3';
            
            const isImage = file.type.startsWith('image/');
            
            preview.innerHTML = `
                <div class="flex items-center space-x-3">
                    ${isImage ? `
                        <img src="${URL.createObjectURL(file)}" alt="${file.name}" 
                             class="w-12 h-12 object-cover rounded">
                    ` : `
                        <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                            </svg>
                        </div>
                    `}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                        <p class="text-xs text-gray-500">${this.formatFileSize(file.size)}</p>
                    </div>
                    <button type="button" class="remove-file text-red-400 hover:text-red-600" data-index="${index}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                        </svg>
                    </button>
                </div>
            `;

            // Bouton de suppression
            const removeBtn = preview.querySelector('.remove-file');
            removeBtn.addEventListener('click', () => {
                this.removeFile(uploadZone, index);
            });

            previewZone.appendChild(preview);
        });
    }

    removeFile(uploadZone, index) {
        uploadZone.files.splice(index, 1);
        this.updateFilePreview(uploadZone);
        this.updateFormData(uploadZone);
    }

    updateFormData(uploadZone) {
        const { input, files } = uploadZone;
        
        // Créer un nouveau FileList (simulation)
        const dt = new DataTransfer();
        files.forEach(file => dt.items.add(file));
        input.files = dt.files;
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    setupSubmission() {
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });
    }

    bindEvents() {
        // Validation en temps réel
        Object.values(this.fields).forEach(field => {
            const { element } = field;
            
            // Validation sur blur
            element.addEventListener('blur', () => {
                this.validateField(field);
            });

            // Validation sur input (avec debounce)
            let timeout;
            element.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.validateField(field);
                }, 300);
            });
        });
    }

    validateField(field) {
        const { element, rules } = field;
        const value = element.value;
        const errors = [];

        // Appliquer les règles de validation
        Object.entries(rules).forEach(([rule, ruleValue]) => {
            if (this.validators[rule]) {
                const isValid = this.validators[rule](value, ruleValue);
                if (!isValid) {
                    errors.push(this.getErrorMessage(rule, ruleValue));
                }
            }
        });

        field.isValid = errors.length === 0;
        field.errors = errors;

        this.updateFieldUI(field);
        return field.isValid;
    }

    getErrorMessage(rule, ruleValue) {
        const messages = {
            required: 'Ce champ est obligatoire',
            email: 'Veuillez saisir une adresse email valide',
            url: 'Veuillez saisir une URL valide',
            minLength: `Minimum ${ruleValue} caractères`,
            maxLength: `Maximum ${ruleValue} caractères`,
            min: `Valeur minimum: ${ruleValue}`,
            max: `Valeur maximum: ${ruleValue}`,
            pattern: 'Format invalide',
            phone: 'Numéro de téléphone invalide',
            password: 'Le mot de passe doit contenir au moins 8 caractères, 1 majuscule, 1 minuscule et 1 chiffre',
            confirmPassword: 'Les mots de passe ne correspondent pas',
        };

        return messages[rule] || 'Valeur invalide';
    }

    updateFieldUI(field) {
        const { element, isValid, errors } = field;
        const container = element.closest('.form-group') || element.parentElement;
        
        // Supprimer les anciens messages d'erreur
        const oldError = container.querySelector('.field-error');
        if (oldError) oldError.remove();

        // Mettre à jour les classes CSS
        element.classList.remove('border-red-500', 'border-green-500');
        
        if (element.value.trim() !== '') {
            if (isValid) {
                element.classList.add('border-green-500');
            } else {
                element.classList.add('border-red-500');
                
                // Afficher le message d'erreur
                const errorDiv = document.createElement('div');
                errorDiv.className = 'field-error mt-1 text-sm text-red-600';
                errorDiv.textContent = errors[0];
                container.appendChild(errorDiv);
            }
        }
    }

    validateForm() {
        let isFormValid = true;
        
        Object.values(this.fields).forEach(field => {
            const fieldValid = this.validateField(field);
            if (!fieldValid) isFormValid = false;
        });

        return isFormValid;
    }

    async handleSubmit() {
        if (this.isSubmitting) return;

        // Valider le formulaire
        if (!this.validateForm()) {
            this.showMessage('Veuillez corriger les erreurs dans le formulaire', 'error');
            return;
        }

        this.isSubmitting = true;
        this.showSubmitLoading();

        try {
            const formData = new FormData(this.form);
            
            // Ajouter le token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                formData.append('_token', csrfToken);
            }

            const response = await fetch(this.form.action, {
                method: this.form.method || 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const result = await response.json();

            if (response.ok) {
                this.handleSuccess(result);
            } else {
                this.handleError(result);
            }
        } catch (error) {
            console.error('Erreur lors de la soumission:', error);
            this.showMessage('Une erreur est survenue lors de l\'envoi du formulaire', 'error');
        } finally {
            this.isSubmitting = false;
            this.hideSubmitLoading();
        }
    }

    showSubmitLoading() {
        const submitBtn = this.form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Envoi en cours...
            `;
        }
    }

    hideSubmitLoading() {
        const submitBtn = this.form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            const originalText = submitBtn.dataset.originalText || 'Envoyer';
            submitBtn.textContent = originalText;
        }
    }

    handleSuccess(result) {
        this.showMessage(result.message || 'Formulaire envoyé avec succès !', 'success');
        
        // Réinitialiser le formulaire si demandé
        if (result.reset !== false) {
            this.resetForm();
        }

        // Redirection si spécifiée
        if (result.redirect) {
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 2000);
        }

        // Événement personnalisé
        this.form.dispatchEvent(new CustomEvent('form:success', {
            detail: result
        }));
    }

    handleError(result) {
        if (result.errors) {
            // Erreurs de validation spécifiques aux champs
            Object.entries(result.errors).forEach(([fieldName, errors]) => {
                const field = this.fields[fieldName];
                if (field) {
                    field.isValid = false;
                    field.errors = Array.isArray(errors) ? errors : [errors];
                    this.updateFieldUI(field);
                }
            });
        }

        this.showMessage(result.message || 'Une erreur est survenue', 'error');

        // Événement personnalisé
        this.form.dispatchEvent(new CustomEvent('form:error', {
            detail: result
        }));
    }

    showMessage(message, type = 'info') {
        // Supprimer les anciens messages
        const oldMessages = document.querySelectorAll('.form-message');
        oldMessages.forEach(msg => msg.remove());

        const messageDiv = document.createElement('div');
        messageDiv.className = `form-message fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md transition-all duration-300 transform translate-x-full`;
        
        const bgColor = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500',
        }[type] || 'bg-blue-500';

        messageDiv.className += ` ${bgColor} text-white`;
        messageDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <p class="flex-1">${message}</p>
                <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(messageDiv);

        // Animation d'entrée
        setTimeout(() => {
            messageDiv.classList.remove('translate-x-full');
        }, 100);

        // Auto-suppression
        setTimeout(() => {
            messageDiv.classList.add('translate-x-full');
            setTimeout(() => messageDiv.remove(), 300);
        }, 5000);
    }

    resetForm() {
        this.form.reset();
        
        // Réinitialiser les champs
        Object.values(this.fields).forEach(field => {
            field.isValid = true;
            field.errors = [];
            this.updateFieldUI(field);
        });

        // Réinitialiser les uploads
        this.uploadZones.forEach(uploadZone => {
            uploadZone.files = [];
            this.updateFilePreview(uploadZone);
        });
    }

    // API publique
    validate() {
        return this.validateForm();
    }

    submit() {
        this.handleSubmit();
    }

    reset() {
        this.resetForm();
    }

    getFormData() {
        return new FormData(this.form);
    }

    destroy() {
        // Nettoyer les event listeners et les ressources
        this.uploadZones.forEach(uploadZone => {
            uploadZone.files.forEach(file => {
                if (file.preview) {
                    URL.revokeObjectURL(file.preview);
                }
            });
        });
    }
} 