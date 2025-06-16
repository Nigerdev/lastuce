@extends('layouts.admin')

@section('title', 'Gestion des Épisodes')

@push('styles')
<style>
.modal {
    transition: opacity 0.25s ease;
}
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}
.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}
.status-badge {
    transition: all 0.2s ease;
}
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 overflow-hidden shadow-xl rounded-xl">
        <div class="px-6 py-8 sm:p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold ">Gestion des Épisodes</h1>
                    <p class="mt-2 text-purple-100">
                        Créez, modifiez et gérez vos épisodes de podcast avec style.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openCreateModal()" class="btn-primary inline-flex items-center px-6 py-3 text-sm font-medium rounded-lg  shadow-lg hover:shadow-xl transition-all duration-300">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nouvel Épisode
                    </button>
                    <button onclick="testFunctions()" class="inline-flex items-center px-4 py-3 border border-white/20 text-sm font-medium rounded-lg  hover:bg-white/10 transition-all duration-300">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        Test Debug
                    </button>
                    <button onclick="openBulkModal()" class="inline-flex items-center px-4 py-3 border border-white/20 text-sm font-medium rounded-lg  hover:bg-white/10 transition-all duration-300">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        Actions groupées
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-100">
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('admin.episodes.index') }}" class="flex items-center gap-4 flex-wrap">
                <div class="flex-shrink-0">
                    <select name="status" id="status" class="rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publié</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Programmé</option>
                    </select>
                </div>
                <div class="flex-1 min-w-0">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Rechercher par titre ou description..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                </div>
                <div class="flex-shrink-0">
                    <select name="sort_by" id="sort_by" class="rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                        <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date de création</option>
                        <option value="titre" {{ request('sort_by') === 'titre' ? 'selected' : '' }}>Titre</option>
                        <option value="statut" {{ request('sort_by') === 'statut' ? 'selected' : '' }}>Statut</option>
                        <option value="vues" {{ request('sort_by') === 'vues' ? 'selected' : '' }}>Vues</option>
                    </select>
                </div>
                <div class="flex-shrink-0">
                    <select name="sort_order" id="sort_order" class="rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                        <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>↓ Décroissant</option>
                        <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>↑ Croissant</option>
                    </select>
                </div>
                <div class="flex-shrink-0">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg  bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 shadow-md">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filtrer
                    </button>
                </div>
                @if(request()->hasAny(['status', 'search', 'sort_by', 'sort_order']))
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.episodes.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Réinitialiser
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card-hover bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Épisodes</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-hover bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Publiés</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['published'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-hover bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Brouillons</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['draft'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-hover bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Programmés</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['scheduled'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Episodes List -->
    <div class="bg-white shadow-xl overflow-hidden rounded-xl border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900">
                    Épisodes ({{ $episodes->total() ?? 0 }})
                </h3>
                <div class="flex items-center space-x-3">
                    <input type="checkbox" id="selectAll" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <label for="selectAll" class="text-sm text-gray-600">Tout sélectionner</label>
                </div>
            </div>
        </div>

        @if(isset($episodes) && $episodes->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($episodes as $episode)
            <div class="px-6 py-5 hover:bg-gradient-to-r hover:from-purple-50 hover:to-blue-50 transition-all duration-200 group">
                <div class="flex items-center space-x-4">
                    <input type="checkbox" name="selected_episodes[]" value="{{ $episode->id }}" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded episode-checkbox">

                    @if($episode->youtube_url)
                    <div class="flex-shrink-0">
                        @php
                            // Extraire l'ID de la vidéo YouTube
                            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $episode->youtube_url, $matches);
                            $videoId = $matches[1] ?? null;
                            $thumbnailUrl = $videoId ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg" : null;
                        @endphp
                        @if($thumbnailUrl)
                        <img class="h-16 w-16 rounded-lg object-cover shadow-md" src="{{ $thumbnailUrl }}" alt="Miniature YouTube">
                        @else
                        <div class="h-16 w-16 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-md">
                            <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                    @elseif($episode->thumbnail_url)
                    <div class="flex-shrink-0">
                        <img class="h-16 w-16 rounded-lg object-cover shadow-md" src="{{ $episode->thumbnail_url }}" alt="Thumbnail">
                    </div>
                    @else
                    <div class="flex-shrink-0">
                        <div class="h-16 w-16 rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center shadow-md">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900 truncate group-hover:text-purple-700 transition-colors">
                                    {{ $episode->titre ?? 'Titre non défini' }}
                                </h4>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                    {{ Str::limit($episode->description ?? 'Aucune description', 120) }}
                                </p>
                                <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $episode->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    @if($episode->duree)
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ gmdate('H:i:s', $episode->duree) }}
                                    </span>
                                    @endif
                                    @if($episode->vues)
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ number_format($episode->vues) }} vues
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col items-end space-y-3">
                                <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $episode->statut === 'published' ? 'bg-green-100 text-green-800 border border-green-200' : '' }}
                                    {{ $episode->statut === 'draft' ? 'bg-gray-100 text-gray-800 border border-gray-200' : '' }}
                                    {{ $episode->statut === 'scheduled' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : '' }}">
                                    @if($episode->statut === 'published')
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Publié
                                    @elseif($episode->statut === 'draft')
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010 2h1.586l-2.293 2.293a1 1 0 001.414 1.414L15 8.414V10a1 1 0 002 0V6a1 1 0 00-1-1h-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Brouillon
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Programmé
                                    @endif
                                </span>

                                <div class="flex items-center space-x-2">
                                    <button onclick="viewEpisode({{ $episode->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Voir
                                    </button>
                                    <button onclick="editEpisode({{ $episode->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Modifier
                                    </button>
                                    <button onclick="deleteEpisode({{ $episode->id }}, '{{ addslashes($episode->titre) }}')" class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $episodes->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <div class="mx-auto h-24 w-24 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center shadow-lg">
                <svg class="h-12 w-12 " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="mt-6 text-lg font-medium text-gray-900">Aucun épisode trouvé</h3>
            <p class="mt-2 text-sm text-gray-500">Commencez par créer votre premier épisode pour donner vie à votre podcast.</p>
            <div class="mt-8">
                <button onclick="openCreateModal()" class="btn-primary inline-flex items-center px-6 py-3 text-sm font-medium rounded-lg  shadow-lg hover:shadow-xl transition-all duration-300">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Créer mon premier épisode
                </button>
            </div>
        </div>
        @endif
    </div>
    </div>

    <!-- Modal Créer/Modifier Épisode -->
    <div id="episodeModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-600 to-blue-600">
                <div class="flex items-center justify-between">
                    <h3 id="modalTitle" class="text-xl font-semibold ">Nouvel Épisode</h3>
                    <button onclick="closeModal('episodeModal')" class=" hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <form id="episodeForm" class="p-6 space-y-6">
                @csrf
                <input type="hidden" id="episodeId" name="id">
                <input type="hidden" id="formMethod" name="_method" value="POST">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="titre" class="block text-sm font-medium text-gray-700 mb-2">Titre de l'épisode *</label>
                        <input type="text" id="titre" name="titre" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="Entrez le titre de l'épisode">
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="Décrivez votre épisode..."></textarea>
                    </div>

                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select id="statut" name="statut" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                            <option value="draft">Brouillon</option>
                            <option value="published">Publié</option>
                            <option value="scheduled">Programmé</option>
                        </select>
                    </div>

                    <div>
                        <label for="duree" class="block text-sm font-medium text-gray-700 mb-2">Durée (en secondes)</label>
                        <input type="number" id="duree" name="duree" min="0" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="3600">
                    </div>

                    <div class="md:col-span-2">
                        <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">URL YouTube *</label>
                        <input type="url" id="youtube_url" name="youtube_url" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="https://www.youtube.com/watch?v=..." onchange="updateYoutubeThumbnail()">
                        
                        <!-- Aperçu de la miniature YouTube -->
                        <div id="youtube_preview" class="mt-3 hidden">
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border">
                                <img id="youtube_thumbnail" src="" alt="Miniature YouTube" class="w-20 h-15 object-cover rounded">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Aperçu de la miniature</p>
                                    <p class="text-xs text-gray-500">Miniature générée automatiquement depuis YouTube</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="date_publication" class="block text-sm font-medium text-gray-700 mb-2">Date de publication</label>
                        <input type="datetime-local" id="date_publication" name="date_publication" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="vues" class="block text-sm font-medium text-gray-700 mb-2">Nombre de vues</label>
                        <input type="number" id="vues" name="vues" min="0" value="0" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal('episodeModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="btn-primary px-6 py-2 text-sm font-medium rounded-lg  shadow-lg hover:shadow-xl transition-all duration-300">
                        <span id="submitText">Créer l'épisode</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Voir Épisode -->
    <div id="viewModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-purple-600">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold ">Détails de l'épisode</h3>
                    <button onclick="closeModal('viewModal')" class=" hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="viewContent" class="p-6">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
        </div>
    </div>

    <!-- Modal Actions groupées -->
    <div id="bulkModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-600 to-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold ">Actions groupées</h3>
                    <button onclick="closeModal('bulkModal')" class=" hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Sélectionnez une action à appliquer aux épisodes sélectionnés :</p>

                <div class="space-y-3">
                    <button onclick="bulkAction('publish')" class="w-full flex items-center px-4 py-3 text-left text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Publier les épisodes sélectionnés
                    </button>

                    <button onclick="bulkAction('draft')" class="w-full flex items-center px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mettre en brouillon
                    </button>

                    <button onclick="bulkAction('delete')" class="w-full flex items-center px-4 py-3 text-left text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Supprimer les épisodes sélectionnés
                    </button>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <button onclick="closeModal('bulkModal')" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmation Suppression -->
    <div id="deleteModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-600 to-red-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold ">Confirmer la suppression</h3>
                    <button onclick="closeModal('deleteModal')" class=" hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-900">Êtes-vous sûr ?</h4>
                        <p class="text-sm text-gray-500 mt-1">
                            Vous êtes sur le point de supprimer l'épisode "<span id="deleteEpisodeTitle" class="font-medium"></span>". Cette action est irréversible.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <button onclick="closeModal('deleteModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                        Annuler
                    </button>
                    <button onclick="confirmDelete()" class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium  bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                        Supprimer définitivement
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let currentEpisodeId = null;

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing episode management...');

    // Vérifier que le CSRF token existe
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found!');
        showNotification('Erreur de sécurité : token CSRF manquant', 'error');
        return;
    }

    console.log('CSRF token found:', csrfToken.getAttribute('content'));

    // Initialiser les événements
    initializeEventListeners();
});

