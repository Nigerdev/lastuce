@extends('layouts.app')

@section('title', __('Astuces et conseils pratiques'))
@section('description', __('Découvrez nos meilleures astuces et conseils pratiques pour simplifier votre quotidien.'))

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">{{ __('Astuces et Conseils') }}</h1>
                <p class="text-xl text-blue-100 mb-8">{{ __('Découvrez nos meilleures astuces pour simplifier votre quotidien') }}</p>
                
                <!-- Search Form -->
                <div class="max-w-md mx-auto">
                    <form method="GET" action="{{ route('astuces.index', ['locale' => app()->getLocale()]) }}" class="flex">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="{{ __('Rechercher une astuce...') }}"
                               class="flex-1 px-4 py-2 rounded-l-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-500 hover:bg-blue-600 rounded-r-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar -->
            <div class="lg:w-1/4">
                <!-- Categories -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Catégories') }}</h3>
                    <div class="space-y-2">
                        <a href="{{ route('astuces.index', ['locale' => app()->getLocale()]) }}" 
                           class="block px-3 py-2 rounded {{ $category === 'all' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                            {{ __('Toutes les astuces') }}
                        </a>
                        @foreach(['cuisine', 'menage', 'bricolage', 'beaute', 'organisation', 'jardinage', 'economie', 'technologie', 'sante', 'autre'] as $cat)
                            <a href="{{ route('astuces.index', ['locale' => app()->getLocale(), 'category' => $cat]) }}" 
                               class="block px-3 py-2 rounded {{ $category === $cat ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                                {{ __(ucfirst($cat)) }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Popular Tips -->
                @if($astucesPopulaires->count() > 0)
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Astuces populaires') }}</h3>
                    <div class="space-y-3">
                        @foreach($astucesPopulaires as $populaire)
                            <a href="{{ route('astuces.show', ['locale' => app()->getLocale(), 'id' => $populaire->id]) }}" 
                               class="block text-sm text-gray-600 hover:text-blue-600 line-clamp-2">
                                {{ $populaire->titre_astuce }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Main Content -->
            <div class="lg:w-3/4">
                <!-- Results Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            @if($search)
                                {{ __('Résultats pour') }} "{{ $search }}"
                            @elseif($category !== 'all')
                                {{ __('Astuces') }} - {{ __(ucfirst($category)) }}
                            @else
                                {{ __('Toutes les astuces') }}
                            @endif
                        </h2>
                        <p class="text-gray-600 mt-1">{{ $astuces->total() }} {{ __('astuce(s) trouvée(s)') }}</p>
                    </div>
                    
                    <a href="{{ route('astuces.create', ['locale' => app()->getLocale()]) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        {{ __('Proposer une astuce') }}
                    </a>
                </div>

                <!-- Tips Grid -->
                @if($astuces->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        @foreach($astuces as $astuce)
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ __(ucfirst($astuce->categorie ?? 'autre')) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $astuce->created_at ? $astuce->created_at->format('d/m/Y') : '' }}
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                    <a href="{{ route('astuces.show', ['locale' => app()->getLocale(), 'id' => $astuce->id]) }}" 
                                       class="hover:text-blue-600 transition-colors">
                                        {{ $astuce->titre_astuce }}
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                    {{ Str::limit(strip_tags($astuce->description), 150) }}
                                </p>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">
                                        {{ __('Par') }} {{ $astuce->nom ?? 'Anonyme' }}
                                    </span>
                                    <a href="{{ route('astuces.show', ['locale' => app()->getLocale(), 'id' => $astuce->id]) }}" 
                                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        {{ __('Lire la suite') }} →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-center">
                        {{ $astuces->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Aucune astuce trouvée') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if($search)
                                {{ __('Essayez avec d\'autres mots-clés ou') }}
                                <a href="{{ route('astuces.index', ['locale' => app()->getLocale()]) }}" class="text-blue-600 hover:text-blue-700">
                                    {{ __('voir toutes les astuces') }}
                                </a>
                            @else
                                {{ __('Soyez le premier à partager une astuce !') }}
                            @endif
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('astuces.create', ['locale' => app()->getLocale()]) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                {{ __('Proposer une astuce') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 