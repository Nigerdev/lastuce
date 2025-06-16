@extends('layouts.admin')

@section('title', 'Partenariat #' . $partenariat->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Partenariat #{{ $partenariat->id }}</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $partenariat->nom_entreprise }} - {{ $partenariat->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.partenariats.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour
                    </a>
                    <a href="{{ route('admin.partenariats.edit', $partenariat) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Modifier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Détails du partenariat -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Détails du partenariat</h3>
                    
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Entreprise</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $partenariat->nom_entreprise }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contact</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $partenariat->contact }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $partenariat->email }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $partenariat->email }}
                                </a>
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $partenariat->status === 'nouveau' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $partenariat->status === 'en_cours' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $partenariat->status === 'accepte' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $partenariat->status === 'refuse' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $partenariat->status)) }}
                                </span>
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $partenariat->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dernière modification</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $partenariat->updated_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Message -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Message de la demande</h3>
                    <div class="prose max-w-none">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $partenariat->message }}</p>
                    </div>
                </div>
            </div>

            <!-- Notes internes -->
            @if($partenariat->notes_internes)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Notes internes</h3>
                    <div class="bg-gray-50 rounded-md p-4">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $partenariat->notes_internes }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions et informations secondaires -->
        <div class="space-y-6">
            <!-- Actions rapides -->
            @if($partenariat->status === 'nouveau' || $partenariat->status === 'en_cours')
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actions rapides</h3>
                    
                    <div class="space-y-3">
                        <!-- Accepter -->
                        <form method="POST" action="{{ route('admin.partenariats.approve', $partenariat) }}" class="w-full">
                            @csrf
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir accepter ce partenariat ?')"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Accepter
                            </button>
                        </form>

                        <!-- Refuser -->
                        <button type="button" onclick="showRejectModal()"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Refuser
                        </button>

                        @if($partenariat->status === 'nouveau')
                        <!-- Marquer en cours -->
                        <form method="POST" action="{{ route('admin.partenariats.update', $partenariat) }}" class="w-full">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="nom_entreprise" value="{{ $partenariat->nom_entreprise }}">
                            <input type="hidden" name="contact" value="{{ $partenariat->contact }}">
                            <input type="hidden" name="email" value="{{ $partenariat->email }}">
                            <input type="hidden" name="message" value="{{ $partenariat->message }}">
                            <input type="hidden" name="status" value="en_cours">
                            <input type="hidden" name="notes_internes" value="{{ $partenariat->notes_internes }}">
                            
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Marquer en cours
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Informations système -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informations système</h3>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">#{{ $partenariat->id }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Créé le</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $partenariat->created_at->format('d/m/Y à H:i:s') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Modifié le</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $partenariat->updated_at->format('d/m/Y à H:i:s') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Durée depuis création</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $partenariat->created_at->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Actions de suppression -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Zone de danger</h3>
                    
                    <form method="POST" action="{{ route('admin.partenariats.destroy', $partenariat) }}" 
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce partenariat ? Cette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Supprimer le partenariat
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de refus -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Refuser le partenariat</h3>
                <button type="button" onclick="hideRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('admin.partenariats.reject', $partenariat) }}">
                @csrf
                <div class="mb-4">
                    <label for="reject_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Raison du refus <span class="text-red-500">*</span>
                    </label>
                    <textarea name="notes_internes" id="reject_notes" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Expliquez pourquoi ce partenariat est refusé..."></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="send_notification" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Envoyer une notification par email</span>
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideRejectModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Annuler
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Refuser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('reject_notes').value = '';
}

// Fermer la modal en cliquant à l'extérieur
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRejectModal();
    }
});
</script>
@endsection