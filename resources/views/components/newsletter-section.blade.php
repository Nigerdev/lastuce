@props([
    'title' => null,
    'description' => null,
    'placeholder' => null,
    'buttonText' => null,
    'variant' => 'default', // default, compact, inline
    'showIcon' => true,
    'backgroundClass' => 'bg-astuce-600'
])

@php
    $title = $title ?? __('components.newsletter.title');
    $description = $description ?? __('components.newsletter.description');
    $placeholder = $placeholder ?? __('components.newsletter.placeholder');
    $buttonText = $buttonText ?? __('components.newsletter.button_text');
@endphp

@if($variant === 'compact')
    <!-- Version compacte pour footer -->
    <div class="space-y-4">
        <h4 class="text-lg font-semibold text-white">{{ $title }}</h4>
                        <form action="{{ route('newsletter.subscribe', ['locale' => app()->getLocale()]) }}" method="POST" class="flex space-x-2">
            @csrf
            <input type="email" 
                   name="email" 
                   placeholder="{{ $placeholder }}" 
                   required
                   class="flex-1 px-4 py-2 rounded-lg border-0 text-gray-900 focus:ring-2 focus:ring-astuce-300 form-input-astuce">
            <button type="submit" 
                    class="btn-astuce whitespace-nowrap">
                {{ $buttonText }}
            </button>
        </form>
    </div>

@elseif($variant === 'inline')
    <!-- Version inline pour intégrer dans du contenu -->
    <div class="bg-gray-50 border border-astuce-200 rounded-xl p-6">
        <div class="flex items-center space-x-4">
            @if($showIcon)
                <div class="flex-shrink-0">
                    <div class="bg-astuce-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-astuce-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a1 1 0 001.42 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            @endif
            
            <div class="flex-1">
                <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ $title }}</h4>
                <p class="text-gray-600 text-sm mb-3">{{ $description }}</p>
                
                <form action="{{ route('newsletter.subscribe', ['locale' => app()->getLocale()]) }}" method="POST" class="flex space-x-3">
                    @csrf
                    <input type="email" 
                           name="email" 
                           placeholder="{{ $placeholder }}" 
                           required
                           class="flex-1 form-input-astuce">
                    <button type="submit" class="btn-astuce">
                        {{ $buttonText }}
                    </button>
                </form>
            </div>
        </div>
    </div>

@else
    <!-- Version par défaut - Section complète -->
    <section class="{{ $backgroundClass }} text-white section-padding">
        <div class="container-astuce">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Icon -->
                @if($showIcon)
                    <div class="mx-auto mb-8 w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a1 1 0 001.42 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
                
                <!-- Title & Description -->
                <h2 class="text-3xl md:text-4xl font-bold mb-6 text-shadow">
                    {{ $title }}
                </h2>
                
                <p class="text-xl mb-8 text-astuce-100 max-w-2xl mx-auto">
                    {{ $description }}
                </p>
                
                <!-- Newsletter Form -->
                <form action="{{ route('newsletter.subscribe', ['locale' => app()->getLocale()]) }}" method="POST" class="max-w-md mx-auto">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <input type="email" 
                                   name="email" 
                                   placeholder="{{ $placeholder }}" 
                                   required
                                   class="w-full px-6 py-4 rounded-lg border-0 text-gray-900 focus:ring-2 focus:ring-white focus:ring-opacity-50 text-lg">
                        </div>
                        
                        <button type="submit" 
                                class="bg-white text-astuce-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-semibold text-lg transition duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                            {{ $buttonText }}
                            <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </form>
                
                <!-- Privacy Notice -->
                <p class="text-sm text-astuce-200 mt-6">
                    🔒 {{ __('components.newsletter.privacy_notice') }}
                </p>
                
                <!-- Stats (optional) -->
                <div class="mt-12 grid grid-cols-3 gap-8 max-w-lg mx-auto">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-white mb-1">5K+</div>
                        <div class="text-sm text-astuce-200">{{ __('components.newsletter.subscribers') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-white mb-1">50+</div>
                        <div class="text-sm text-astuce-200">{{ __('components.newsletter.episodes') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-white mb-1">⭐ 4.9</div>
                        <div class="text-sm text-astuce-200">{{ __('components.newsletter.average_rating') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif 