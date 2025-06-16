@extends('layouts.admin')

@section('title', 'Gestion des Astuces')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestion des Astuces</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Modérez et gérez les astuces soumises par les utilisateurs.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.astuces.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nouvelle Astuce
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('admin.astuces.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="approuve" {{ request('status') === 'approuve' ? 'selected' : '' }}>Approuvé</option>
                        <option value="rejete" {{ request('status') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Titre, contenu..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700">Trier par</label>
                    <select name="sort" id="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date de création</option>
                        <option value="titre_astuce" {{ request('sort') === 'titre_astuce' ? 'selected' : '' }}>Titre</option>
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
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
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
                            <dt class="text-sm font-medium text-gray-500 truncate">En attente</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['en_attente'] ?? 0 }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Approuvées</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['approuve'] ?? 0 }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Rejetées</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['rejete'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
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

    <!-- Astuces List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Astuces ({{ $astuces->total() }})
                </h3>
                @if($astuces->count() > 0)
                <div class="flex space-x-2">
                    <button type="button" onclick="selectAll()" class="text-sm text-indigo-600 hover:text-indigo-900">Tout sélectionner</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" onclick="bulkAction('approve')" class="text-sm text-green-600 hover:text-green-900">Approuver sélection</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" onclick="bulkAction('reject')" class="text-sm text-red-600 hover:text-red-900">Rejeter sélection</button>
                </div>
                @endif
            </div>
        </div>

        @if($astuces->count() > 0)
        <ul class="divide-y divide-gray-200">
            @foreach($astuces as $astuce)
            <li class="px-4 py-4 hover:bg-gray-50">
                <div class="flex items-center space-x-4">
                    <input type="checkbox" name="selected_astuces[]" value="{{ $astuce->id }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $astuce->titre_astuce }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ Str::limit($astuce->description, 100) }}
                                </p>
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <span>Par {{ $astuce->nom }} ({{ $astuce->email }})</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $astuce->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $astuce->status === 'en_attente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $astuce->status === 'approuve' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $astuce->status === 'rejete' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $astuce->status)) }}
                                </span>
                                
                                <div class="flex space-x-1">
                                    <a href="{{ route('admin.astuces.show', $astuce) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Voir
                                    </a>
                                    
                                    @if($astuce->status === 'en_attente')
                                    <span class="text-gray-300">|</span>
                                    <form method="POST" action="{{ route('admin.astuces.approve', $astuce) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                            Approuver
                                        </button>
                                    </form>
                                    <span class="text-gray-300">|</span>
                                    <form method="POST" action="{{ route('admin.astuces.reject', $astuce) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            Rejeter
                                        </button>
                                    </form>
                                    @endif
                                    
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('admin.astuces.edit', $astuce) }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
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
            {{ $astuces->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune astuce</h3>
            <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle astuce.</p>
            <div class="mt-6">
                <a href="{{ route('admin.astuces.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvelle Astuce
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('input[name="selected_astuces[]"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

function bulkAction(action) {
    const selected = Array.from(document.querySelectorAll('input[name="selected_astuces[]"]:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Veuillez sélectionner au moins une astuce.');
        return;
    }
    
    if (confirm(`Êtes-vous sûr de vouloir ${action === 'approve' ? 'approuver' : 'rejeter'} ${selected.length} astuce(s) ?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.astuces.bulk-action") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_astuces[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection 