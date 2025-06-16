@props(['stats' => []])

<div class="section-padding bg-white relative overflow-hidden">
    <!-- Background decorative elements -->
    <div class="absolute inset-0 bg-pattern opacity-5"></div>
    <div class="absolute top-0 left-0 w-64 h-64 bg-astuce-100 rounded-full mix-blend-multiply filter blur-2xl opacity-50 animate-blob"></div>
    <div class="absolute bottom-0 right-0 w-64 h-64 bg-accent-100 rounded-full mix-blend-multiply filter blur-2xl opacity-50 animate-blob animation-delay-2000"></div>
    
    <div class="container-astuce relative z-10">
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-astuce-100 to-accent-100 text-astuce-700 text-sm font-medium mb-6">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                En chiffres
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                L'Astuce en <span class="text-gradient">statistiques</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Découvrez l'impact de notre communauté passionnée d'astuces
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Episodes -->
            <div class="group text-center p-8 rounded-2xl bg-gradient-to-br from-astuce-50 to-astuce-100 hover:from-astuce-100 hover:to-astuce-200 transition duration-500 transform hover:-translate-y-2 hover:shadow-astuce-lg">
                <div class="w-16 h-16 bg-gradient-to-br from-astuce-500 to-astuce-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-astuce-700 mb-2 group-hover:text-astuce-800 transition duration-300">150+</div>
                <div class="text-astuce-600 font-medium">Épisodes diffusés</div>
            </div>
            
            <!-- Astuces -->
            <div class="group text-center p-8 rounded-2xl bg-gradient-to-br from-accent-50 to-accent-100 hover:from-accent-100 hover:to-accent-200 transition duration-500 transform hover:-translate-y-2 hover:shadow-astuce-lg">
                <div class="w-16 h-16 bg-gradient-to-br from-accent-500 to-accent-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-accent-700 mb-2 group-hover:text-accent-800 transition duration-300">1,200+</div>
                <div class="text-accent-600 font-medium">Astuces partagées</div>
            </div>
            
            <!-- Membres -->
            <div class="group text-center p-8 rounded-2xl bg-gradient-to-br from-success-50 to-success-100 hover:from-success-100 hover:to-success-200 transition duration-500 transform hover:-translate-y-2 hover:shadow-astuce-lg">
                <div class="w-16 h-16 bg-gradient-to-br from-success-500 to-success-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-success-700 mb-2 group-hover:text-success-800 transition duration-300">25K+</div>
                <div class="text-success-600 font-medium">Membres actifs</div>
            </div>
            
            <!-- Partenaires -->
            <div class="group text-center p-8 rounded-2xl bg-gradient-to-br from-warning-50 to-warning-100 hover:from-warning-100 hover:to-warning-200 transition duration-500 transform hover:-translate-y-2 hover:shadow-astuce-lg">
                <div class="w-16 h-16 bg-gradient-to-br from-warning-500 to-warning-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8zM8 14v.01M12 14v.01M16 14v.01"></path>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-warning-700 mb-2 group-hover:text-warning-800 transition duration-300">50+</div>
                <div class="text-warning-600 font-medium">Partenaires</div>
            </div>
        </div>
        
        <!-- Call to action -->
        <div class="text-center mt-16">
            <x-cta-button 
                href="{{ route('about') }}"
                type="primary"
                size="lg"
                class="transform hover:scale-105 transition duration-300">
                Découvrir notre histoire
            </x-cta-button>
        </div>
    </div>
</div> 