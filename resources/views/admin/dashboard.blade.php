@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Bienvenue, {{ auth()->user()->name }}. Voici un aperçu de votre site.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="refreshStats()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Actualiser
                    </button>
                    <a href="{{ route('admin.export') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exporter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Episodes -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Épisodes</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['episodes']['total'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-green-600 font-medium">+{{ $stats['episodes']['this_month'] ?? 0 }}</span>
                    <span class="text-gray-600"> ce mois</span>
                </div>
            </div>
        </div>

        <!-- Astuces -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Astuces</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['astuces']['total'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-red-600 font-medium">{{ $stats['astuces']['pending'] ?? 0 }}</span>
                    <span class="text-gray-600"> en attente</span>
                </div>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Abonnés</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['newsletter']['total'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-green-600 font-medium">+{{ $stats['newsletter']['this_week'] ?? 0 }}</span>
                    <span class="text-gray-600"> cette semaine</span>
                </div>
            </div>
        </div>

        <!-- Partenariats -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Partenariats</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['partenariats']['total'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-yellow-600 font-medium">{{ $stats['partenariats']['pending'] ?? 0 }}</span>
                    <span class="text-gray-600"> en attente</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(!empty($alerts))
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Alertes</h3>
            <div class="space-y-3">
                @foreach($alerts as $alert)
                <div class="flex items-start p-3 rounded-md {{ $alert['type'] === 'error' ? 'bg-red-50 border border-red-200' : ($alert['type'] === 'warning' ? 'bg-yellow-50 border border-yellow-200' : 'bg-blue-50 border border-blue-200') }}">
                    <div class="flex-shrink-0">
                        @if($alert['type'] === 'error')
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        @elseif($alert['type'] === 'warning')
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium {{ $alert['type'] === 'error' ? 'text-red-800' : ($alert['type'] === 'warning' ? 'text-yellow-800' : 'text-blue-800') }}">
                            {{ $alert['message'] }}
                        </p>
                        @if(isset($alert['action']))
                        <div class="mt-2">
                            <a href="{{ $alert['action']['url'] }}" class="text-sm {{ $alert['type'] === 'error' ? 'text-red-600 hover:text-red-500' : ($alert['type'] === 'warning' ? 'text-yellow-600 hover:text-yellow-500' : 'text-blue-600 hover:text-blue-500') }} underline">
                                {{ $alert['action']['text'] }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Activité des 30 derniers jours</h3>
                <div class="h-64">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Activité récente</h3>
                <div class="flow-root">
                    <ul class="-mb-8">
                        @forelse($recentActivity as $index => $activity)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full {{ $activity['severity_color'] }} flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ $activity['description'] }}</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $activity['created_at'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="text-center py-4 text-gray-500">Aucune activité récente</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Stats -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Statistiques de sécurité</h3>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $securityStats['failed_attempts'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Tentatives échouées (24h)</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $securityStats['blocked_ips'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">IPs bloquées</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $securityStats['admin_logins'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Connexions admin (7j)</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $securityStats['critical_actions'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Actions critiques (7j)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actions rapides</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @can('admin:episodes.manage')
                <a href="{{ route('admin.episodes.create') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-300 hover:border-gray-400">
                    <div>
                        <span class="rounded-lg inline-flex p-3 bg-blue-50 text-blue-700 ring-4 ring-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </span>
                    </div>
                    <div class="mt-8">
                        <h3 class="text-lg font-medium">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            Nouvel épisode
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Créer un nouvel épisode de podcast
                        </p>
                    </div>
                </a>
                @endcan

                @can('admin:astuces.moderate')
                <a href="{{ route('admin.astuces.index', ['status' => 'pending']) }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-300 hover:border-gray-400">
                    <div>
                        <span class="rounded-lg inline-flex p-3 bg-yellow-50 text-yellow-700 ring-4 ring-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                    <div class="mt-8">
                        <h3 class="text-lg font-medium">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            Modérer astuces
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Approuver ou rejeter les astuces
                        </p>
                    </div>
                </a>
                @endcan

                @can('admin:newsletter.manage')
                <a href="{{ route('admin.newsletter.index') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-300 hover:border-gray-400">
                    <div>
                        <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-700 ring-4 ring-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                    </div>
                    <div class="mt-8">
                        <h3 class="text-lg font-medium">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            Newsletter
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Gérer les abonnés et campagnes
                        </p>
                    </div>
                </a>
                @endcan

                @can('admin:logs.view')
                <a href="{{ route('admin.security.logs') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-300 hover:border-gray-400">
                    <div>
                        <span class="rounded-lg inline-flex p-3 bg-red-50 text-red-700 ring-4 ring-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                    </div>
                    <div class="mt-8">
                        <h3 class="text-lg font-medium">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            Logs sécurité
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Consulter les logs d'activité
                        </p>
                    </div>
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activity Chart
    const ctx = document.getElementById('activityChart').getContext('2d');
    const chartData = @json($chartData ?? []);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels || [],
            datasets: [
                {
                    label: 'Épisodes',
                    data: chartData.episodes || [],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1
                },
                {
                    label: 'Astuces',
                    data: chartData.astuces || [],
                    borderColor: 'rgb(245, 158, 11)',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.1
                },
                {
                    label: 'Newsletter',
                    data: chartData.newsletter || [],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            }
        }
    });
});

// Refresh stats function
function refreshStats() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    AdminUtils.showLoading(button);
    
    fetch('{{ route("admin.stats") }}')
        .then(response => response.json())
        .then(data => {
            location.reload();
        })
        .catch(error => {
            console.error('Error refreshing stats:', error);
            alert('Erreur lors de l\'actualisation des statistiques');
        })
        .finally(() => {
            AdminUtils.hideLoading(button, originalText);
        });
}
</script>
@endpush
@endsection 