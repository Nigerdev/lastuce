@extends('layouts.app')

@section('title', __('forms.suggest_tip.page_title'))
@section('description', __('forms.suggest_tip.meta_description'))

@section('content')
<!-- Header Section -->
<section class="relative py-20 gradient-astuce overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-10 left-10 w-32 h-32 bg-green-500 rounded-full mix-blend-multiply filter blur-xl opacity-40 animate-blob"></div>
        <div class="absolute top-20 right-10 w-40 h-40 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-40 animate-blob animation-delay-2000"></div>
    </div>
    
    <div class="container-astuce relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center space-x-2 glass rounded-full px-6 py-3 mb-8">
                <span class="text-2xl">💡</span>
                <span class="text-white font-medium">{{ __('forms.suggest_tip.badge') }}</span>
            </div>
            
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                {{ __('forms.suggest_tip.title') }}
            </h1>
            <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                {{ __('forms.suggest_tip.subtitle') }}
            </p>
            
            <!-- Benefits -->
            <div class="grid md:grid-cols-3 gap-6 max-w-2xl mx-auto">
                <div class="text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-blue-100">{{ __('forms.suggest_tip.benefit_featured') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-blue-100">{{ __('forms.suggest_tip.benefit_community') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-blue-100">{{ __('forms.suggest_tip.benefit_recognition') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form Section -->
<section class="section-padding bg-slate-900">
    <div class="container-astuce">
        <div class="max-w-4xl mx-auto">
                            <form action="{{ route('astuces.store', ['locale' => app()->getLocale()]) }}" method="POST" enctype="multipart/form-data" id="tipForm" class="space-y-8">
                @csrf
                
                <!-- Progress Indicator -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-400">{{ __('forms.progress') }}</span>
                        <span class="text-sm font-medium text-gray-400">
                            <span id="currentStep">1</span> / 4
                        </span>
                    </div>
                    <div class="w-full bg-slate-700 rounded-full h-2">
                        <div id="progressBar" class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-500" style="width: 25%"></div>
                    </div>
                </div>
                
                <!-- Step 1: Basic Information -->
                <div id="step1" class="form-step">
                    <div class="card-glass">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center mr-4">
                                <span class="text-white font-bold">1</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ __('forms.suggest_tip.step1.title') }}</h2>
                                <p class="text-gray-400">{{ __('forms.suggest_tip.step1.subtitle') }}</p>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Tip Title -->
                            <div class="md:col-span-2">
                                <label for="titre" class="form-label required">{{ __('forms.tip_title') }}</label>
                                <input type="text" 
                                       id="titre" 
                                       name="titre" 
                                       value="{{ old('titre') }}"
                                       placeholder="{{ __('forms.tip_title_placeholder') }}" 
                                       class="form-input @error('titre') border-red-500 @enderror"
                                       required
                                       maxlength="100">
                                @error('titre')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                                <p class="form-help">{{ __('forms.suggest_tip.step1.title_help') }}</p>
                            </div>
                            
                            <!-- Category -->
                            <div>
                                <label for="categorie" class="form-label required">{{ __('forms.category') }}</label>
                                <select id="categorie" 
                                        name="categorie" 
                                        class="form-input @error('categorie') border-red-500 @enderror"
                                        required>
                                    <option value="">{{ __('forms.select_category') }}</option>
                                    <option value="cuisine" {{ old('categorie') === 'cuisine' ? 'selected' : '' }}>{{ __('forms.categories.cuisine') }}</option>
                                    <option value="maison" {{ old('categorie') === 'maison' ? 'selected' : '' }}>{{ __('forms.categories.home') }}</option>
                                    <option value="technologie" {{ old('categorie') === 'technologie' ? 'selected' : '' }}>{{ __('forms.categories.technology') }}</option>
                                    <option value="productivite" {{ old('categorie') === 'productivite' ? 'selected' : '' }}>{{ __('forms.categories.productivity') }}</option>
                                    <option value="sante" {{ old('categorie') === 'sante' ? 'selected' : '' }}>{{ __('forms.categories.health') }}</option>
                                    <option value="bricolage" {{ old('categorie') === 'bricolage' ? 'selected' : '' }}>{{ __('forms.categories.diy') }}</option>
                                    <option value="autre" {{ old('categorie') === 'autre' ? 'selected' : '' }}>{{ __('forms.categories.other') }}</option>
                                </select>
                                @error('categorie')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Difficulty -->
                            <div>
                                <label for="difficulte" class="form-label required">{{ __('forms.difficulty') }}</label>
                                <select id="difficulte" 
                                        name="difficulte" 
                                        class="form-input @error('difficulte') border-red-500 @enderror"
                                        required>
                                    <option value="">{{ __('forms.select_difficulty') }}</option>
                                    <option value="facile" {{ old('difficulte') === 'facile' ? 'selected' : '' }}>{{ __('forms.difficulty.easy') }}</option>
                                    <option value="moyen" {{ old('difficulte') === 'moyen' ? 'selected' : '' }}>{{ __('forms.difficulty.medium') }}</option>
                                    <option value="difficile" {{ old('difficulte') === 'difficile' ? 'selected' : '' }}>{{ __('forms.difficulty.hard') }}</option>
                                </select>
                                @error('difficulte')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button type="button" onclick="nextStep()" class="btn-primary">
                                {{ __('forms.next_step') }}
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2: Description -->
                <div id="step2" class="form-step hidden">
                    <div class="card-glass">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mr-4">
                                <span class="text-white font-bold">2</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ __('forms.suggest_tip.step2.title') }}</h2>
                                <p class="text-gray-400">{{ __('forms.suggest_tip.step2.subtitle') }}</p>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="space-y-6">
                            <div>
                                <label for="description" class="form-label required">{{ __('forms.description') }}</label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="6"
                                          placeholder="{{ __('forms.tip_description_placeholder') }}"
                                          class="form-input @error('description') border-red-500 @enderror"
                                          required
                                          maxlength="1000">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                                <p class="form-help">{{ __('forms.suggest_tip.step2.description_help') }}</p>
                            </div>
                            
                            <!-- Materials/Tools -->
                            <div>
                                <label for="materiel" class="form-label">{{ __('forms.materials_tools') }}</label>
                                <textarea id="materiel" 
                                          name="materiel" 
                                          rows="4"
                                          placeholder="{{ __('forms.materials_placeholder') }}"
                                          class="form-input @error('materiel') border-red-500 @enderror"
                                          maxlength="500">{{ old('materiel') }}</textarea>
                                @error('materiel')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                                <p class="form-help">{{ __('forms.suggest_tip.step2.materials_help') }}</p>
                            </div>
                            
                            <!-- Steps -->
                            <div>
                                <label for="etapes" class="form-label">{{ __('forms.steps') }}</label>
                                <textarea id="etapes" 
                                          name="etapes" 
                                          rows="6"
                                          placeholder="{{ __('forms.steps_placeholder') }}"
                                          class="form-input @error('etapes') border-red-500 @enderror"
                                          maxlength="1500">{{ old('etapes') }}</textarea>
                                @error('etapes')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                                <p class="form-help">{{ __('forms.suggest_tip.step2.steps_help') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-6">
                            <button type="button" onclick="prevStep()" class="btn-secondary">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                                </svg>
                                {{ __('forms.previous_step') }}
                            </button>
                            <button type="button" onclick="nextStep()" class="btn-primary">
                                {{ __('forms.next_step') }}
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Step 3: Media -->
                <div id="step3" class="form-step hidden">
                    <div class="card-glass">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center mr-4">
                                <span class="text-white font-bold">3</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ __('forms.suggest_tip.step3.title') }}</h2>
                                <p class="text-gray-400">{{ __('forms.suggest_tip.step3.subtitle') }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <!-- Photo Upload -->
                            <div>
                                <label for="photos" class="form-label">{{ __('forms.photos') }}</label>
                                <div class="mt-2">
                                    <div id="photoDropzone" class="dropzone">
                                        <div class="dropzone-content">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-gray-300 text-center">
                                                <span class="font-semibold">{{ __('forms.click_to_upload') }}</span> {{ __('forms.or_drag_drop') }}
                                            </p>
                                            <p class="text-gray-500 text-sm text-center mt-2">{{ __('forms.photo_requirements') }}</p>
                                        </div>
                                        <input type="file" 
                                               id="photos" 
                                               name="photos[]" 
                                               multiple 
                                               accept="image/*"
                                               class="hidden">
                                    </div>
                                    <div id="photoPreview" class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4 hidden"></div>
                                </div>
                                @error('photos')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Video URL -->
                            <div>
                                <label for="video_url" class="form-label">{{ __('forms.video_url') }}</label>
                                <input type="url" 
                                       id="video_url" 
                                       name="video_url" 
                                       value="{{ old('video_url') }}"
                                       placeholder="{{ __('forms.video_url_placeholder') }}" 
                                       class="form-input @error('video_url') border-red-500 @enderror">
                                @error('video_url')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                                <p class="form-help">{{ __('forms.suggest_tip.step3.video_help') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-6">
                            <button type="button" onclick="prevStep()" class="btn-secondary">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                                </svg>
                                {{ __('forms.previous_step') }}
                            </button>
                            <button type="button" onclick="nextStep()" class="btn-primary">
                                {{ __('forms.next_step') }}
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Step 4: Contact -->
                <div id="step4" class="form-step hidden">
                    <div class="card-glass">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center mr-4">
                                <span class="text-white font-bold">4</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ __('forms.suggest_tip.step4.title') }}</h2>
                                <p class="text-gray-400">{{ __('forms.suggest_tip.step4.subtitle') }}</p>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="nom" class="form-label required">{{ __('forms.name') }}</label>
                                <input type="text" 
                                       id="nom" 
                                       name="nom" 
                                       value="{{ old('nom') }}"
                                       placeholder="{{ __('forms.name_placeholder') }}" 
                                       class="form-input @error('nom') border-red-500 @enderror"
                                       required
                                       maxlength="100">
                                @error('nom')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="form-label required">{{ __('forms.email') }}</label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       placeholder="{{ __('forms.email_placeholder') }}" 
                                       class="form-input @error('email') border-red-500 @enderror"
                                       required>
                                @error('email')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Social Media -->
                            <div class="md:col-span-2">
                                <label for="reseaux_sociaux" class="form-label">{{ __('forms.social_media') }}</label>
                                <input type="text" 
                                       id="reseaux_sociaux" 
                                       name="reseaux_sociaux" 
                                       value="{{ old('reseaux_sociaux') }}"
                                       placeholder="{{ __('forms.social_media_placeholder') }}" 
                                       class="form-input @error('reseaux_sociaux') border-red-500 @enderror">
                                @error('reseaux_sociaux')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                                <p class="form-help">{{ __('forms.suggest_tip.step4.social_help') }}</p>
                            </div>
                        </div>
                        
                        <!-- Consent Checkboxes -->
                        <div class="space-y-4 mt-6 p-4 bg-slate-800 rounded-lg">
                            <div class="flex items-start">
                                <input type="checkbox" 
                                       id="accord_publication" 
                                       name="accord_publication" 
                                       value="1"
                                       class="form-checkbox mt-1"
                                       required>
                                <label for="accord_publication" class="ml-3 text-sm text-gray-300">
                                    {{ __('forms.suggest_tip.step4.consent_publication') }}
                                    <span class="text-red-400">*</span>
                                </label>
                            </div>
                            
                            <div class="flex items-start">
                                <input type="checkbox" 
                                       id="accord_newsletter" 
                                       name="accord_newsletter" 
                                       value="1"
                                       class="form-checkbox mt-1">
                                <label for="accord_newsletter" class="ml-3 text-sm text-gray-300">
                                    {{ __('forms.suggest_tip.step4.consent_newsletter') }}
                                </label>
                            </div>
                            
                            <div class="flex items-start">
                                <input type="checkbox" 
                                       id="accord_donnees" 
                                       name="accord_donnees" 
                                       value="1"
                                       class="form-checkbox mt-1"
                                       required>
                                <label for="accord_donnees" class="ml-3 text-sm text-gray-300">
                                    {{ __('forms.suggest_tip.step4.consent_data') }}
                                    <a href="@localizedRoute('privacy')" class="text-blue-400 hover:text-blue-300 underline">{{ __('forms.privacy_policy') }}</a>
                                    <span class="text-red-400">*</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-6">
                            <button type="button" onclick="prevStep()" class="btn-secondary">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                                </svg>
                                {{ __('forms.previous_step') }}
                            </button>
                            <button type="submit" class="btn-primary bg-green-600 hover:bg-green-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                {{ __('forms.submit_tip') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Success Message -->
@if(session('success'))
<div id="successModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-800 rounded-xl p-8 max-w-md mx-4 text-center">
        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">{{ __('forms.success.title') }}</h3>
        <p class="text-gray-300 mb-6">{{ session('success') }}</p>
        <button onclick="closeSuccessModal()" class="btn-primary">
            {{ __('forms.success.continue') }}
        </button>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 4;

function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            // Hide current step
            document.getElementById(`step${currentStep}`).classList.add('hidden');
            
            // Show next step
            currentStep++;
            document.getElementById(`step${currentStep}`).classList.remove('hidden');
            
            // Update progress
            updateProgress();
        }
    }
}

