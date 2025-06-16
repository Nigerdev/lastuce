@extends('layouts.admin')

@section('title', 'Gestion des Partenariats')

@push('scripts')
<script src="{{ asset('js/admin-partenariats.js') }}"></script>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestion des Partenariats</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Gérez les demandes de partenariat soumises par les entreprises.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.partenariats.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nouveau Partenariat
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('admin.partenariats.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="nouveau" {{ request('status') === 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                        <option value="en_cours" {{ request('status') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="accepte" {{ request('status') === 'accepte' ? 'selected' : '' }}>Accepté</option>
                        <option value="refuse" {{ request('status') === 'refuse' ? 'selected' : '' }}>Refusé</option>
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Entreprise, contact..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700">Trier par</label>
                    <select name="sort" id="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date de création</option>
                        <option value="nom_entreprise" {{ request('sort') === 'nom_entreprise' ? 'selected' : '' }}>Entreprise</option>
                        <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Statut</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-5">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Nouveau</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['nouveau'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">En cours</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['en_cours'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Acceptés</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['accepte'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Refusés</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['refuse'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Partenariats List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Partenariats ({{ $partenariats->total() }})
                </h3>
                @if($partenariats->count() > 0)
                <div class="flex space-x-2">
                    <button type="button" onclick="selectAll()" class="text-sm text-indigo-600 hover:text-indigo-900">Tout sélectionner</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" onclick="bulkAction('approve')" class="text-sm text-green-600 hover:text-green-900">Accepter sélection</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" onclick="bulkAction('reject')" class="text-sm text-red-600 hover:text-red-900">Refuser sélection</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" onclick="bulkAction('en_cours')" class="text-sm text-yellow-600 hover:text-yellow-900">Marquer en cours</button>
                </div>
                @endif
            </div>
        </div>

        @if($partenariats->count() > 0)
        <ul class="divide-y divide-gray-200">
            @foreach($partenariats as $partenariat)
            <li class="px-4 py-4 hover:bg-gray-50">
                <div class="flex items-center space-x-4">
                    <input type="checkbox" name="selected_partenariats[]" value="{{ $partenariat->id }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $partenariat->nom_entreprise }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    Contact: {{ $partenariat->contact }} ({{ $partenariat->email }})
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ Str::limit($partenariat->message, 100) }}
                                </p>
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <span>{{ $partenariat->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $partenariat->status === 'nouveau' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $partenariat->status === 'en_cours' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $partenariat->status === 'accepte' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $partenariat->status === 'refuse' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $partenariat->status)) }}
                                </span>
                                
                                <div class="flex space-x-1">
                                    <a href="{{ route('admin.partenariats.show', $partenariat) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Voir
                                    </a>
                                    
                                    @if($partenariat->status === 'nouveau' || $partenariat->status === 'en_cours')
                                    <span class="text-gray-300">|</span>
                                    <button type="button" onclick="approvePartenariat({{ $partenariat->id }})" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        Accepter
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <button type="button" onclick="rejectPartenariat({{ $partenariat->id }})" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Refuser
                                    </button>
                                    @endif
                                    
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('admin.partenariats.edit', $partenariat) }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                        Modifier
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $partenariats->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m-8 0V6a2 2 0 00-2 2v6" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun partenariat</h3>
            <p class="mt-1 text-sm text-gray-500">Commencez par créer un nouveau partenariat.</p>
            <div class="mt-6">
                <a href="{{ route('admin.partenariats.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouveau Partenariat
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Partenariats JavaScript initialisé');
});

function showNotification(message, type = 'success') {
    // Créer une notification simple
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Supprimer après 5 secondes
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

function selectAll() {
    console.log('📋 Sélection/désélection de tous les partenariats');
    const checkboxes = document.querySelectorAll('input[name="selected_partenariats[]"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

async function bulkAction(action) {
    console.log(`🔄 Action en lot: ${action}`);
    
    const selected = Array.from(document.querySelectorAll('input[name="selected_partenariats[]"]:checked')).map(cb => cb.value);
    
    if (selected.length === 0) {
        showNotification('Veuillez sélectionner au moins un partenariat.', 'error');
        return;
    }
    
    let actionText = '';
    switch(action) {
        case 'approve': actionText = 'accepter'; break;
        case 'reject': actionText = 'refuser'; break;
        case 'en_cours': actionText = 'marquer en cours'; break;
        default: actionText = action;
    }
    
    if (!confirm(`Êtes-vous sûr de vouloir ${actionText} ${selected.length} partenariat(s) ?`)) {
        return;
    }
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        const response = await fetch('{{ route("admin.partenariats.bulk-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: action,
                partenariats: selected
            })
        });
        
        if (!response.ok) {
            if (response.status === 401) {
                showNotification('Session expirée. Redirection...', 'error');
                setTimeout(() => window.location.href = '/login', 2000);
                return;
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showNotification(data.message || 'Une erreur est survenue', 'error');
        }
        
    } catch (error) {
        console.error('❌ Erreur:', error);
        showNotification('Erreur: ' + error.message, 'error');
    }
}

async function approvePartenariat(id) {
    if (!confirm('Êtes-vous sûr de vouloir accepter ce partenariat ?')) {
        return;
    }
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        const response = await fetch(`/admin/partenariats/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                send_notification: false
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showNotification(data.message || 'Une erreur est survenue', 'error');
        }
        
    } catch (error) {
        console.error('❌ Erreur:', error);
        showNotification('Erreur: ' + error.message, 'error');
    }
}

async function rejectPartenariat(id) {
    const reason = prompt('Raison du refus (obligatoire):');
    if (!reason || reason.trim() === '') {
        showNotification('La raison du refus est obligatoire.', 'error');
        return;
    }
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        const response = await fetch(`/admin/partenariats/${id}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                notes_internes: reason,
                send_notification: false
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showNotification(data.message || 'Une erreur est survenue', 'error');
        }
        
    } catch (error) {
        console.error('❌ Erreur:', error);
        showNotification('Erreur: ' + error.message, 'error');
    }
}
</script>
<script src="{{ asset('js/admin-partenariats.js') }}"></script>
@endsection