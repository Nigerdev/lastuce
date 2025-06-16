@extends('layouts.app')

@section('title', 'Blog - L\'Astuce')
@section('description', 'Découvrez nos derniers articles de blog')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6">Blog</h1>
    
    <!-- Search Bar -->
    <div class="mb-6">
        <form method="GET" action="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" class="max-w-md">
            <div class="flex">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}"
                       placeholder="Rechercher un article..."
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-r-lg hover:bg-purple-700">
                    Rechercher
                </button>
            </div>
        </form>
    </div>
    
    <!-- Results Info -->
    <div class="mb-6">
        <p class="text-gray-600">
            {{ $articles->total() }} article(s) trouvé(s)
            @if($search ?? false)
                pour "{{ $search }}"
            @endif
        </p>
        @if($search ?? false)
            <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" 
               class="text-purple-600 hover:text-purple-700 text-sm">
                Effacer la recherche
            </a>
        @endif
    </div>
    
    <!-- Articles Grid -->
    @if($articles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($articles as $article)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6">
                    @if($article->image)
                        <div class="mb-4">
                            <img src="{{ $article->image }}" 
                                 alt="{{ $article->titre }}"
                                 class="w-full h-48 object-cover rounded-lg">
                        </div>
                    @endif
                    
                    <h3 class="text-lg font-semibold mb-2 text-gray-800">
                        <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'slug' => $article->slug]) }}" 
                           class="hover:text-purple-600 transition-colors">
                            {{ $article->titre }}
                        </a>
                    </h3>
                    
                    @if($article->extrait)
                        <p class="text-gray-600 mb-4">
                            {{ Str::limit(strip_tags($article->extrait), 120) }}
                        </p>
                    @endif
                    
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">
                            {{ $article->is_published ? 'Publié' : 'Brouillon' }}
                        </span>
                        @if($article->date_publication)
                            <span>{{ $article->date_publication->format('d/m/Y') }}</span>
                        @else
                            <span>{{ $article->created_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $articles->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun article trouvé</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search ?? false)
                        Aucun article ne correspond à votre recherche.
                    @else
                        Aucun article n'est disponible pour le moment.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('episodes.index', ['locale' => app()->getLocale()]) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        Voir les épisodes
                    </a>
                </div>
            </div>
        </div>
    @endif
    
    <!-- CTA Section -->
    <div class="mt-12 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg p-8 text-white text-center">
        <h2 class="text-2xl font-bold mb-4">Restez informé</h2>
        <p class="text-purple-100 mb-6">
            Découvrez nos derniers articles et conseils pratiques pour améliorer votre quotidien.
        </p>
        <a href="{{ route('newsletter.subscribe', ['locale' => app()->getLocale()]) }}" 
           class="inline-flex items-center px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
            📧 S'abonner à la newsletter
        </a>
    </div>
</div>
@endsection 