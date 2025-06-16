@extends('layouts.app')

@section('title', ($episode->titre ?? 'Épisode') . ' - L\'Astuce')
@section('description', $episode->description ?? __('episodes.meta_description'))

@push('meta')
<!-- Open Graph / Facebook -->
<meta property="og:type" content="video.other">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="{{ $episode->titre ?? 'Épisode' }} - L'Astuce">
<meta property="og:description" content="{{ $episode->description ?? __('episodes.meta_description') }}">
<meta property="og:image" content="{{ $episode->thumbnail_url ?? asset('images/default-thumbnail.jpg') }}">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ url()->current() }}">
<meta property="twitter:title" content="{{ $episode->titre ?? 'Épisode' }} - L'Astuce">
<meta property="twitter:description" content="{{ $episode->description ?? __('episodes.meta_description') }}">
<meta property="twitter:image" content="{{ $episode->thumbnail_url ?? asset('images/default-thumbnail.jpg') }}">

<!-- Video specific -->
@if(isset($episode->youtube_url))
<meta property="og:video" content="{{ $episode->youtube_url }}">
<meta property="og:video:type" content="text/html">
<meta property="og:video:width" content="1280">
<meta property="og:video:height" content="720">
@endif
@endpush

@section('content')
<div class="min-h-screen bg-slate-900">
    <!-- Breadcrumb -->
    <section class="bg-slate-800 border-b border-slate-700">
        <div class="container-astuce py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-gray-400 hover:text-white transition-colors">
                    {{ __('navigation.home') }}
                </a>
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('episodes.index', ['locale' => app()->getLocale()]) }}" class="text-gray-400 hover:text-white transition-colors">
                    {{ __('episodes.title') }}
                </a>
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-white font-medium">{{ $episode->titre ?? 'Épisode' }}</span>
            </nav>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section-padding">
        <div class="container-astuce">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Video Player and Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Video Player -->
                    <div class="relative">
                        @if(isset($episode->youtube_url) && $episode->youtube_url)
                            @php
                                // Extract YouTube video ID
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $episode->youtube_url, $matches);
                                $videoId = $matches[1] ?? null;
                            @endphp
                            
                            @if($videoId)
                                <div class="aspect-video bg-black rounded-xl overflow-hidden shadow-2xl">
                                    <iframe 
                                        src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&modestbranding=1&playsinline=1"
                                        title="{{ $episode->titre }}"
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen
                                        class="w-full h-full"
                                        loading="lazy">
                                    </iframe>
                                </div>
                            @else
                                <!-- Fallback if YouTube URL is invalid -->
                                <div class="aspect-video bg-gradient-to-br from-gray-700 to-gray-900 rounded-xl flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                        <p class="text-lg font-medium">{{ __('episodes.detail.video_not_available') }}</p>
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Placeholder if no video URL -->
                            <div class="aspect-video bg-gradient-to-br from-gray-700 to-gray-900 rounded-xl flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-medium">{{ __('episodes.detail.video_not_available') }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Video Actions -->
                        <div class="absolute top-4 right-4 flex space-x-2">
                            @if(isset($episode->youtube_url) && $episode->youtube_url)
                                <a href="{{ $episode->youtube_url }}" target="_blank" 
                                   class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                                   title="{{ __('episodes.detail.watch_on_youtube') }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                </a>
                            @endif
                            
                            <button onclick="shareEpisode()" 
                                    class="p-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors"
                                    title="{{ __('episodes.detail.share_episode') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Episode Title and Metadata -->
                    <div class="space-y-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">
                                    {{ $episode->titre ?? 'Titre de l\'épisode' }}
                                </h1>
                                
                                <!-- Type Badge -->
                                @php
                                    $typeColors = [
                                        'episode' => 'bg-blue-600',
                                        'tutorial' => 'bg-green-600', 
                                        'behind_scenes' => 'bg-purple-600',
                                        'live' => 'bg-red-600'
                                    ];
                                    $bgColor = $typeColors[$episode->type ?? 'episode'] ?? 'bg-gray-600';
                                @endphp
                                <span class="{{ $bgColor }} text-white text-sm px-3 py-1 rounded-full font-medium">
                                    {{ __('episodes.types.' . ($episode->type ?? 'episode')) }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Episode Metadata -->
                        <div class="flex flex-wrap items-center gap-6 text-sm text-gray-400">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ __('episodes.detail.published_on') }}: </span>
                                <span class="text-white ml-1">
                                    @if(isset($episode->created_at))
                                        {{ $episode->created_at->format('d F Y') }}
                                    @else
                                        {{ now()->subDays(rand(1, 30))->format('d F Y') }}
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('episodes.detail.duration') }}: </span>
                                <span class="text-white ml-1">
                                    @if(isset($episode->duree))
                                        {{ gmdate('H:i:s', $episode->duree) }}
                                    @else
                                        {{ sprintf('%02d:%02d', rand(10,45), rand(10,59)) }}
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span class="text-white">{{ number_format($episode->vues ?? rand(1000, 50000)) }} {{ __('episodes.views') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Episode Description -->
                    <div class="card-glass">
                        <h2 class="text-xl font-bold text-white mb-4">{{ __('episodes.detail.description') }}</h2>
                        <div class="prose prose-invert max-w-none">
                            @if(isset($episode->description) && $episode->description)
                                <p class="text-gray-300 leading-relaxed">{{ $episode->description }}</p>
                            @else
                                <p class="text-gray-300 leading-relaxed">
                                    Découvrez cette astuce incroyable qui va révolutionner votre quotidien et vous faire gagner du temps. Dans cet épisode, nous explorons des techniques pratiques et faciles à appliquer pour améliorer votre efficacité au quotidien.
                                </p>
                                <p class="text-gray-300 leading-relaxed mt-4">
                                    Que vous soyez débutant ou expert, ces conseils vous aideront à optimiser votre temps et à simplifier vos tâches quotidiennes. N'hésitez pas à partager vos propres astuces en commentaire !
                                </p>
                            @endif
                        </div>
                        
                        <!-- Tags -->
                        @if(isset($episode->tags) && $episode->tags->count() > 0)
                            <div class="mt-6">
                                <h3 class="text-sm font-medium text-gray-400 mb-3">{{ __('episodes.detail.tags') }}:</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($episode->tags as $tag)
                                        <span class="text-xs bg-slate-700 text-gray-300 px-3 py-1 rounded-full hover:bg-slate-600 transition-colors cursor-pointer">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <!-- Demo tags -->
                            <div class="mt-6">
                                <h3 class="text-sm font-medium text-gray-400 mb-3">{{ __('episodes.detail.tags') }}:</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['astuce', 'productivité', 'quotidien', 'pratique', 'efficacité'] as $tag)
                                        <span class="text-xs bg-slate-700 text-gray-300 px-3 py-1 rounded-full hover:bg-slate-600 transition-colors cursor-pointer">
                                            #{{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4">
                        <button onclick="toggleWatchLater({{ $episode->id ?? 1 }})" 
                                class="btn-secondary flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('episodes.actions.watch_later') }}
                        </button>
                        
                        <button onclick="addToPlaylist({{ $episode->id ?? 1 }})" 
                                class="btn-secondary flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            {{ __('episodes.detail.add_to_playlist') }}
                        </button>
                        
                        <button onclick="reportIssue({{ $episode->id ?? 1 }})" 
                                class="text-gray-400 hover:text-red-400 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            {{ __('episodes.detail.report_issue') }}
                        </button>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Suggested Episodes -->
                    <div class="card-glass">
                        <h2 class="text-xl font-bold text-white mb-6">{{ __('episodes.detail.suggested_episodes') }}</h2>
                        <div class="space-y-4">
                            @for($i = 1; $i <= 5; $i++)
                                <div class="flex items-start space-x-3 group cursor-pointer hover:bg-slate-700/50 p-3 rounded-lg transition-colors"
                                     onclick="window.location.href='@localizedRoute('episodes.show', ['episode' => $i])'">
                                    <div class="flex-shrink-0">
                                        <div class="w-20 aspect-video bg-gradient-to-br from-gray-700 to-gray-900 rounded-lg overflow-hidden relative">
                                            <div class="w-full h-full flex items-center justify-center">
                                                <div class="w-4 h-4 bg-white/20 rounded-full flex items-center justify-center">
                                                    <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M8 5v14l11-7z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="absolute bottom-1 right-1 bg-black/70 text-white text-xs px-1 rounded">
                                                {{ sprintf('%02d:%02d', rand(5,25), rand(10,59)) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-white line-clamp-2 group-hover:text-blue-400 transition-colors">
                                            {{ __('home.sample_episode_title') }} {{ $i + 10 }}
                                        </h3>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ number_format(rand(1000, 50000)) }} {{ __('episodes.views') }}
                                        </p>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        
                        <div class="mt-6">
                            <a href="@localizedRoute('episodes.index')" class="btn-primary w-full text-center">
                                {{ __('episodes.view_all_episodes') }}
                            </a>
                        </div>
                    </div>
                    
                    <!-- Newsletter CTA -->
                    <div class="card-glass bg-gradient-to-br from-blue-600/20 to-purple-600/20 border-blue-500/30">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">{{ __('components.newsletter.title') }}</h3>
                            <p class="text-sm text-gray-300 mb-4">{{ __('components.newsletter.description') }}</p>
                            <a href="@localizedRoute('newsletter.subscribe')" class="btn-primary w-full">
                                {{ __('components.newsletter.subscribe_button') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
function shareEpisode() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $episode->titre ?? "Épisode" }} - L\'Astuce',
            text: '{{ $episode->description ?? "Découvrez cet épisode incroyable sur L\'Astuce" }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('{{ __('episodes.actions.link_copied') }}');
        });
    }
}

function toggleWatchLater(episodeId) {
    // Implementation for watch later functionality
    console.log('Toggle watch later for episode:', episodeId);
    showToast('{{ __('episodes.actions.added_to_watch_later') }}');
}

function addToPlaylist(episodeId) {
    // Implementation for add to playlist
    console.log('Add to playlist:', episodeId);
    showToast('{{ __('episodes.actions.added_to_playlist') }}');
}

function reportIssue(episodeId) {
    // Implementation for reporting issues
    if (confirm('{{ __('episodes.actions.confirm_report') }}')) {
        console.log('Report issue for episode:', episodeId);
        showToast('{{ __('episodes.actions.issue_reported') }}');
    }
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform translate-y-0 transition-transform duration-300';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('translate-y-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endpush 