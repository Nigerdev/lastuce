@extends('layouts.admin')

@section('title', 'Nouveau Partenariat')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Nouveau Partenariat</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Créer une nouvelle demande de partenariat.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.partenariats.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('admin.partenariats.store') }}" class="space-y-6">
            @csrf
            
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <!-- Nom de l'entreprise -->
                    <div class="sm:col-span-1">
                        <label for="nom_entreprise" class="block text-sm font-medium text-gray-700">
                            Nom de l'entreprise <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="text" name="nom_entreprise" id="nom_entreprise" value="{{ old('nom_entreprise') }}" required
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('nom_entreprise') border-red-300 @enderror">
                        </div>
                        @error('nom_entreprise')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact -->
                    <div class="sm:col-span-1">
                        <label for="contact" class="block text-sm font-medium text-gray-700">
                            Personne de contact <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="text" name="contact" id="contact" value="{{ old('contact') }}" required
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('contact') border-red-300 @enderror">
                        </div>
                        @error('contact')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="sm:col-span-1">
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('email') border-red-300 @enderror">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div class="sm:col-span-1">
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <select name="status" id="status" required
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('status') border-red-300 @enderror">
                                <option value="">Sélectionner un statut</option>
                                <option value="nouveau" {{ old('status') === 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                                <option value="en_cours" {{ old('status') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="accepte" {{ old('status') === 'accepte' ? 'selected' : '' }}>Accepté</option>
                                <option value="refuse" {{ old('status') === 'refuse' ? 'selected' : '' }}>Refusé</option>
                            </select>
                        </div>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div class="sm:col-span-2">
                        <label for="message" class="block text-sm font-medium text-gray-700">
                            Message de la demande <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <textarea name="message" id="message" rows="4" required
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('message') border-red-300 @enderror"
                                placeholder="Décrivez la demande de partenariat...">{{ old('message') }}</textarea>
                        </div>
                        @error('message')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Minimum 20 caractères requis.</p>
                    </div>

                    <!-- Notes internes -->
                    <div class="sm:col-span-2">
                        <label for="notes_internes" class="block text-sm font-medium text-gray-700">
                            Notes internes
                        </label>
                        <div class="mt-1">
                            <textarea name="notes_internes" id="notes_internes" rows="3"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('notes_internes') border-red-300 @enderror"
                                placeholder="Notes internes pour l'équipe...">{{ old('notes_internes') }}</textarea>
                        </div>
                        @error('notes_internes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Ces notes ne seront visibles que par l'équipe d'administration.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 rounded-b-lg">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.partenariats.index') }}" 
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Annuler
                    </a>
                    <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Créer le partenariat
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textarea
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });

    // Character counter for message
    const messageTextarea = document.getElementById('message');
    const messageContainer = messageTextarea.parentElement;
    
    function updateCharacterCount() {
        const currentLength = messageTextarea.value.length;
        const minLength = 20;
        
        let counterElement = messageContainer.querySelector('.character-counter');
        if (!counterElement) {
            counterElement = document.createElement('p');
            counterElement.className = 'character-counter mt-2 text-sm';
            messageContainer.appendChild(counterElement);
        }
        
        if (currentLength < minLength) {
            counterElement.textContent = `${currentLength}/${minLength} caractères (${minLength - currentLength} restants)`;
            counterElement.className = 'character-counter mt-2 text-sm text-red-500';
        } else {
            counterElement.textContent = `${currentLength} caractères`;
            counterElement.className = 'character-counter mt-2 text-sm text-green-500';
        }
    }
    
    messageTextarea.addEventListener('input', updateCharacterCount);
    updateCharacterCount(); // Initial count
});
</script>
@endsection