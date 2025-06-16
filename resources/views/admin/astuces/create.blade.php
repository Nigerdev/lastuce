@extends('layouts.admin')

@section('title', 'Créer une Astuce')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Créer une Astuce</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Ajoutez une nouvelle astuce à la base de données.
                    </p>
                </div>
                <div>
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

    <!-- Formulaire -->
    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('admin.astuces.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Titre -->
                    <div class="sm:col-span-2">
                        <label for="titre" class="block text-sm font-medium text-gray-700">Titre de l'astuce *</label>
                        <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('titre') border-red-300 @enderror">
                        @error('titre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catégorie -->
                    <div>
                        <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie *</label>
                        <input type="text" name="categorie" id="categorie" value="{{ old('categorie') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('categorie') border-red-300 @enderror">
                        @error('categorie')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Difficulté -->
                    <div>
                        <label for="difficulte" class="block text-sm font-medium text-gray-700">Difficulté *</label>
                        <select name="difficulte" id="difficulte" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('difficulte') border-red-300 @enderror">
                            <option value="">Sélectionner...</option>
                            <option value="facile" {{ old('difficulte') === 'facile' ? 'selected' : '' }}>Facile</option>
                            <option value="moyen" {{ old('difficulte') === 'moyen' ? 'selected' : '' }}>Moyen</option>
                            <option value="difficile" {{ old('difficulte') === 'difficile' ? 'selected' : '' }}>Difficile</option>
                        </select>
                        @error('difficulte')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Temps estimé -->
                    <div>
                        <label for="temps_estime" class="block text-sm font-medium text-gray-700">Temps estimé (minutes)</label>
                        <input type="number" name="temps_estime" id="temps_estime" value="{{ old('temps_estime') }}" min="1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('temps_estime') border-red-300 @enderror">
                        @error('temps_estime')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Statut *</label>
                        <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('status') border-red-300 @enderror">
                            <option value="en_attente" {{ old('status') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="approuve" {{ old('status') === 'approuve' ? 'selected' : '' }}>Approuvé</option>
                            <option value="rejete" {{ old('status') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                        <textarea name="description" id="description" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Matériel requis -->
                    <div class="sm:col-span-2">
                        <label for="materiel_requis" class="block text-sm font-medium text-gray-700">Matériel requis</label>
                        <textarea name="materiel_requis" id="materiel_requis" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('materiel_requis') border-red-300 @enderror">{{ old('materiel_requis') }}</textarea>
                        @error('materiel_requis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Étapes -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Étapes *</label>
                        <div id="etapes-container" class="mt-2 space-y-3">
                            @if(old('etapes'))
                                @foreach(old('etapes') as $index => $etape)
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-500">{{ $index + 1 }}.</span>
                                        <input type="text" name="etapes[]" value="{{ $etape }}" required
                                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <button type="button" onclick="removeEtape(this)" class="text-red-600 hover:text-red-900">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-500">1.</span>
                                    <input type="text" name="etapes[]" required
                                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <button type="button" onclick="removeEtape(this)" class="text-red-600 hover:text-red-900">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addEtape()" class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ajouter une étape
                        </button>
                        @error('etapes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Conseils -->
                    <div class="sm:col-span-2">
                        <label for="conseils" class="block text-sm font-medium text-gray-700">Conseils</label>
                        <textarea name="conseils" id="conseils" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('conseils') border-red-300 @enderror">{{ old('conseils') }}</textarea>
                        @error('conseils')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nom du soumetteur -->
                    <div>
                        <label for="nom_soumetteur" class="block text-sm font-medium text-gray-700">Nom du soumetteur *</label>
                        <input type="text" name="nom_soumetteur" id="nom_soumetteur" value="{{ old('nom_soumetteur') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('nom_soumetteur') border-red-300 @enderror">
                        @error('nom_soumetteur')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email du soumetteur -->
                    <div>
                        <label for="email_soumetteur" class="block text-sm font-medium text-gray-700">Email du soumetteur *</label>
                        <input type="email" name="email_soumetteur" id="email_soumetteur" value="{{ old('email_soumetteur') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email_soumetteur') border-red-300 @enderror">
                        @error('email_soumetteur')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Images -->
                    <div class="sm:col-span-2">
                        <label for="images" class="block text-sm font-medium text-gray-700">Images</label>
                        <input type="file" name="images[]" id="images" multiple accept="image/*"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-sm text-gray-500">Vous pouvez sélectionner plusieurs images (max 2MB chacune).</p>
                        @error('images')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 rounded-b-lg">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.astuces.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Annuler
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Créer l'astuce
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function addEtape() {
    const container = document.getElementById('etapes-container');
    const etapeCount = container.children.length + 1;
    
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2';
    div.innerHTML = `
        <span class="text-sm font-medium text-gray-500">${etapeCount}.</span>
        <input type="text" name="etapes[]" required
               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        <button type="button" onclick="removeEtape(this)" class="text-red-600 hover:text-red-900">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    
    container.appendChild(div);
}

function removeEtape(button) {
    const container = document.getElementById('etapes-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
        updateEtapeNumbers();
    }
}

function updateEtapeNumbers() {
    const container = document.getElementById('etapes-container');
    Array.from(container.children).forEach((div, index) => {
        const span = div.querySelector('span');
        span.textContent = `${index + 1}.`;
    });
}
</script>
@endsection