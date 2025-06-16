@extends('layouts.app')

@section('title', $astuce->titre . ' - ' . __('app.nav.tips'))
@section('description', Str::limit(strip_tags($astuce->description), 160))

@section('content')
<!-- Breadcrumb -->
<nav class="bg-gray-50 py-4">
    <div class="container-astuce">
        <ol class="flex items-center space-x-2 text-sm">
            <li><a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-gray-500 hover:text-gray-700">{{ __('app.nav.home') }}</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="{{ route('astuces.index', ['locale' => app()->getLocale()]) }}" class="text-gray-500 hover:text-gray-700">{{ __('app.nav.tips') }}</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li class="text-gray-900 font-medium">{{ Str::limit($astuce->titre, 50) }}</li>
        </ol>
    </div>
</nav>

<!-- Main Content -->
<article class="py-12">
    <div class="container-astuce">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Main Article -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-8">
                    <!-- Header -->
                    <header class="mb-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
                                @php
                                $categoryLabels = [
                                    'cuisine' => 'Cuisine',
                                    'menage' => 'Ménage',
                                    'bricolage' => 'Bricolage',
                                    'beaute' => 'Beauté',
                                    'organisation' => 'Organisation',
                                    'jardinage' => 'Jardinage',
                                    'economie' => 'Économies',
                                    'technologie' => 'Technologie',
                                    'sante' => 'Santé',
                                    'autre' => 'Autre'
                                ];
                                @endphp
                                {{ $categoryLabels[$astuce->categorie] ?? ucfirst($astuce->categorie) }}
                            </span>
                            <span class="text-gray-500 text-sm">
                                {{ $astuce->date_soumission->format('d/m/Y') }}
                            </span>
                        </div>
                        
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                            {{ $astuce->titre }}
                        </h1>
                        
                        <div class="flex items-center text-gray-600 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ __('Partagé par') }} <strong class="ml-1">{{ $astuce->nom_soumetteur }}</strong>
                        </div>
                    </header>
                    
                    <!-- Content -->
                    <div class="prose prose-lg max-w-none">
                        <div class="text-gray-700 leading-relaxed">
                            {!! nl2br(e($astuce->description)) !!}
                        </div>
                    </div>
                    
                    <!-- Media Attachments -->
                    @if($astuce->media && $astuce->media->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Fichiers joints') }}</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($astuce->media as $media)
                                @if(in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/gif']))
                                    <div class="relative group">
                                        <img src="{{ $media->getUrl() }}" 
                                             alt="{{ $media->name }}"
                                             class="w-full h-32 object-cover rounded-lg">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-opacity rounded-lg"></div>
                                    </div>
                                @else
                                    <a href="{{ $media->getUrl() }}" 
                                       target="_blank"
                                       class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700 truncate">{{ $media->name }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Share Section -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Partager cette astuce') }}</h3>
                        <div class="flex space-x-4">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                               target="_blank"
                               class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($astuce->titre) }}&url={{ urlencode(request()->url()) }}" 
                               target="_blank"
                               class="flex items-center px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                                Twitter
                            </a>
                            <button onclick="copyToClipboard('{{ request()->url() }}')" 
                                    class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                {{ __('Copier le lien') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Similar Tips -->
                @if($astucesSimilaires->count() > 0)
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Astuces similaires') }}</h3>
                    <div class="space-y-4">
                        @foreach($astucesSimilaires as $similaire)
                        <a href="{{ route('astuces.show', ['locale' => app()->getLocale(), 'id' => $similaire->id]) }}" 
                           class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <h4 class="font-medium text-sm text-gray-900 line-clamp-2 mb-2">{{ $similaire->titre }}</h4>
                            <p class="text-xs text-gray-600 line-clamp-2">{{ Str::limit(strip_tags($similaire->description), 80) }}</p>
                            <span class="text-xs text-blue-600 mt-2 inline-block">{{ $categoryLabels[$similaire->categorie] ?? ucfirst($similaire->categorie) }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- CTA -->
                <div class="bg-gradient-to-br from-blue-600 to-purple-600 text-white rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-2">{{ __('Vous aussi, partagez !') }}</h3>
                    <p class="text-blue-100 text-sm mb-4">{{ __('Avez-vous une astuce à partager avec notre communauté ?') }}</p>
                    <a href="{{ route('astuces.create', ['locale' => app()->getLocale()]) }}" 
                       class="bg-white text-blue-600 hover:bg-gray-100 px-4 py-2 rounded-lg font-medium text-sm transition-colors inline-flex items-center">
                        💡 {{ __('Proposer une astuce') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</article>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Afficher une notification de succès
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>{{ __("Copié !") }}';
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 2000);
    });
}
</script>
@endsection 