function prevStep() {
    if (currentStep > 1) {
        // Hide current step
        document.getElementById(`step${currentStep}`).classList.add('hidden');
        
        // Show previous step
        currentStep--;
        document.getElementById(`step${currentStep}`).classList.remove('hidden');
        
        // Update progress
        updateProgress();
    }
}

function updateProgress() {
    const progress = (currentStep / totalSteps) * 100;
    document.getElementById('progressBar').style.width = `${progress}%`;
    document.getElementById('currentStep').textContent = currentStep;
}

function validateCurrentStep() {
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const requiredInputs = currentStepElement.querySelectorAll('[required]');
    
    for (let input of requiredInputs) {
        if (!input.value.trim()) {
            input.focus();
            showError(input, '{{ __("validation.required") }}');
            return false;
        }
    }
    
    return true;
}

function showError(input, message) {
    // Remove existing error
    const existingError = input.parentNode.querySelector('.form-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Add error class
    input.classList.add('border-red-500');
    
    // Add error message
    const errorElement = document.createElement('p');
    errorElement.className = 'form-error';
    errorElement.textContent = message;
    input.parentNode.appendChild(errorElement);
    
    // Remove error on input
    input.addEventListener('input', function() {
        input.classList.remove('border-red-500');
        if (errorElement) {
            errorElement.remove();
        }
    }, { once: true });
}

// Photo upload handling
document.addEventListener('DOMContentLoaded', function() {
    const photoDropzone = document.getElementById('photoDropzone');
    const photoInput = document.getElementById('photos');
    const photoPreview = document.getElementById('photoPreview');
    
    photoDropzone.addEventListener('click', () => photoInput.click());
    
    photoDropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        photoDropzone.classList.add('border-blue-500', 'bg-blue-500/10');
    });
    
    photoDropzone.addEventListener('dragleave', () => {
        photoDropzone.classList.remove('border-blue-500', 'bg-blue-500/10');
    });
    
    photoDropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        photoDropzone.classList.remove('border-blue-500', 'bg-blue-500/10');
        const files = e.dataTransfer.files;
        handleFiles(files);
    });
    
    photoInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
    
    function handleFiles(files) {
        photoPreview.innerHTML = '';
        photoPreview.classList.remove('hidden');
        
        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const preview = document.createElement('div');
                    preview.className = 'relative';
                    preview.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg" alt="Preview ${index + 1}">
                        <button type="button" onclick="removePhoto(this)" class="absolute top-2 right-2 w-6 h-6 bg-red-600 rounded-full flex items-center justify-center text-white text-xs hover:bg-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    photoPreview.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

function removePhoto(button) {
    button.parentElement.remove();
    
    // If no photos left, hide preview
    if (document.getElementById('photoPreview').children.length === 0) {
        document.getElementById('photoPreview').classList.add('hidden');
    }
}

function closeSuccessModal() {
    document.getElementById('successModal').remove();
}

// Auto-close success modal after 5 seconds
@if(session('success'))
setTimeout(() => {
    const modal = document.getElementById('successModal');
    if (modal) {
        modal.remove();
    }
}, 5000);
@endif
</script>
@endpush 