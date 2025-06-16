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
        <nav class="bg-white shadow">
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
                        @include('components.language-switch', ['languages' => $languageLinks])
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