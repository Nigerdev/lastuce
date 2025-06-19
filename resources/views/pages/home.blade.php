@extends('layouts.app')

@section('title', __('navigation.home'))
@section('description', __('home.meta_description'))

@section('content')
<!-- Hero Section with Featured Video -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden gradient-astuce">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Floating Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-1/4 animate-float">
            <div class="w-8 h-8 bg-white/20 rounded-full backdrop-blur-sm"></div>
        </div>
        <div class="absolute top-1/3 right-1/3 animate-float animation-delay-1000">
            <div class="w-6 h-6 bg-blue-400/30 rounded-full backdrop-blur-sm"></div>
        </div>
        <div class="absolute bottom-1/4 right-1/4 animate-float animation-delay-2000">
            <div class="w-10 h-10 bg-purple-400/20 rounded-full backdrop-blur-sm"></div>
        </div>
    </div>

    <div class="container-astuce relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-left">
                <!-- Badge -->
                <div class="inline-flex items-center space-x-2 glass rounded-full px-6 py-3 mb-8 animate-scale-in">
                    <span class="text-2xl">✨</span>
                    <span class="text-white font-medium">{{ __('home.badge_text') }}</span>
                    <span class="text-2xl">✨</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                    <span class="block text-white mb-2">{{ __('home.welcome_text') }}</span>
                    <span class="block text-gradient animate-gradient">
                        {{ __('home.site_name') }}
                    </span>
                </h1>

                <!-- Subtitle -->
                <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-xl leading-relaxed">
                    {{ __('home.hero_subtitle') }}
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-start space-y-4 sm:space-y-0 sm:space-x-6 mb-8">
                    <a href="{{ route('episodes.index', ['locale' => app()->getLocale()]) }}" class="btn-primary animate-pulse-glow">
                        🎬 {{ __('home.view_episodes') }}
                    </a>
                    <a href="{{ route('astuces.create', ['locale' => app()->getLocale()]) }}" class="btn-secondary">
                        💡 {{ __('home.suggest_tip.cta') }}
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6 max-w-md">
                    <div class="text-center animate-scale-in">
                        <div class="text-2xl md:text-3xl font-bold text-white mb-1">1000+</div>
                        <div class="text-sm text-gray-300">{{ __('home.stats.tips_shared') }}</div>
                    </div>
                    <div class="text-center animate-scale-in animation-delay-200">
                        <div class="text-2xl md:text-3xl font-bold text-white mb-1">25K+</div>
                        <div class="text-sm text-gray-300">{{ __('home.stats.active_community') }}</div>
                    </div>
                    <div class="text-center animate-scale-in animation-delay-400">
                        <div class="text-2xl md:text-3xl font-bold text-white mb-1">150+</div>
                        <div class="text-sm text-gray-300">{{ __('home.stats.exclusive_content') }}</div>
                    </div>
                </div>
            </div>

            <!-- Right Content - Featured Video -->
            <div class="relative animate-scale-in animation-delay-600">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    @php
                        $featuredVideoUrl = 'https://youtu.be/nZrsOHvBvM0?si=oF13PE_bAi-mFSJU';
                        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $featuredVideoUrl, $matches);
                        $featuredVideoId = $matches[1] ?? 'nZrsOHvBvM0';
                        $featuredThumbnailUrl = "https://img.youtube.com/vi/{$featuredVideoId}/maxresdefault.jpg";
                    @endphp

                    <div class="aspect-video bg-gradient-to-br from-gray-900 to-gray-800 relative cursor-pointer group"
                         data-video-id="{{ $featuredVideoId }}"
                         data-video-title="{{ __('home.featured_video.title') }}"
                         id="featured-video-container">
                        <!-- Miniature YouTube -->
                        <img src="{{ $featuredThumbnailUrl }}"
                             alt="{{ __('home.featured_video.title') }}"
                             class="w-full h-full object-cover"
                             onerror="this.src='https://img.youtube.com/vi/{{ $featuredVideoId }}/hqdefault.jpg'">

                        <!-- Overlay avec bouton play -->
                        <div class="absolute inset-0 bg-black/30 group-hover:bg-black/20 transition-all flex items-center justify-center">
                            <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center group-hover:bg-red-700 transition-colors shadow-2xl group-hover:scale-110 transform duration-300">
                                <svg class="w-10 h-10 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Informations vidéo -->
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6">
                            <h3 class="text-xl font-bold text-white mb-2">{{ __('home.featured_video.title') }}</h3>
                            <p class="text-gray-300 text-sm">{{ __('home.featured_video.description') }}</p>

                            <!-- Badge YouTube -->
                            <div class="mt-3 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-600 text-white">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                    YouTube
                                </span>
                                <a href="{{ $featuredVideoUrl }}" target="_blank"
                                   class="text-gray-300 hover:text-white text-xs transition-colors"
                                   onclick="event.stopPropagation()">
                                    {{ __('Voir sur YouTube') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white hover:text-blue-400 transition-colors animate-bounce">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>

<!-- Latest Episodes Section -->
<section id="latest-episodes" class="section-padding bg-slate-800">
    <div class="container-astuce">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                {{ __('home.latest_episodes.title') }}
            </h2>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto mb-8">
                {{ __('home.latest_episodes.subtitle') }}
            </p>
        </div>

        <!-- Episodes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @forelse($latestEpisodes as $episode)
            <div class="card-glass group hover:scale-105 transition-all duration-300">
                <div class="relative mb-4">
                    @if($episode->youtube_url)
                        @php
                            // Extraire l'ID de la vidéo YouTube
                            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $episode->youtube_url, $matches);
                            $videoId = $matches[1] ?? null;
                            $thumbnailUrl = $videoId ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg" : null;
                        @endphp

                        <div class="aspect-video bg-gradient-to-br from-gray-700 to-gray-900 rounded-xl overflow-hidden relative cursor-pointer"
                             onclick="playYouTubeVideo('{{ $videoId }}', this)">
                            @if($thumbnailUrl)
                                <img src="{{ $thumbnailUrl }}" alt="{{ $episode->titre }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.src='https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg'">
                            @endif

                            <!-- Play Button Overlay -->
                            <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/20 transition-all">
                                <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center group-hover:bg-red-700 transition-colors shadow-lg">
                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="aspect-video bg-gradient-to-br from-gray-700 to-gray-900 rounded-xl overflow-hidden">
                            <div class="w-full h-full flex items-center justify-center">
                                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center group-hover:bg-white/30 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="absolute top-3 left-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $episode->type === 'episode' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $episode->type === 'coulisse' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $episode->type === 'bonus' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $episode->type === 'special' ? 'bg-green-100 text-green-800' : '' }}">
                            {{ ucfirst($episode->type ?? 'Episode') }}
                        </span>
                    </div>

                    @if($episode->duree)
                    <div class="absolute bottom-3 right-3 bg-black/70 text-white text-xs px-2 py-1 rounded">
                        {{ gmdate('H:i:s', $episode->duree) }}
                    </div>
                    @endif
                </div>

                <h3 class="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                    {{ $episode->titre }}
                </h3>

                <p class="text-gray-400 text-sm mb-4 line-clamp-2">
                    {{ Str::limit($episode->description, 120) }}
                </p>

                <div class="flex items-center justify-between">
                    <div class="flex items-center text-gray-500 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ number_format($episode->vues ?? 0) }}
                    </div>
                    <span class="text-gray-500 text-sm">
                        {{ $episode->created_at->diffForHumans() }}
                    </span>
                </div>

                <!-- Boutons d'action -->
                <div class="mt-4 flex items-center space-x-2">
                    @if($episode->youtube_url)
                    <a href="{{ $episode->youtube_url }}" target="_blank"
                       class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-2 rounded-lg transition-colors text-center">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                        YouTube
                    </a>
                    @endif

                    <a href="{{ route('episodes.show', ['locale' => app()->getLocale(), 'slug' => $episode->slug]) }}"
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-2 rounded-lg transition-colors text-center">
                        {{ __('Voir plus') }}
                    </a>
                </div>
            </div>
            @empty
            @endforelse
        </div>

        <div class="text-center">
            <a href="{{ route('episodes.index', ['locale' => app()->getLocale()]) }}" class="btn-primary">
                {{ __('home.view_all_episodes') }}
                <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Suggest Your Tip Section -->
<section class="section-padding bg-gradient-to-r from-blue-600 to-purple-600">
    <div class="container-astuce">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                    {{ __('home.suggest_tip.title') }}
                </h2>
                <p class="text-xl text-blue-100 mb-8">
                    {{ __('home.suggest_tip.description') }}
                </p>

                <div class="space-y-4 mb-8">
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-blue-100">{{ __('home.suggest_tip.benefit_1') }}</p>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-blue-100">{{ __('home.suggest_tip.benefit_2') }}</p>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-blue-100">{{ __('home.suggest_tip.benefit_3') }}</p>
                    </div>
                </div>

                <a href="{{ route('astuces.create', ['locale' => app()->getLocale()]) }}" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-semibold text-lg transition duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl inline-flex items-center">
                    💡 {{ __('home.suggest_tip.cta') }}
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>

            <div class="relative">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">{{ __('home.suggest_tip.form_title') }}</h3>
                        <p class="text-blue-100">{{ __('home.suggest_tip.form_subtitle') }}</p>
                    </div>

                    <div class="space-y-4">
                        <input type="text" placeholder="{{ __('forms.tip_title_placeholder') }}" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200 focus:ring-2 focus:ring-white focus:border-white">
                        <textarea placeholder="{{ __('forms.tip_description_placeholder') }}" rows="3" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200 focus:ring-2 focus:ring-white focus:border-white resize-none"></textarea>
                        <div class="text-center">
                            <p class="text-blue-200 text-sm">{{ __('home.suggest_tip.form_note') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partners Section -->
<section class="section-padding bg-slate-900">
    <div class="container-astuce">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                    {{ __('Nos Partenaires') }}
                </h2>
                <p class="text-xl text-gray-400 mb-8 max-w-2xl mx-auto">
                    {{ __('Découvrez les entreprises qui nous font confiance et collaborent avec nous') }}
                </p>
            </div>

            @if($partners && $partners->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    @foreach($partners as $partner)
                        <div class="bg-slate-800 rounded-2xl p-6 hover:bg-slate-700 transition-colors duration-300">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h4M9 7h6m-6 4h6m-6 4h6"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-white mb-2">{{ $partner->nom_entreprise }}</h3>
                                    <p class="text-sm text-gray-400 mb-2">
                                        <span class="font-medium">Contact:</span> {{ $partner->contact }}
                                    </p>
                                    <p class="text-gray-300 text-sm line-clamp-3">{{ Str::limit($partner->message, 120) }}</p>
                                    <div class="mt-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ {{ __('Partenaire actif') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gradient-to-r from-gray-500 to-gray-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">{{ __('Aucun partenaire pour le moment') }}</h3>
                    <p class="text-gray-400 mb-6">{{ __('Soyez le premier à rejoindre notre réseau de partenaires !') }}</p>
                </div>
            @endif

            <div class="text-center">
                <a href="{{ route('partenariats.create', ['locale' => app()->getLocale()]) }}" class="btn-primary text-lg px-8 py-4">
                    🤝 {{ __('Devenir partenaire') }}
                    <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Behind the Scenes News -->
<section class="section-padding bg-slate-800">
    <div class="container-astuce">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                {{ __('home.behind_scenes.title') }}
            </h2>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                {{ __('home.behind_scenes.subtitle') }}
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                @for($i = 1; $i <= 3; $i++)
                <div class="card-glass p-6 hover:scale-105 transition-all duration-300">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-white mb-2">
                                {{ __('home.behind_scenes.news_title') }} {{ $i }}
                            </h3>
                            <p class="text-gray-400 mb-4">
                                {{ __('home.behind_scenes.news_description') }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-blue-400 text-sm font-medium">{{ __('app.common.read_more') }}</span>
                                <span class="text-gray-500 text-sm">{{ __('time.days_ago', ['count' => $i]) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>

            <div class="relative">
                <div class="aspect-square bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl overflow-hidden shadow-2xl">
                    <div class="w-full h-full flex items-center justify-center">
                        <div class="text-center text-white">
                            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-4">{{ __('home.behind_scenes.gallery_title') }}</h3>
                            <p class="text-purple-100">{{ __('home.behind_scenes.gallery_description') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
@include('components.newsletter-section', [
    'title' => __('home.newsletter.title'),
    'description' => __('home.newsletter.description'),
    'backgroundClass' => 'bg-gradient-to-r from-slate-900 to-slate-800'
])

@endsection

@push('scripts')
<script>
// Variables globales pour les fonctions vidéo
window.VideoPlayer = {
    // Fonction pour jouer une vidéo YouTube directement dans l'élément
    playYouTubeVideo: function(videoId, element) {
        console.log('playYouTubeVideo called with:', videoId, element);

        if (!videoId) {
            console.error('Video ID is required');
            return;
        }

        if (!element) {
            console.error('Element is required');
            return;
        }

        // Créer l'iframe YouTube
        const iframe = document.createElement('iframe');
        iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&enablejsapi=1`;
        iframe.width = '100%';
        iframe.height = '100%';
        iframe.frameBorder = '0';
        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
        iframe.allowFullscreen = true;
        iframe.className = 'absolute inset-0 w-full h-full rounded-xl';
        iframe.setAttribute('allowfullscreen', '');

        // Remplacer le contenu de l'élément par l'iframe
        element.innerHTML = '';
        element.appendChild(iframe);

        // Ajouter une classe pour indiquer que la vidéo est en cours de lecture
        element.classList.add('playing-video');

        console.log('Video iframe created and added to element');
    },

    // Fonction pour créer un modal de lecture vidéo
    openVideoModal: function(videoId, title) {
        console.log('openVideoModal called with:', videoId, title);

        if (!videoId) {
            console.error('Video ID is required for modal');
            return;
        }

        // Vérifier si un modal existe déjà
        const existingModal = document.querySelector('.video-modal');
        if (existingModal) {
            this.closeVideoModal(existingModal);
        }

        // Créer le modal
        const modal = document.createElement('div');
        modal.className = 'video-modal fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4 opacity-0 transition-opacity duration-300';

        const self = this;
        modal.onclick = function(e) {
            if (e.target === modal) {
                self.closeVideoModal(modal);
            }
        };

        // Contenu du modal
        modal.innerHTML = `
            <div class="relative w-full max-w-5xl bg-black rounded-lg overflow-hidden shadow-2xl">
                <div class="aspect-video">
                    <iframe
                        src="https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&enablejsapi=1"
                        width="100%"
                        height="100%"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                        class="w-full h-full">
                    </iframe>
                </div>
                <div class="absolute top-4 right-4 z-10">
                    <button class="close-modal-btn bg-black bg-opacity-70 text-white p-3 rounded-full hover:bg-opacity-90 transition-all transform hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                ${title ? `
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-black/50 to-transparent p-6">
                        <h3 class="text-white text-xl font-bold">${title}</h3>
                    </div>
                ` : ''}
            </div>
        `;

        // Ajouter au DOM
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';

        // Ajouter event listener pour le bouton fermer
        const closeBtn = modal.querySelector('.close-modal-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                this.closeVideoModal(modal);
            });
        }

        // Animation d'entrée
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
        }, 50);

        console.log('Video modal created and displayed');
    },

    // Fonction pour fermer le modal vidéo
    closeVideoModal: function(modal) {
        console.log('closeVideoModal called');

        if (!modal) {
            console.error('Modal element not found');
            return;
        }

        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');

        setTimeout(() => {
            if (modal.parentNode) {
                document.body.removeChild(modal);
            }
            document.body.style.overflow = 'auto';
            console.log('Video modal closed and removed');
        }, 300);
    }
};

// Fonctions globales pour compatibilité
window.openVideoModal = function(videoId, title) {
    return window.VideoPlayer.openVideoModal(videoId, title);
};

window.closeVideoModal = function(modal) {
    return window.VideoPlayer.closeVideoModal(modal);
};

window.playYouTubeVideo = function(videoId, element) {
    return window.VideoPlayer.playYouTubeVideo(videoId, element);
};

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, video functions ready');

    // Test si les fonctions sont disponibles
    if (typeof window.VideoPlayer.openVideoModal === 'function') {
        console.log('VideoPlayer.openVideoModal function is available');
    } else {
        console.error('VideoPlayer.openVideoModal function is NOT available');
    }

    // Ajouter event listener pour la vidéo featured
    const featuredVideo = document.getElementById('featured-video-container');
    if (featuredVideo) {
        console.log('Featured video container found, adding click listener');

        featuredVideo.addEventListener('click', function() {
            const videoId = this.getAttribute('data-video-id');
            const videoTitle = this.getAttribute('data-video-title');

            console.log('Featured video clicked:', videoId, videoTitle);

            if (videoId) {
                window.VideoPlayer.openVideoModal(videoId, videoTitle);
            } else {
                console.error('No video ID found in data attributes');
            }
        });
    } else {
        console.error('Featured video container NOT found');
    }

    // Gestion des touches clavier
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const videoModal = document.querySelector('.video-modal');
            if (videoModal) {
                window.VideoPlayer.closeVideoModal(videoModal);
            }
        }
    });

    // Lazy loading pour les miniatures YouTube
    const thumbnails = document.querySelectorAll('img[src*="youtube.com"]');

    if (thumbnails.length > 0) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });

        thumbnails.forEach(img => imageObserver.observe(img));
    }
});
</script>
@endpush
