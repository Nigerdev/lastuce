<div class="card-glass hover:scale-[1.02] transition-all duration-300 cursor-pointer" 
     onclick="window.location.href='@localizedRoute('episodes.show', ['episode' => $episode->id])'">
    <div class="flex items-center space-x-6">
        <!-- Thumbnail -->
        <div class="flex-shrink-0">
            <div class="relative w-40 md:w-48 overflow-hidden rounded-lg">
                <div class="aspect-video bg-gradient-to-br from-gray-700 to-gray-900 relative">
                    @if(isset($episode->thumbnail_url) && $episode->thumbnail_url)
                        <img src="{{ $episode->thumbnail_url }}" 
                             alt="{{ $episode->titre }}"
                             class="w-full h-full object-cover"
                             loading="lazy">
                    @else
                        <!-- Default placeholder -->
                        <div class="w-full h-full flex items-center justify-center">
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center group-hover:bg-white/30 transition-colors">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Play overlay -->
                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Duration -->
                <div class="absolute bottom-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded font-medium">
                    @if(isset($episode->duree))
                        {{ gmdate('H:i:s', $episode->duree) }}
                    @else
                        {{ sprintf('%02d:%02d', rand(5,25), rand(10,59)) }}
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="flex-1 min-w-0 space-y-3">
            <!-- Header with title and type -->
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <h3 class="text-xl font-bold text-white line-clamp-2 group-hover:text-blue-400 transition-colors leading-tight">
                        {{ $episode->titre ?? 'Episode Title' }}
                    </h3>
                </div>
                
                <!-- Type Badge -->
                <div class="flex-shrink-0 ml-4">
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
            </div>
            
            <!-- Description -->
            <p class="text-gray-400 line-clamp-2 leading-relaxed">
                {{ $episode->description ?? 'Episode description goes here and provides more details about the content...' }}
            </p>
            
            <!-- Metadata Row -->
            <div class="flex items-center justify-between text-sm text-gray-500">
                <div class="flex items-center space-x-6">
                    <!-- Views -->
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>{{ number_format($episode->vues ?? rand(1000, 50000)) }} {{ __('episodes.views') }}</span>
                    </div>
                    
                    <!-- Date -->
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>
                            @if(isset($episode->created_at))
                                {{ $episode->created_at->diffForHumans() }}
                            @else
                                {{ __('time.days_ago', ['count' => rand(1, 30)]) }}
                            @endif
                        </span>
                    </div>
                    
                    <!-- Duration -->
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>
                            @if(isset($episode->duree))
                                {{ gmdate('i:s', $episode->duree) }} {{ __('episodes.minutes') }}
                            @else
                                {{ rand(5, 25) }}:{{ sprintf('%02d', rand(10,59)) }} {{ __('episodes.minutes') }}
                            @endif
                        </span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-2">
                    <!-- Watch Later -->
                    <button class="p-2 rounded-lg bg-slate-700 text-gray-300 hover:bg-slate-600 hover:text-white transition-colors"
                            onclick="event.stopPropagation(); toggleWatchLater({{ $episode->id }})"
                            title="{{ __('episodes.actions.watch_later') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    
                    <!-- Share -->
                    <button class="p-2 rounded-lg bg-slate-700 text-gray-300 hover:bg-slate-600 hover:text-white transition-colors"
                            onclick="event.stopPropagation(); shareEpisode({{ $episode->id }})"
                            title="{{ __('episodes.actions.share') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                    </button>
                    
                    <!-- More Options -->
                    <button class="p-2 rounded-lg bg-slate-700 text-gray-300 hover:bg-slate-600 hover:text-white transition-colors"
                            onclick="event.stopPropagation(); showEpisodeOptions({{ $episode->id }})"
                            title="{{ __('episodes.actions.more_options') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Tags (if available) -->
            @if(isset($episode->tags) && $episode->tags->count() > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($episode->tags->take(5) as $tag)
                        <span class="text-xs bg-slate-700 text-gray-300 px-2 py-1 rounded hover:bg-slate-600 transition-colors cursor-pointer">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                    @if($episode->tags->count() > 5)
                        <span class="text-xs text-gray-500">
                            +{{ $episode->tags->count() - 5 }} {{ __('episodes.more_tags') }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- New Badge -->
        @if(isset($episode->created_at) && $episode->created_at->diffInDays(now()) <= 7)
            <div class="absolute top-4 left-4 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-xs px-2 py-1 rounded-full font-medium animate-pulse">
                {{ __('episodes.badges.new') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function showEpisodeOptions(episodeId) {
    // Implementation for showing episode options menu
    console.log('Show options for episode:', episodeId);
    // You could show a dropdown menu with additional options
}
</script>
@endpush 