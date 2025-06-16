@extends('layouts.admin')

@section('title', 'Astuce: ' . $astuce->titre_astuce)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $astuce->titre_astuce }}</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Soumise le {{ $astuce->created_at->format('d/m/Y à H:i') }} par {{ $astuce->nom }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.astuces.edit', $astuce) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Modifier
                    </a>
                    <a href="{{ route('admin.astuces.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statut et Actions -->
    @if($astuce->status === 'en_attente')
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium text-yellow-800">Cette astuce est en attente de modération</span>
            </div>
            <div class="flex space-x-2">
                <form method="POST" action="{{ route('admin.astuces.approve', $astuce) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approuver
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.astuces.reject', $astuce) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Rejeter
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contenu principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Description</h3>
                    <div class="prose max-w-none">
                        <p class="text-gray-700">{{ $astuce->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Étapes -->
            @if($astuce->etapes && count($astuce->etapes) > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Étapes</h3>
                    <ol class="space-y-3">
                        @foreach($astuce->etapes as $index => $etape)
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 text-white text-sm font-medium rounded-full flex items-center justify-center mr-3">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-gray-700">{{ $etape }}</span>
                        </li>
                        @endforeach
                    </ol>
                </div>
            </div>
            @endif

            <!-- Matériel requis -->
            @if($astuce->materiel_requis)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Matériel requis</h3>
                    <div class="prose max-w-none">
                        <p class="text-gray-700">{{ $astuce->materiel_requis }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Conseils -->
            @if($astuce->conseils)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Conseils</h3>
                    <div class="prose max-w-none">
                        <p class="text-gray-700">{{ $astuce->conseils }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Images -->
            @if($astuce->images && count($astuce->images) > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Images</h3>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        @foreach($astuce->images as $image)
                        <div class="relative">
                            <img src="{{ Storage::url($image) }}" alt="Image de l'astuce" class="w-full h-32 object-cover rounded-lg cursor-pointer" onclick="openImageModal('{{ Storage::url($image) }}')">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Informations générales -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informations</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $astuce->status === 'en_attente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $astuce->status === 'approuve' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $astuce->status === 'rejete' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $astuce->status)) }}
                                </span>
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $astuce->categorie }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Difficulté</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $astuce->difficulte === 'facile' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $astuce->difficulte === 'moyen' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $astuce->difficulte === 'difficile' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($astuce->difficulte) }}
                                </span>
                            </dd>
                        </div>
                        
                        @if($astuce->temps_estime)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Temps estimé</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $astuce->temps_estime }} minutes</dd>
                        </div>
                        @endif
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de soumission</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $astuce->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                        
                        @if($astuce->updated_at != $astuce->created_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dernière modification</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $astuce->updated_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Informations du soumetteur -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Soumetteur</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nom</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $astuce->nom }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $astuce->email }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $astuce->email }}
                                </a>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Commentaire admin -->
            @if($astuce->commentaire_admin)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Commentaire administrateur</h3>
                    <p class="text-sm text-gray-700">{{ $astuce->commentaire_admin }}</p>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.astuces.edit', $astuce) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Modifier
                        </a>
                        
                        <form method="POST" action="{{ route('admin.astuces.destroy', $astuce) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette astuce ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les images -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-end">
                <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-2 text-center">
                <img id="modalImage" src="" alt="Image agrandie" class="max-w-full h-auto mx-auto">
            </div>
        </div>
    </div>
</div>

<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
</script>
@endsection