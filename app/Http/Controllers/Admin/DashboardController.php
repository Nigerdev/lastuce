<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Episode;
use App\Models\AstucesSoumise;
use App\Models\Partenariat;
use App\Models\NewsletterAbonne;
use App\Models\BlogArticle;
use App\Models\AdminLog;
use App\Models\FailedLoginAttempt;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Afficher le dashboard principal
     */
    public function index(Request $request)
    {
        // Statistiques générales (mise en cache pour 5 minutes)
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'episodes' => [
                    'total' => Episode::count(),
                    'published' => Episode::where('statut', 'published')->count(),
                    'draft' => Episode::where('statut', 'draft')->count(),
                    'this_month' => Episode::whereMonth('created_at', now()->month)->count(),
                ],
                'astuces' => [
                    'total' => AstucesSoumise::count(),
                    'pending' => AstucesSoumise::where('status', 'en_attente')->count(),
                                'approved' => AstucesSoumise::where('status', 'approuve')->count(),
            'rejected' => AstucesSoumise::where('status', 'rejete')->count(),
                    'this_week' => AstucesSoumise::where('created_at', '>=', now()->startOfWeek())->count(),
                ],
                'partenariats' => [
                    'total' => Partenariat::count(),
                    'pending' => Partenariat::where('status', 'en_attente')->count(),
                                'approved' => Partenariat::where('status', 'approuve')->count(),
            'rejected' => Partenariat::where('status', 'rejete')->count(),
                    'this_month' => Partenariat::whereMonth('created_at', now()->month)->count(),
                ],
                'newsletter' => [
                    'total' => NewsletterAbonne::count(),
                    'active' => NewsletterAbonne::where('status', 'actif')->count(),
                    'pending' => NewsletterAbonne::where('status', 'en_attente')->count(),
                    'unsubscribed' => NewsletterAbonne::where('status', 'desabonne')->count(),
                    'this_week' => NewsletterAbonne::where('created_at', '>=', now()->startOfWeek())->count(),
                ],
                'blog' => [
                    'total' => BlogArticle::count(),
                    'published' => BlogArticle::where('is_published', true)->count(),
                    'draft' => BlogArticle::where('is_published', false)->count(),
                    'this_month' => BlogArticle::whereMonth('created_at', now()->month)->count(),
                ],
            ];
        });

        // Activité récente
        $recentActivity = $this->getRecentActivity();

        // Statistiques de sécurité
        $securityStats = $this->getSecurityStats();

        // Graphiques de données (derniers 30 jours)
        $chartData = $this->getChartData();

        // Éléments nécessitant une attention
        $alerts = $this->getAlerts();

        return view('admin.dashboard', compact(
            'stats',
            'recentActivity',
            'securityStats',
            'chartData',
            'alerts'
        ));
    }

    /**
     * Obtenir l'activité récente
     */
    private function getRecentActivity()
    {
        return AdminLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user?->name ?? 'Système',
                    'action' => $log->action,
                    'description' => $log->formatted_description,
                    'severity' => $log->severity,
                    'severity_color' => $log->severity_color,
                    'created_at' => $log->created_at->diffForHumans(),
                    'model_type' => $log->model_type ? class_basename($log->model_type) : null,
                ];
            });
    }

    /**
     * Obtenir les statistiques de sécurité
     */
    private function getSecurityStats()
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();

        return [
            'failed_logins_today' => FailedLoginAttempt::where('attempted_at', '>=', $today)->count(),
            'failed_logins_week' => FailedLoginAttempt::where('attempted_at', '>=', $thisWeek)->count(),
            'blocked_ips' => FailedLoginAttempt::where('attempted_at', '>=', now()->subHour())
                ->groupBy('ip_address')
                ->havingRaw('COUNT(*) >= 5')
                ->distinct('ip_address')
                ->count('ip_address'),
            'admin_logins_today' => AdminLog::where('action', 'login')
                ->where('created_at', '>=', $today)
                ->count(),
            'critical_actions_week' => AdminLog::where('severity', 'critical')
                ->where('created_at', '>=', $thisWeek)
                ->count(),
        ];
    }

    /**
     * Obtenir les données pour les graphiques
     */
    private function getChartData()
    {
        $days = collect(range(29, 0))->map(function ($daysAgo) {
            return now()->subDays($daysAgo)->format('Y-m-d');
        });

        // Épisodes par jour
        $episodesData = Episode::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->pluck('count', 'date');

        // Astuces par jour
        $astucesData = AstucesSoumise::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->pluck('count', 'date');

        // Newsletter par jour
        $newsletterData = NewsletterAbonne::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->pluck('count', 'date');

        return [
            'labels' => $days->map(function ($date) {
                return Carbon::parse($date)->format('d/m');
            })->values(),
            'episodes' => $days->map(function ($date) use ($episodesData) {
                return $episodesData->get($date, 0);
            })->values(),
            'astuces' => $days->map(function ($date) use ($astucesData) {
                return $astucesData->get($date, 0);
            })->values(),
            'newsletter' => $days->map(function ($date) use ($newsletterData) {
                return $newsletterData->get($date, 0);
            })->values(),
        ];
    }

    /**
     * Obtenir les alertes nécessitant une attention
     */
    private function getAlerts()
    {
        $alerts = [];

        // Astuces en attente
        $pendingAstuces = AstucesSoumise::where('status', 'en_attente')->count();
        if ($pendingAstuces > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'lightbulb',
                'title' => 'Astuces en attente',
                'message' => "{$pendingAstuces} astuce(s) en attente de modération",
                'action_url' => route('admin.astuces.index', ['status' => 'en_attente']),
                'action_text' => 'Modérer',
            ];
        }

        // Partenariats en attente
        $pendingPartenariats = Partenariat::where('status', 'en_attente')->count();
        if ($pendingPartenariats > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'handshake',
                'title' => 'Partenariats en attente',
                'message' => "{$pendingPartenariats} demande(s) de partenariat en attente",
                'action_url' => route('admin.partenariats.index', ['status' => 'en_attente']),
                'action_text' => 'Examiner',
            ];
        }

        // Tentatives de connexion suspectes
        $suspiciousLogins = FailedLoginAttempt::where('attempted_at', '>=', now()->subHour())
            ->groupBy('ip_address')
            ->havingRaw('COUNT(*) >= 3')
            ->distinct('ip_address')
            ->count('ip_address');
        
        if ($suspiciousLogins > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'shield-exclamation',
                'title' => 'Activité suspecte',
                'message' => "{$suspiciousLogins} IP(s) avec tentatives de connexion multiples",
                'action_url' => route('admin.security.logs'),
                'action_text' => 'Voir les logs',
            ];
        }

        // Abonnés newsletter en attente de confirmation
        $pendingNewsletter = NewsletterAbonne::where('status', 'en_attente')->count();
        if ($pendingNewsletter > 10) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'envelope',
                'title' => 'Confirmations newsletter',
                'message' => "{$pendingNewsletter} abonnements en attente de confirmation",
                'action_url' => route('admin.newsletter.index', ['status' => 'en_attente']),
                'action_text' => 'Gérer',
            ];
        }

        return $alerts;
    }

    /**
     * API pour obtenir les statistiques en temps réel
     */
    public function getStats(Request $request)
    {
        $type = $request->get('type', 'general');

        switch ($type) {
            case 'security':
                return response()->json($this->getSecurityStats());
            
            case 'activity':
                return response()->json($this->getRecentActivity());
            
            case 'charts':
                return response()->json($this->getChartData());
            
            default:
                return response()->json([
                    'episodes_count' => Episode::count(),
                                'astuces_pending' => AstucesSoumise::where('status', 'en_attente')->count(),
            'partenariats_pending' => Partenariat::where('status', 'en_attente')->count(),
                    'newsletter_active' => NewsletterAbonne::where('status', 'actif')->count(),
                ]);
        }
    }

    /**
     * Exporter les données du dashboard
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $data = [
            'generated_at' => now()->toISOString(),
            'stats' => Cache::get('admin_dashboard_stats'),
            'recent_activity' => $this->getRecentActivity(),
            'security_stats' => $this->getSecurityStats(),
            'chart_data' => $this->getChartData(),
        ];

        switch ($format) {
            case 'csv':
                // Implémentation CSV si nécessaire
                return response()->json(['error' => 'Format CSV non implémenté'], 400);
            
            case 'json':
            default:
                return response()->json($data);
        }
    }

    /**
     * Nettoyer les données anciennes
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:logs,failed_attempts,all'],
            'days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $days = $request->integer('days');
        $type = $request->string('type');
        $deleted = 0;

        if ($type === 'logs' || $type === 'all') {
            $deleted += AdminLog::where('created_at', '<', now()->subDays($days))->delete();
        }

        if ($type === 'failed_attempts' || $type === 'all') {
            $deleted += FailedLoginAttempt::cleanOldAttempts($days);
        }

        // Vider le cache des statistiques
        Cache::forget('admin_dashboard_stats');

        return response()->json([
            'success' => true,
            'deleted_records' => $deleted,
            'message' => "Nettoyage terminé : {$deleted} enregistrement(s) supprimé(s)",
        ]);
    }
}