function initializeEventListeners() {
    // Sélection multiple
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.episode-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    // Soumission du formulaire
    const episodeForm = document.getElementById('episodeForm');
    if (episodeForm) {
        episodeForm.addEventListener('submit', handleFormSubmit);
    }

    // Fermer les modales avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal:not(.hidden)');
            modals.forEach(modal => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
            document.body.style.overflow = 'auto';
        }
    });
}

// Gestion des modales
function openModal(modalId) {
    console.log('Opening modal:', modalId);
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.error('Modal not found:', modalId);
        showNotification('Erreur : Modal introuvable', 'error');
        return;
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    console.log('Closing modal:', modalId);
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.error('Modal not found:', modalId);
        return;
    }
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';

    if (modalId === 'episodeModal') {
        resetForm();
    }
}

// Créer un nouvel épisode
function openCreateModal() {
    console.log('Opening create modal...');
    resetForm();
    document.getElementById('modalTitle').textContent = 'Nouvel Épisode';
    document.getElementById('submitText').textContent = 'Créer l\'épisode';
    document.getElementById('formMethod').value = 'POST';
    openModal('episodeModal');
}

// Modifier un épisode
function editEpisode(id) {
    console.log('Editing episode:', id);

    if (!id) {
        console.error('Episode ID is required');
        showNotification('Erreur : ID de l\'épisode manquant', 'error');
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showNotification('Erreur de sécurité : token CSRF manquant', 'error');
        return;
    }

    fetch(`/admin/episodes/${id}/edit`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Edit response status:', response.status);

        if (response.status === 401) {
            showNotification('Session expirée. Redirection...', 'error');
            setTimeout(() => window.location.href = '/admin/login', 2000);
            return;
        }

        if (response.status === 404) {
            throw new Error('Épisode introuvable');
        }

        if (!response.ok) {
            throw new Error(`Erreur ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Edit response data:', data);
        if (data.success) {
            const episode = data.episode;
            document.getElementById('episodeId').value = episode.id;
            document.getElementById('titre').value = episode.titre || '';
            document.getElementById('description').value = episode.description || '';
            document.getElementById('statut').value = episode.statut || 'draft';
            document.getElementById('duree').value = episode.duree || '';
            document.getElementById('youtube_url').value = episode.youtube_url || '';
            document.getElementById('vues').value = episode.vues || 0;

            if (episode.date_publication) {
                const date = new Date(episode.date_publication);
                document.getElementById('date_publication').value = date.toISOString().slice(0, 16);
            }

            // Mettre à jour la miniature YouTube si une URL est présente
            if (episode.youtube_url) {
                setTimeout(() => updateYoutubeThumbnail(), 100);
            }

            document.getElementById('modalTitle').textContent = 'Modifier l\'épisode';
            document.getElementById('submitText').textContent = 'Mettre à jour';
            document.getElementById('formMethod').value = 'PUT';
            openModal('episodeModal');
        } else {
            showNotification(data.message || 'Erreur lors du chargement de l\'épisode', 'error');
        }
    })
    .catch(error => {
        console.error('Error editing episode:', error);
        showNotification('Erreur lors du chargement de l\'épisode: ' + error.message, 'error');
    });
}

// Voir un épisode
function viewEpisode(id) {
    console.log('Viewing episode:', id);

    if (!id) {
        console.error('Episode ID is required');
        showNotification('Erreur : ID de l\'épisode manquant', 'error');
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showNotification('Erreur de sécurité : token CSRF manquant', 'error');
        return;
    }

    fetch(`/admin/episodes/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('View response status:', response.status);

        if (response.status === 401) {
            showNotification('Session expirée. Redirection...', 'error');
            setTimeout(() => window.location.href = '/admin/login', 2000);
            return;
        }

        if (response.status === 404) {
            throw new Error('Épisode introuvable');
        }

        if (!response.ok) {
            throw new Error(`Erreur ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('View response data:', data);
        if (data.success) {
            const episode = data.episode;
            const content = `
                <div class="space-y-6">
                    ${episode.youtube_url ? `
                        <div class="text-center">
                            <img src="https://img.youtube.com/vi/${extractYoutubeVideoId(episode.youtube_url)}/maxresdefault.jpg" 
                                 alt="Miniature YouTube" 
                                 class="mx-auto h-48 w-80 object-cover rounded-lg shadow-lg"
                                 onerror="this.src='https://img.youtube.com/vi/${extractYoutubeVideoId(episode.youtube_url)}/hqdefault.jpg'">
                        </div>
                    ` : ''}

                    <div>
                        <h4 class="text-2xl font-bold text-gray-900 mb-2">${episode.titre || 'Titre non défini'}</h4>
                        <p class="text-gray-600">${episode.description || 'Aucune description'}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Statut</span>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    ${episode.statut === 'published' ? 'bg-green-100 text-green-800' : ''}
                                    ${episode.statut === 'draft' ? 'bg-gray-100 text-gray-800' : ''}
                                    ${episode.statut === 'scheduled' ? 'bg-yellow-100 text-yellow-800' : ''}">
                                    ${episode.statut === 'published' ? 'Publié' : episode.statut === 'draft' ? 'Brouillon' : 'Programmé'}
                                </span>
                            </p>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500">Vues</span>
                            <p class="mt-1 text-lg font-semibold text-gray-900">${episode.vues ? episode.vues.toLocaleString() : '0'}</p>
                        </div>

                        ${episode.duree ? `
                            <div>
                                <span class="text-sm font-medium text-gray-500">Durée</span>
                                <p class="mt-1 text-lg font-semibold text-gray-900">${new Date(episode.duree * 1000).toISOString().substr(11, 8)}</p>
                            </div>
                        ` : ''}

                        <div>
                            <span class="text-sm font-medium text-gray-500">Date de création</span>
                            <p class="mt-1 text-lg font-semibold text-gray-900">${new Date(episode.created_at).toLocaleDateString('fr-FR')}</p>
                        </div>
                    </div>

                    ${episode.youtube_url ? `
                        <div>
                            <span class="text-sm font-medium text-gray-500">YouTube</span>
                            <div class="mt-2">
                                <a href="${episode.youtube_url}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                    Voir sur YouTube
                                </a>
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;

            document.getElementById('viewContent').innerHTML = content;
            openModal('viewModal');
        } else {
            showNotification(data.message || 'Erreur lors du chargement de l\'épisode', 'error');
        }
    })
    .catch(error => {
        console.error('Error viewing episode:', error);
        showNotification('Erreur lors du chargement de l\'épisode: ' + error.message, 'error');
    });
}

// Supprimer un épisode
function deleteEpisode(id, title) {
    console.log('Deleting episode:', id, title);

    if (!id) {
        console.error('Episode ID is required');
        showNotification('Erreur : ID de l\'épisode manquant', 'error');
        return;
    }

    currentEpisodeId = id;
    document.getElementById('deleteEpisodeTitle').textContent = title || 'Épisode sans titre';
    openModal('deleteModal');
}

function confirmDelete() {
    console.log('Confirming delete for episode:', currentEpisodeId);

    if (!currentEpisodeId) {
        showNotification('Erreur : Aucun épisode sélectionné', 'error');
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showNotification('Erreur de sécurité : token CSRF manquant', 'error');
        return;
    }

    fetch(`/admin/episodes/${currentEpisodeId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Delete response status:', response.status);

        if (response.status === 401) {
            showNotification('Session expirée. Redirection...', 'error');
            setTimeout(() => window.location.href = '/admin/login', 2000);
            return;
        }

        if (response.status === 404) {
            throw new Error('Épisode introuvable');
        }

        if (!response.ok) {
            throw new Error(`Erreur ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Delete response data:', data);
        if (data.success) {
            showNotification(data.message || 'Épisode supprimé avec succès', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Erreur lors de la suppression', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting episode:', error);
        showNotification('Erreur lors de la suppression: ' + error.message, 'error');
    })
    .finally(() => {
        closeModal('deleteModal');
        currentEpisodeId = null;
    });
}

// Soumission du formulaire
function handleFormSubmit(e) {
    e.preventDefault();
    console.log('Form submitted');

    const formData = new FormData(e.target);
    const episodeId = document.getElementById('episodeId').value;
    const method = document.getElementById('formMethod').value;

    console.log('Form data:', {
        episodeId: episodeId,
        method: method,
        titre: formData.get('titre')
    });

    let url = '/admin/episodes';
    if (method === 'PUT' && episodeId) {
        url += `/${episodeId}`;
        formData.append('_method', 'PUT');
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showNotification('Erreur de sécurité : token CSRF manquant', 'error');
        return;
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        console.log('Form response status:', response.status);

        if (response.status === 401) {
            showNotification('Session expirée. Redirection...', 'error');
            setTimeout(() => window.location.href = '/admin/login', 2000);
            return;
        }

        if (response.status === 422) {
            return response.json().then(data => {
                throw new Error(data.message || 'Erreur de validation');
            });
        }

        if (!response.ok) {
            throw new Error(`Erreur ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Form response data:', data);
        if (data.success) {
            showNotification(data.message || 'Épisode sauvegardé avec succès', 'success');
            closeModal('episodeModal');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Erreur lors de la sauvegarde', 'error');
        }
    })
    .catch(error => {
        console.error('Error submitting form:', error);
        showNotification('Erreur lors de la sauvegarde: ' + error.message, 'error');
    });
}

// Actions groupées
function openBulkModal() {
    console.log('Opening bulk modal');
    const selected = document.querySelectorAll('.episode-checkbox:checked');
    console.log('Selected episodes:', selected.length);

    if (selected.length === 0) {
        showNotification('Veuillez sélectionner au moins un épisode', 'warning');
        return;
    }
    openModal('bulkModal');
}

function bulkAction(action) {
    console.log('Bulk action:', action);
    const selected = Array.from(document.querySelectorAll('.episode-checkbox:checked')).map(cb => cb.value);

    console.log('Selected episode IDs:', selected);

    if (selected.length === 0) {
        showNotification('Aucun épisode sélectionné', 'warning');
        return;
    }

    let confirmMessage = '';
    switch(action) {
        case 'publish':
            confirmMessage = `Publier ${selected.length} épisode(s) ?`;
            break;
        case 'draft':
            confirmMessage = `Mettre ${selected.length} épisode(s) en brouillon ?`;
            break;
        case 'delete':
            confirmMessage = `Supprimer définitivement ${selected.length} épisode(s) ?`;
            break;
    }

    if (!confirm(confirmMessage)) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showNotification('Erreur de sécurité : token CSRF manquant', 'error');
        return;
    }

    fetch('/admin/episodes/bulk-action', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            action: action,
            episodes: selected
        })
    })
    .then(response => {
        console.log('Bulk action response status:', response.status);

        if (response.status === 401) {
            showNotification('Session expirée. Redirection...', 'error');
            setTimeout(() => window.location.href = '/admin/login', 2000);
            return;
        }

        if (response.status === 422) {
            return response.json().then(data => {
                throw new Error(data.message || 'Erreur de validation');
            });
        }

        if (!response.ok) {
            throw new Error(`Erreur ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Bulk action response data:', data);
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Erreur lors de l\'action groupée', 'error');
        }
    })
    .catch(error => {
        console.error('Error in bulk action:', error);
        showNotification('Erreur lors de l\'action groupée: ' + error.message, 'error');
    })
    .finally(() => {
        closeModal('bulkModal');
    });
}

// Réinitialiser le formulaire
function resetForm() {
    console.log('Resetting form');
    const form = document.getElementById('episodeForm');
    if (form) {
        form.reset();
        document.getElementById('episodeId').value = '';
        document.getElementById('formMethod').value = 'POST';
        
        // Cacher l'aperçu YouTube
        const preview = document.getElementById('youtube_preview');
        if (preview) {
            preview.classList.add('hidden');
        }
    }
}

// Notifications
function showNotification(message, type = 'info') {
    console.log('Showing notification:', message, type);

    // Supprimer les notifications existantes
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notif => notif.remove());

    // Créer une notification toast
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;

    const colors = {
        success: 'bg-green-500 ',
        error: 'bg-red-500 ',
        warning: 'bg-yellow-500 ',
        info: 'bg-blue-500 '
    };

    notification.className += ` ${colors[type] || colors.info}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-3  hover:text-gray-200">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    // Animation d'entrée
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Auto-suppression après 5 secondes
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Fonction de test pour déboguer
function testFunctions() {
    console.log('=== DÉBUT DES TESTS DE DÉBOGAGE ===');

    // Test 1: Vérifier les éléments DOM
    console.log('Test 1: Vérification des éléments DOM');
    const episodeModal = document.getElementById('episodeModal');
    const episodeForm = document.getElementById('episodeForm');
    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    console.log('episodeModal:', episodeModal ? 'TROUVÉ' : 'MANQUANT');
    console.log('episodeForm:', episodeForm ? 'TROUVÉ' : 'MANQUANT');
    console.log('csrfToken:', csrfToken ? 'TROUVÉ' : 'MANQUANT');

    if (csrfToken) {
        console.log('CSRF Token value:', csrfToken.getAttribute('content'));
    }

    // Test 2: Tester les notifications
    console.log('Test 2: Test des notifications');
    showNotification('Test de notification réussie!', 'success');

    // Test 3: Tester l'ouverture de modal
    console.log('Test 3: Test d\'ouverture de modal');
    setTimeout(() => {
        if (episodeModal) {
            openModal('episodeModal');
            showNotification('Modal ouverte avec succès!', 'info');

            // Fermer après 3 secondes
            setTimeout(() => {
                closeModal('episodeModal');
                showNotification('Modal fermée avec succès!', 'info');
            }, 3000);
        } else {
            showNotification('Erreur: Modal introuvable!', 'error');
        }
    }, 2000);

    // Test 4: Tester une requête AJAX simple
    console.log('Test 4: Test de requête AJAX');
    setTimeout(() => {
        if (csrfToken) {
            fetch('/admin/episodes/create', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Test AJAX - Status:', response.status);
                if (response.ok) {
                    showNotification('Test AJAX réussi!', 'success');
                    return response.json();
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }
            })
            .then(data => {
                console.log('Test AJAX - Data:', data);
            })
            .catch(error => {
                console.error('Test AJAX - Error:', error);
                showNotification('Test AJAX échoué: ' + error.message, 'error');
            });
        } else {
            showNotification('Impossible de tester AJAX: CSRF token manquant', 'error');
        }
    }, 4000);

    console.log('=== FIN DES TESTS DE DÉBOGAGE ===');
}

// Fonction pour extraire l'ID YouTube et afficher la miniature
function updateYoutubeThumbnail() {
    const youtubeUrl = document.getElementById('youtube_url').value;
    const preview = document.getElementById('youtube_preview');
    const thumbnail = document.getElementById('youtube_thumbnail');
    
    if (!youtubeUrl) {
        preview.classList.add('hidden');
        return;
    }
    
    // Extraire l'ID de la vidéo YouTube
    const videoId = extractYoutubeVideoId(youtubeUrl);
    
    if (videoId) {
        // Générer l'URL de la miniature
        const thumbnailUrl = `https://img.youtube.com/vi/${videoId}/maxresdefault.jpg`;
        
        // Afficher la miniature
        thumbnail.src = thumbnailUrl;
        preview.classList.remove('hidden');
        
        // Vérifier si l'image existe, sinon utiliser la miniature par défaut
        thumbnail.onerror = function() {
            this.src = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
        };
    } else {
        preview.classList.add('hidden');
        showNotification('URL YouTube invalide', 'warning');
    }
}

// Fonction pour extraire l'ID de la vidéo YouTube
function extractYoutubeVideoId(url) {
    const patterns = [
        /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/,
        /youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/
    ];
    
    for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match) {
            return match[1];
        }
    }
    
    return null;
}
</script>
@endpush
