<div class="card-glass group hover:scale-105 transition-all duration-300 cursor-pointer" 
     onclick="window.location.href='@localizedRoute('episodes.show', ['episode' => $episode->id])'">
    <!-- Thumbnail Container -->
    <div class="relative mb-4 overflow-hidden rounded-xl">
        <!-- Video Thumbnail -->
        <div class="aspect-video bg-gradient-to-br from-gray-700 to-gray-900 relative">
            @if(isset($episode->thumbnail_url) && $episode->thumbnail_url)
                <img src="{{ $episode->thumbnail_url }}" 
                     alt="{{ $episode->titre }}"
                     class="w-full h-full object-cover"
                     loading="lazy">
            @else
                <!-- Default placeholder -->
                <div class="w-full h-full flex items-center justify-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center group-hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
            @endif
            
            <!-- Overlay on hover -->
            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Type Badge -->
        <div class="absolute top-3 left-3">
            @php
                $typeColors = [
                    'episode' => 'bg-blue-600',
                    'tutorial' => 'bg-green-600', 
                    'behind_scenes' => 'bg-purple-600',
                    'live' => 'bg-red-600'
                ];
                $bgColor = $typeColors[$episode->type] ?? 'bg-gray-600';
            @endphp
            <span class="{{ $bgColor }} text-white text-xs px-2 py-1 rounded-full font-medium">
                {{ __('episodes.types.' . $episode->type) }}
            </span>
        </div>
        
        <!-- Duration -->
        <div class="absolute bottom-3 right-3 bg-black/70 text-white text-xs px-2 py-1 rounded font-medium">
            @if(isset($episode->duree))
                {{ gmdate('H:i:s', $episode->duree) }}
            @else
                {{ sprintf('%02d:%02d', rand(5,25), rand(10,59)) }}
            @endif
        </div>
        
        <!-- New Badge -->
        @if(isset($episode->created_at) && $episode->created_at->diffInDays(now()) <= 7)
            <div class="absolute top-3 right-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-xs px-2 py-1 rounded-full font-medium animate-pulse">
                {{ __('episodes.badges.new') }}
            </div>
        @endif
    </div>
    
    <!-- Episode Info -->
    <div class="space-y-3">
        <!-- Title -->
        <h3 class="text-lg font-bold text-white line-clamp-2 group-hover:text-blue-400 transition-colors leading-tight">
            {{ $episode->titre ?? 'Episode Title' }}
        </h3>
        
        <!-- Description -->
        <p class="text-gray-400 text-sm line-clamp-2 leading-relaxed">
            {{ $episode->description ?? 'Episode description goes here...' }}
        </p>
        
        <!-- Metadata -->
        <div class="flex items-center justify-between text-xs text-gray-500">
            <!-- Views -->
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <span>{{ number_format($episode->vues ?? rand(1000, 50000)) }}</span>
            </div>
            
            <!-- Date -->
            <span>
                @if(isset($episode->created_at))
                    {{ $episode->created_at->diffForHumans() }}
                @else
                    {{ __('time.days_ago', ['count' => rand(1, 30)]) }}
                @endif
            </span>
        </div>
        
        <!-- Tags (if available) -->
        @if(isset($episode->tags) && $episode->tags->count() > 0)
            <div class="flex flex-wrap gap-1">
                @foreach($episode->tags->take(3) as $tag)
                    <span class="text-xs bg-slate-700 text-gray-300 px-2 py-1 rounded">
                        #{{ $tag->name }}
                    </span>
                @endforeach
                @if($episode->tags->count() > 3)
                    <span class="text-xs text-gray-500">
                        +{{ $episode->tags->count() - 3 }}
                    </span>
                @endif
            </div>
        @endif
    </div>
    
    <!-- Action Buttons (visible on hover) -->
    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <div class="flex space-x-2">
            <!-- Watch Later -->
            <button class="w-8 h-8 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-black/70 transition-colors"
                    onclick="event.stopPropagation(); toggleWatchLater({{ $episode->id }})"
                    title="{{ __('episodes.actions.watch_later') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </button>
            
            <!-- Share -->
            <button class="w-8 h-8 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-black/70 transition-colors"
                    onclick="event.stopPropagation(); shareEpisode({{ $episode->id }})"
                    title="{{ __('episodes.actions.share') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleWatchLater(episodeId) {
    // Implementation for watch later functionality
    console.log('Toggle watch later for episode:', episodeId);
    // You would implement this with your backend API
}

function shareEpisode(episodeId) {
    // Implementation for share functionality
    if (navigator.share) {
        navigator.share({
            title: '{{ $episode->titre ?? "Episode" }}',
            url: window.location.origin + '@localizedRoute('episodes.show', ['episode' => $episode->id])'
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        const url = window.location.origin + '@localizedRoute('episodes.show', ['episode' => $episode->id])';
        navigator.clipboard.writeText(url).then(() => {
            // Show a toast notification
            showToast('{{ __('episodes.actions.link_copied') }}');
        });
    }
}

function showToast(message) {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endpush 