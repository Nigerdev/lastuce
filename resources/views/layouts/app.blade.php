<!DOCTYPE html>
<html lang="{{ $htmlLang }}" dir="{{ $localeDirection }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', __('app.seo.site_name'))</title>
    <meta name="description" content="@yield('description', __('app.seo.site_description'))">
    <meta name="keywords" content="@yield('keywords', $seoKeywords)">
    <meta name="author" content="{{ __('app.seo.author') }}">

    <!-- Open Graph -->
    <meta property="og:type" content="{{ __('app.seo.og_type') }}">
    <meta property="og:title" content="@yield('title', __('app.seo.site_name'))">
    <meta property="og:description" content="@yield('description', __('app.seo.site_description'))">
    <meta property="og:site_name" content="{{ __('app.seo.site_name') }}">
    <meta property="og:locale" content="{{ $htmlLang }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Alternate URLs for other languages -->
    @foreach($supportedLocales as $locale => $config)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ App\Helpers\LocalizationHelper::getCurrentRouteInLocale($locale) }}">
    @endforeach

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white shadow" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-xl font-bold text-gray-900">
                                {{ __('app.seo.site_name') }}
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                                {{ __('app.nav.home') }}
                            </a>
                            <a href="{{ route('episodes.index', ['locale' => app()->getLocale()]) }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('episodes.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                                {{ __('app.nav.episodes') }}
                            </a>
                            <a href="{{ route('astuces.index', ['locale' => app()->getLocale()]) }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('astuces.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                                {{ __('app.nav.tips') }}
                            </a>
                            <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('blog.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                                {{ __('app.nav.blog') }}
                            </a>
                            <a href="{{ route('contact.create', ['locale' => app()->getLocale()]) }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('contact.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                                {{ __('app.nav.contact') }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Language Switcher -->
                        <div class="hidden sm:block">
                            @include('components.language-switch', ['languages' => $languageLinks])
                        </div>

                        <!-- Mobile menu button -->
                        <div class="sm:hidden">
                            <button @click="open = !open" type="button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" :aria-expanded="open">
                                <span class="sr-only">{{ __('Ouvrir le menu principal') }}</span>
                                <!-- Icon when menu is closed -->
                                <svg x-show="!open" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <!-- Icon when menu is open -->
                                <svg x-show="open" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="sm:hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('home') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium"
                       @click="open = false">
                        {{ __('app.nav.home') }}
                    </a>
                    <a href="{{ route('episodes.index', ['locale' => app()->getLocale()]) }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('episodes.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium"
                       @click="open = false">
                        {{ __('app.nav.episodes') }}
                    </a>
                    <a href="{{ route('astuces.index', ['locale' => app()->getLocale()]) }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('astuces.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium"
                       @click="open = false">
                        {{ __('app.nav.tips') }}
                    </a>
                    <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('blog.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium"
                       @click="open = false">
                        {{ __('app.nav.blog') }}
                    </a>
                    <a href="{{ route('contact.create', ['locale' => app()->getLocale()]) }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('contact.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium"
                       @click="open = false">
                        {{ __('app.nav.contact') }}
                    </a>

                    <!-- Mobile Language Switcher -->
                    <div class="border-t border-gray-200 pt-4 pb-3">
                        <div class="px-3">
                            <p class="text-sm font-medium text-gray-500 mb-2">{{ __('Langue') }}</p>
                            @include('components.language-switch', ['languages' => $languageLinks, 'mobile' => true])
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- About -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ __('app.footer.about_us') }}</h3>
                        <p class="text-gray-300">{{ __('app.seo.site_description') }}</p>
                    </div>

                    <!-- Links -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ __('app.nav.menu') }}</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('episodes.index', ['locale' => app()->getLocale()]) }}" class="text-gray-300 hover:text-white">{{ __('app.nav.episodes') }}</a></li>
                            <li><a href="{{ route('astuces.index', ['locale' => app()->getLocale()]) }}" class="text-gray-300 hover:text-white">{{ __('app.nav.tips') }}</a></li>
                            <li><a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" class="text-gray-300 hover:text-white">{{ __('app.nav.blog') }}</a></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ __('app.footer.contact_us') }}</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('contact.create', ['locale' => app()->getLocale()]) }}" class="text-gray-300 hover:text-white">{{ __('app.nav.contact') }}</a></li>
                            <li><a href="{{ route('newsletter.subscribe', ['locale' => app()->getLocale()]) }}" class="text-gray-300 hover:text-white">{{ __('app.footer.newsletter_signup') }}</a></li>
                        </ul>
                    </div>

                    <!-- Social Media -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ __('app.footer.follow_us') }}</h3>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-700 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-300">© {{ date('Y') }} {{ __('app.seo.site_name') }}. {{ __('app.footer.copyright') }}</p>
                    <p class="text-gray-300 mt-2 md:mt-0">{{ __('app.footer.made_with_love') }}</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>