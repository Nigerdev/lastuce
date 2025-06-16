<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Administration') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Additional CSS -->
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-gray-900">
                                {{ config('app.name') }} Admin
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard*')">
                                {{ __('Dashboard') }}
                            </x-nav-link>

                            @can('admin:episodes.manage')
                            <x-nav-link :href="route('admin.episodes.index')" :active="request()->routeIs('admin.episodes*')">
                                {{ __('Épisodes') }}
                            </x-nav-link>
                            @endcan

                            @can('admin:astuces.moderate')
                            <x-nav-link :href="route('admin.astuces.index')" :active="request()->routeIs('admin.astuces*')">
                                {{ __('Astuces') }}
                                @if($pendingAstuces = \App\Models\AstucesSoumise::where('status', 'en_attente')->count())
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $pendingAstuces }}
                                    </span>
                                @endif
                            </x-nav-link>
                            @endcan

                            @can('admin:partenariats.manage')
                            <x-nav-link :href="route('admin.partenariats.index')" :active="request()->routeIs('admin.partenariats*')">
                                {{ __('Partenariats') }}
                                @if($pendingPartenariats = \App\Models\Partenariat::where('status', 'en_attente')->count())
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $pendingPartenariats }}
                                    </span>
                                @endif
                            </x-nav-link>
                            @endcan

                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Notifications -->
                        <div class="relative mr-4">
                            <button id="notifications-button" class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span class="sr-only">Voir les notifications</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19c-5 0-9-4-9-9s4-9 9-9 9 4 9 9c0 .273-.02.543-.06.81L15 15H6c-.55 0-1-.45-1-1V8c0-.55.45-1 1-1h8c.55 0 1 .45 1 1v2"/>
                                </svg>
                                <span id="notification-count" class="hidden absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"></span>
                            </button>

                            <!-- Notifications Dropdown -->
                            <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                                <div class="px-4 py-2 border-b border-gray-200">
                                    <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                                </div>
                                <div id="notifications-list" class="max-h-96 overflow-y-auto">
                                    <!-- Notifications will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('admin.settings.index')">
                                    {{ __('Paramètres') }}
                                </x-dropdown-link>

                                @can('admin:logs.view')
                                <x-dropdown-link :href="route('admin.security.logs')">
                                    {{ __('Logs de sécurité') }}
                                </x-dropdown-link>
                                @endcan

                                <div class="border-t border-gray-100"></div>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('admin.logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Se déconnecter') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('warning') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // CSRF Token
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        // Notifications
        document.addEventListener('DOMContentLoaded', function() {
            const notificationsButton = document.getElementById('notifications-button');
            const notificationsDropdown = document.getElementById('notifications-dropdown');
            const notificationsList = document.getElementById('notifications-list');
            const notificationCount = document.getElementById('notification-count');

            // Toggle notifications dropdown
            notificationsButton.addEventListener('click', function() {
                notificationsDropdown.classList.toggle('hidden');
                if (!notificationsDropdown.classList.contains('hidden')) {
                    loadNotifications();
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!notificationsButton.contains(event.target) && !notificationsDropdown.contains(event.target)) {
                    notificationsDropdown.classList.add('hidden');
                }
            });

            // Load notifications
            function loadNotifications() {
                fetch('{{ route("admin.api.notifications") }}')
                    .then(response => response.json())
                    .then(data => {
                        notificationsList.innerHTML = '';

                        if (data.length === 0) {
                            notificationsList.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Aucune notification</div>';
                            notificationCount.classList.add('hidden');
                        } else {
                            notificationCount.textContent = data.length;
                            notificationCount.classList.remove('hidden');

                            data.forEach(notification => {
                                const item = document.createElement('div');
                                item.className = 'px-4 py-3 hover:bg-gray-50 border-b border-gray-100 cursor-pointer';
                                item.innerHTML = `
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-${getPriorityColor(notification.priority)}-500 rounded-full mt-2"></div>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                                            <p class="text-sm text-gray-500">${notification.message}</p>
                                            <p class="text-xs text-gray-400 mt-1">${formatDate(notification.created_at)}</p>
                                        </div>
                                    </div>
                                `;

                                item.addEventListener('click', function() {
                                    markAsRead(notification.id);
                                    if (notification.action_url) {
                                        window.location.href = notification.action_url;
                                    }
                                });

                                notificationsList.appendChild(item);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des notifications:', error);
                    });
            }

            // Mark notification as read
            function markAsRead(notificationId) {
                fetch(`{{ route("admin.api.notifications", "") }}/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    }
                });
            }

            // Helper functions
            function getPriorityColor(priority) {
                switch (priority) {
                    case 'urgent': return 'red';
                    case 'high': return 'orange';
                    case 'normal': return 'blue';
                    case 'low': return 'gray';
                    default: return 'blue';
                }
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;
                const minutes = Math.floor(diff / 60000);
                const hours = Math.floor(diff / 3600000);
                const days = Math.floor(diff / 86400000);

                if (minutes < 1) return 'À l\'instant';
                if (minutes < 60) return `Il y a ${minutes} min`;
                if (hours < 24) return `Il y a ${hours}h`;
                if (days < 7) return `Il y a ${days}j`;
                return date.toLocaleDateString('fr-FR');
            }

            // Load notifications on page load
            loadNotifications();

            // Refresh notifications every 30 seconds
            setInterval(loadNotifications, 30000);
        });

        // Global admin functions
        window.AdminUtils = {
            // Show confirmation dialog
            confirm: function(message, callback) {
                if (confirm(message)) {
                    callback();
                }
            },

            // Show loading state
            showLoading: function(element) {
                element.disabled = true;
                element.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Chargement...';
            },

            // Hide loading state
            hideLoading: function(element, originalText) {
                element.disabled = false;
                element.innerHTML = originalText;
            },

            // Format numbers
            formatNumber: function(num) {
                return new Intl.NumberFormat('fr-FR').format(num);
            },

            // Format file size
            formatFileSize: function(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        };
    </script>

    @stack('scripts')
</body>
</html>
