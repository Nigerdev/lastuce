@extends('layouts.app')

@section('title', __('Épisodes - L\'Astuce'))
@section('description', __('Découvrez tous nos épisodes de L\'Astuce avec des conseils pratiques pour améliorer votre quotidien'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-slate-900 via-purple-900 to-slate-900 py-20">
    <div class="container-astuce">
        <div class="text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                {{ __('Tous nos épisodes') }}
            </h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto mb-8">
                {{ __('Découvrez notre collection complète d\'épisodes remplis d\'astuces pratiques pour améliorer votre quotidien') }}
            </p>
            
            <!-- Statistiques -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-2xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-400">{{ $stats['total'] ?? 0 }}</div>
                    <div class="text-sm text-gray-400">{{ __('Épisodes') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-400">{{ $stats['episode'] ?? 0 }}</div>
                    <div class="text-sm text-gray-400">{{ __('Principaux') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-400">{{ $stats['coulisse'] ?? 0 }}</div>
                    <div class="text-sm text-gray-400">{{ __('Coulisses') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-400">{{ $stats['bonus'] ?? 0 }}</div>
                    <div class="text-sm text-gray-400">{{ __('Bonus') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filtres et Recherche -->
<section class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
    <div class="container-astuce py-6">
        <form method="GET" action="{{ route('episodes.index', ['locale' => app()->getLocale()]) }}" class="space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Recherche -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}"
                               placeholder="{{ __('Rechercher un épisode...') }}"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="flex flex-wrap gap-3">
                    <!-- Type -->
                    <select name="type" class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @if(isset($types))
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ ($typeFilter ?? 'all') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    <!-- Tri -->
                    <select name="sort" class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @if(isset($sortOptions))
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" {{ ($sortBy ?? 'recent') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    <!-- Bouton de recherche -->
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium">
                        {{ __('Filtrer') }}
                    </button>

                    <!-- Reset -->
                    @if(($search ?? '') || ($typeFilter ?? 'all') !== 'all' || ($sortBy ?? 'recent') !== 'recent')
                    <a href="{{ route('episodes.index', ['locale' => app()->getLocale()]) }}" 
                       class="px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                        {{ __('Réinitialiser') }}
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Liste des épisodes -->
<section class="section-padding bg-gray-50">
    <div class="container-astuce">
        @if(isset($episodes) && $episodes->count() > 0)
            <!-- Résultats -->
            <div class="mb-8">
                <p class="text-gray-600">
                    {{ $episodes->total() }} {{ __('épisode(s) trouvé(s)') }}
                    @if($search ?? '')
                        {{ __('pour') }} "<strong>{{ $search }}</strong>"
                    @endif
                </p>
            </div>

            <!-- Grille des épisodes -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($episodes as $episode)
                <div class="episode-card animate-scale-in" style="animation-delay: {{ $loop->index * 100 }}ms;">
                    <!-- Miniature -->
                    <div class="episode-thumbnail rounded-t-xl">
                        @if($episode->youtube_url)
                            @php
                                // Extraire l'ID de la vidéo YouTube
                                preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $episode->youtube_url, $matches);
                                $videoId = $matches[1] ?? null;
                                $thumbnailUrl = $videoId ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg" : null;
                            @endphp
                            
                            <div class="w-full h-full cursor-pointer" onclick="playYouTubeVideo('{{ $videoId }}', this)">
                                @if($thumbnailUrl)
                                    <img src="{{ $thumbnailUrl }}" alt="{{ $episode->titre }}" 
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                         onerror="this.src='https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg'">
                                @endif
                                
                                <!-- Play Button Overlay -->
                                <div class="episode-overlay">
                                    <div class="episode-play-button">
                                        <svg class="w-6 h-6 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-900">
                                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Badge type -->
                        <div class="absolute top-3 left-3">
                            <span class="episode-badge 
                                {{ $episode->type === 'episode' ? 'episode-badge-episode' : '' }}
                                {{ $episode->type === 'coulisse' ? 'episode-badge-coulisse' : '' }}
                                {{ $episode->type === 'bonus' ? 'episode-badge-bonus' : '' }}
                                {{ $episode->type === 'special' ? 'episode-badge-special' : '' }}">
                                {{ ucfirst($episode->type ?? 'Episode') }}
                            </span>
                        </div>
                        
                        <!-- Durée -->
                        @if($episode->duree)
                        <div class="episode-duration">
                            {{ gmdate('H:i:s', $episode->duree) }}
                        </div>
                        @endif
                    </div>
                    
                    <!-- Contenu -->
                    <div class="p-6">
                        <h3 class="episode-title">
                            {{ $episode->titre }}
                        </h3>
                        
                        <p class="episode-description">
                            {{ $episode->description }}
                        </p>
                        
                        <!-- Métadonnées -->
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ number_format($episode->vues ?? 0) }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $episode->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center space-x-2">
                            @if($episode->youtube_url)
                            <a href="{{ $episode->youtube_url }}" target="_blank" 
                               class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-2 rounded-lg transition-all duration-300 text-center font-medium shadow-md hover:shadow-lg transform hover:scale-105">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                                YouTube
                            </a>
                            @endif
                            
                            <a href="{{ route('episodes.show', ['locale' => app()->getLocale(), 'slug' => $episode->slug]) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-2 rounded-lg transition-all duration-300 text-center font-medium shadow-md hover:shadow-lg transform hover:scale-105">
                                {{ __('Voir plus') }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $episodes->appends(request()->query())->links() }}
            </div>

        @else
            <!-- État vide -->
            <div class="text-center py-16">
                <div class="mx-auto h-24 w-24 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center shadow-lg mb-6">
                    <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">
                    @if($search ?? '')
                        {{ __('Aucun résultat trouvé') }}
                    @else
                        {{ __('Aucun épisode disponible') }}
                    @endif
                </h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    @if($search ?? '')
                        {{ __('Essayez de modifier vos critères de recherche ou explorez nos autres épisodes.') }}
                    @else
                        {{ __('Nos épisodes arrivent bientôt ! Revenez plus tard pour découvrir nos astuces.') }}
                    @endif
                </p>
                
                @if(($search ?? '') || ($typeFilter ?? 'all') !== 'all')
                <a href="{{ route('episodes.index', ['locale' => app()->getLocale()]) }}" 
                   class="btn-primary">
                    {{ __('Voir tous les épisodes') }}
                </a>
                @endif
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
// Fonction pour jouer une vidéo YouTube
function playYouTubeVideo(videoId, element) {
    if (!videoId) {
        console.error('Video ID is required');
        return;
    }
    
    // Créer l'iframe YouTube
    const iframe = document.createElement('iframe');
    iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
    iframe.width = '100%';
    iframe.height = '100%';
    iframe.frameBorder = '0';
    iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
    iframe.allowFullscreen = true;
    iframe.className = 'absolute inset-0 w-full h-full rounded-xl';
    
    // Remplacer le contenu de l'élément par l'iframe
    element.innerHTML = '';
    element.appendChild(iframe);
    
    // Ajouter une classe pour indiquer que la vidéo est en cours de lecture
    element.classList.add('playing-video');
}

// Auto-submit du formulaire lors du changement de filtres
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.querySelector('select[name="type"]');
    const sortSelect = document.querySelector('select[name="sort"]');
    
    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>
@endpush 