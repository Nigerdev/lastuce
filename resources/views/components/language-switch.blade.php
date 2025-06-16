<div class="relative inline-block text-left" x-data="{ open: false }">
    <div>
        <button type="button" 
                class="inline-flex items-center justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                @click="open = !open"
                aria-expanded="true" 
                aria-haspopup="true">
            <span class="mr-2">{{ $languageLinks[array_search(true, array_column($languageLinks, 'active'))]['flag'] ?? '🇫🇷' }}</span>
            <span>{{ $languageLinks[array_search(true, array_column($languageLinks, 'active'))]['name'] ?? 'Français' }}</span>
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" 
         x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         role="menu" 
         aria-orientation="vertical" 
         aria-labelledby="menu-button" 
         tabindex="-1">
        <div class="py-1" role="none">
            @foreach($languageLinks as $language)
                <a href="{{ $language['url'] }}" 
                   class="group flex items-center px-4 py-2 text-sm {{ $language['active'] ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}" 
                   role="menuitem" 
                   tabindex="-1"
                   onclick="changeLanguage('{{ $language['locale'] }}')">
                    <span class="mr-3 text-lg">{{ $language['flag'] }}</span>
                    <span class="font-medium">{{ $language['name'] }}</span>
                    @if($language['active'])
                        <svg class="ml-auto h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

<script>
function changeLanguage(locale) {
    // Sauvegarder la langue en session
    fetch('/api/language', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ locale: locale })
    });
}
</script> 