<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion Administration - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-2xl font-bold text-gray-900">
                    {{ config('app.name') }}
                </a>
            </div>

            <div class="mb-4 text-center">
                <h2 class="text-xl font-semibold text-gray-700">Administration</h2>
                <p class="text-sm text-gray-600 mt-1">Connectez-vous pour accéder au panneau d'administration</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-4">
                    <div class="font-medium text-sm text-red-600">
                        {{ __('Whoops! Something went wrong.') }}
                    </div>

                    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" id="login-form">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700">{{ __('Email') }}</label>
                    <input id="email" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block font-medium text-sm text-gray-700">{{ __('Password') }}</label>
                    <input id="password" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password" required autocomplete="current-password" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <!-- Security Info -->
                <div class="mt-4 p-3 bg-blue-50 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Sécurité renforcée</h3>
                            <div class="mt-1 text-sm text-blue-700">
                                <p>Cette interface est protégée par des mesures de sécurité avancées. Toutes les tentatives de connexion sont enregistrées.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    @if (Route::has('admin.password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('admin.password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <button type="submit" id="login-button" class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>

            <!-- Security Status -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Connexion sécurisée SSL</span>
                    <span id="security-status" class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                        Sécurisé
                    </span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-xs text-gray-500">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <p class="mt-1">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="hover:text-gray-700">Retour au site</a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('login-form');
            const button = document.getElementById('login-button');
            const originalButtonText = button.innerHTML;

            form.addEventListener('submit', function() {
                button.disabled = true;
                button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Connexion...';
            });

            // Reset button if form submission fails
            setTimeout(function() {
                if (button.disabled) {
                    button.disabled = false;
                    button.innerHTML = originalButtonText;
                }
            }, 5000);

            // Check security status
            fetch('{{ route("admin.api.security.status") }}')
                .then(response => response.json())
                .then(data => {
                    const statusElement = document.getElementById('security-status');
                    if (data.secure) {
                        statusElement.innerHTML = '<div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>Sécurisé';
                    } else {
                        statusElement.innerHTML = '<div class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></div>Attention';
                    }
                })
                .catch(error => {
                    console.log('Security check failed:', error);
                });
        });
    </script>
</body>
</html> 