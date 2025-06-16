@extends('layouts.app')

@section('title', __('app.nav.contact'))
@section('description', __('Contactez l\'équipe de L\'Astuce. Nous sommes là pour répondre à vos questions et écouter vos suggestions.'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-green-600 to-teal-600 text-white py-16">
    <div class="container-astuce">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                {{ __('Contactez-nous') }}
            </h1>
            <p class="text-xl text-green-100 mb-8 max-w-2xl mx-auto">
                {{ __('Une question, une suggestion ou simplement envie de nous dire bonjour ? Nous sommes là pour vous écouter !') }}
            </p>
        </div>
    </div>
</section>

<!-- Contact Form -->
<section class="py-16">
    <div class="container-astuce">
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Envoyez-nous un message') }}</h2>
                
                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
                @endif
                
                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
                @endif
                
                <form method="POST" action="{{ route('contact.store', ['locale' => app()->getLocale()]) }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Nom') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nom') border-red-500 @enderror">
                            @error('nom')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Email') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror">
                            @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="sujet" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Sujet') }} <span class="text-red-500">*</span>
                        </label>
                        <select id="sujet" 
                                name="sujet" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('sujet') border-red-500 @enderror">
                            <option value="">{{ __('Choisissez un sujet') }}</option>
                            <option value="question_generale" {{ old('sujet') == 'question_generale' ? 'selected' : '' }}>{{ __('Question générale') }}</option>
                            <option value="suggestion_astuce" {{ old('sujet') == 'suggestion_astuce' ? 'selected' : '' }}>{{ __('Suggestion d\'astuce') }}</option>
                            <option value="probleme_technique" {{ old('sujet') == 'probleme_technique' ? 'selected' : '' }}>{{ __('Problème technique') }}</option>
                            <option value="partenariat" {{ old('sujet') == 'partenariat' ? 'selected' : '' }}>{{ __('Proposition de partenariat') }}</option>
                            <option value="presse" {{ old('sujet') == 'presse' ? 'selected' : '' }}>{{ __('Demande presse') }}</option>
                            <option value="autre" {{ old('sujet') == 'autre' ? 'selected' : '' }}>{{ __('Autre') }}</option>
                        </select>
                        @error('sujet')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Message') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="6" 
                                  required
                                  placeholder="{{ __('Décrivez votre demande en détail...') }}"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-start">
                        <input type="checkbox" 
                               id="agree_privacy" 
                               name="agree_privacy" 
                               required
                               class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="agree_privacy" class="ml-2 text-sm text-gray-600">
                            {{ __('J\'accepte que mes données soient utilisées pour traiter ma demande') }} <span class="text-red-500">*</span>
                        </label>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition duration-300 transform hover:scale-105">
                        {{ __('Envoyer le message') }}
                    </button>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div class="space-y-8">
                <!-- Contact Methods -->
                <div class="bg-gray-50 rounded-xl p-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">{{ __('Autres moyens de nous contacter') }}</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">{{ __('Email') }}</h4>
                                <p class="text-gray-600">contact@lastuce.com</p>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Réponse sous 24-48h') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">{{ __('Réseaux sociaux') }}</h4>
                                <p class="text-gray-600">@lastuce_officiel</p>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Suivez-nous pour les dernières actualités') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">{{ __('YouTube') }}</h4>
                                <p class="text-gray-600">L'Astuce</p>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Découvrez nos épisodes') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ -->
                <div class="bg-white rounded-xl shadow-sm p-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">{{ __('Questions fréquentes') }}</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('Comment proposer une astuce ?') }}</h4>
                            <p class="text-gray-600 text-sm">{{ __('Utilisez notre formulaire de soumission d\'astuces disponible dans le menu principal.') }}</p>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('Délai de réponse ?') }}</h4>
                            <p class="text-gray-600 text-sm">{{ __('Nous répondons généralement sous 24 à 48 heures ouvrées.') }}</p>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('Partenariats ?') }}</h4>
                            <p class="text-gray-600 text-sm">{{ __('Contactez-nous avec le sujet "Proposition de partenariat" pour discuter des opportunités.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 