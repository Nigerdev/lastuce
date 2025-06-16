<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\AstucesSoumise;
use App\Models\Partenariat;
use App\Models\NewsletterAbonne;
use App\Models\FailedLoginAttempt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Créer une notification pour une nouvelle astuce soumise
     */
    public function notifyNewAstuce(AstucesSoumise $astuce): void
    {
        AdminNotification::createForAllAdmins([
            'type' => 'new_astuce',
            'title' => 'Nouvelle astuce soumise',
            'message' => "Une nouvelle astuce '{$astuce->titre}' a été soumise par {$astuce->nom_soumetteur}",
            'data' => [
                'astuce_id' => $astuce->id,
                'submitter' => $astuce->nom_soumetteur,
                'category' => $astuce->categorie,
            ],
            'action_url' => route('admin.astuces.show', $astuce),
            'action_text' => 'Modérer',
            'priority' => 'normal',
        ]);

        // Envoyer un email aux admins si configuré
        $this->sendEmailNotificationToAdmins(
            'Nouvelle astuce soumise',
            "Une nouvelle astuce '{$astuce->titre}' nécessite votre attention.",
            route('admin.astuces.show', $astuce)
        );
    }

    /**
     * Créer une notification pour une nouvelle demande de partenariat
     */
    public function notifyNewPartenariat(Partenariat $partenariat): void
    {
        AdminNotification::createForAllAdmins([
            'type' => 'new_partenariat',
            'title' => 'Nouvelle demande de partenariat',
            'message' => "Une nouvelle demande de partenariat a été soumise par {$partenariat->nom_entreprise}",
            'data' => [
                'partenariat_id' => $partenariat->id,
                'company' => $partenariat->nom_entreprise,
                'type' => $partenariat->type_partenariat,
            ],
            'action_url' => route('admin.partenariats.show', $partenariat),
            'action_text' => 'Examiner',
            'priority' => 'normal',
        ]);

        // Envoyer un email aux admins
        $this->sendEmailNotificationToAdmins(
            'Nouvelle demande de partenariat',
            "Une nouvelle demande de partenariat de {$partenariat->nom_entreprise} nécessite votre attention.",
            route('admin.partenariats.show', $partenariat)
        );
    }

    /**
     * Créer une notification pour un nouvel abonnement newsletter
     */
    public function notifyNewsletterSignup(NewsletterAbonne $abonne): void
    {
        // Notification seulement si c'est un pic d'inscriptions
        $todaySignups = NewsletterAbonne::whereDate('created_at', today())->count();
        
        if ($todaySignups > 0 && $todaySignups % 10 === 0) { // Tous les 10 nouveaux abonnés
            AdminNotification::createForAllAdmins([
                'type' => 'newsletter_signup',
                'title' => 'Pic d\'inscriptions newsletter',
                'message' => "{$todaySignups} nouvelles inscriptions à la newsletter aujourd'hui",
                'data' => [
                    'count' => $todaySignups,
                    'latest_email' => $abonne->email,
                ],
                'action_url' => route('admin.newsletter.index'),
                'action_text' => 'Voir les abonnés',
                'priority' => 'low',
            ]);
        }
    }

    /**
     * Créer une alerte de sécurité
     */
    public function notifySecurityAlert(string $type, array $data = []): void
    {
        $messages = [
            'multiple_failed_logins' => 'Tentatives de connexion multiples détectées',
            'suspicious_activity' => 'Activité suspecte détectée',
            'ip_blocked' => 'Adresse IP bloquée automatiquement',
            'admin_account_locked' => 'Compte administrateur verrouillé',
        ];

        $message = $messages[$type] ?? 'Alerte de sécurité';
        
        AdminNotification::createForAllAdmins([
            'type' => 'security_alert',
            'title' => 'Alerte de sécurité',
            'message' => $message,
            'data' => array_merge($data, ['alert_type' => $type]),
            'action_url' => route('admin.security.logs'),
            'action_text' => 'Voir les logs',
            'priority' => 'urgent',
        ]);

        // Log de sécurité
        Log::warning("Security alert: {$type}", $data);

        // Email immédiat pour les alertes urgentes
        $this->sendEmailNotificationToAdmins(
            'ALERTE DE SÉCURITÉ',
            $message . '. Vérifiez immédiatement les logs de sécurité.',
            route('admin.security.logs'),
            true // urgent
        );
    }

    /**
     * Créer une notification système
     */
    public function notifySystemEvent(string $type, string $message, array $data = []): void
    {
        $priorities = [
            'backup_completed' => 'low',
            'backup_failed' => 'high',
            'maintenance_mode' => 'normal',
            'system_update' => 'normal',
            'disk_space_low' => 'high',
            'error_rate_high' => 'urgent',
        ];

        AdminNotification::createForAllAdmins([
            'type' => $type,
            'title' => 'Événement système',
            'message' => $message,
            'data' => $data,
            'action_url' => route('admin.settings.index'),
            'action_text' => 'Paramètres',
            'priority' => $priorities[$type] ?? 'normal',
        ]);
    }

    /**
     * Vérifier et créer des alertes automatiques
     */
    public function checkAndCreateAlerts(): void
    {
        // Vérifier les tentatives de connexion suspectes
        $this->checkSuspiciousLogins();
        
        // Vérifier les éléments en attente
        $this->checkPendingItems();
        
        // Vérifier l'espace disque (si applicable)
        $this->checkSystemHealth();
    }

    /**
     * Vérifier les tentatives de connexion suspectes
     */
    private function checkSuspiciousLogins(): void
    {
        $suspiciousIps = FailedLoginAttempt::select('ip_address')
            ->selectRaw('COUNT(*) as attempts')
            ->where('attempted_at', '>=', now()->subHour())
            ->groupBy('ip_address')
            ->havingRaw('COUNT(*) >= 5')
            ->get();

        if ($suspiciousIps->count() > 0) {
            $this->notifySecurityAlert('multiple_failed_logins', [
                'suspicious_ips' => $suspiciousIps->pluck('ip_address')->toArray(),
                'timeframe' => '1 hour',
            ]);
        }
    }

    /**
     * Vérifier les éléments en attente
     */
    private function checkPendingItems(): void
    {
        $pendingAstuces = AstucesSoumise::where('status', 'en_attente')
            ->where('created_at', '<', now()->subDays(3))
            ->count();

        if ($pendingAstuces > 5) {
            AdminNotification::createForAllAdmins([
                'type' => 'pending_moderation',
                'title' => 'Modération en retard',
                'message' => "{$pendingAstuces} astuces en attente depuis plus de 3 jours",
                'data' => ['count' => $pendingAstuces],
                'action_url' => route('admin.astuces.index', ['status' => 'en_attente']),
                'action_text' => 'Modérer',
                'priority' => 'high',
            ]);
        }

        $pendingPartenariats = Partenariat::where('status', 'en_attente')
            ->where('created_at', '<', now()->subWeek())
            ->count();

        if ($pendingPartenariats > 3) {
            AdminNotification::createForAllAdmins([
                'type' => 'pending_partnerships',
                'title' => 'Partenariats en attente',
                'message' => "{$pendingPartenariats} demandes de partenariat en attente depuis plus d'une semaine",
                'data' => ['count' => $pendingPartenariats],
                'action_url' => route('admin.partenariats.index', ['status' => 'en_attente']),
                'action_text' => 'Examiner',
                'priority' => 'normal',
            ]);
        }
    }

    /**
     * Vérifier la santé du système
     */
    private function checkSystemHealth(): void
    {
        // Vérifier l'espace disque (exemple basique)
        $diskUsage = disk_free_space('/') / disk_total_space('/');
        
        if ($diskUsage < 0.1) { // Moins de 10% d'espace libre
            $this->notifySystemEvent(
                'disk_space_low',
                'Espace disque faible: moins de 10% d\'espace libre disponible',
                ['disk_usage_percent' => round((1 - $diskUsage) * 100, 2)]
            );
        }

        // Vérifier les erreurs récentes dans les logs
        $recentErrors = \App\Models\AdminLog::where('severity', 'error')
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentErrors > 10) {
            $this->notifySystemEvent(
                'error_rate_high',
                "Taux d'erreur élevé: {$recentErrors} erreurs dans la dernière heure",
                ['error_count' => $recentErrors]
            );
        }
    }

    /**
     * Envoyer une notification par email aux admins
     */
    private function sendEmailNotificationToAdmins(string $subject, string $message, string $actionUrl = null, bool $urgent = false): void
    {
        // Cette méthode peut être implémentée selon vos besoins d'email
        // Pour l'instant, on log juste l'intention d'envoyer un email
        
        Log::info('Email notification would be sent', [
            'subject' => $subject,
            'message' => $message,
            'action_url' => $actionUrl,
            'urgent' => $urgent,
        ]);

        // Exemple d'implémentation avec Mail:
        /*
        $admins = User::admins()->get();
        
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new AdminNotificationMail(
                $subject,
                $message,
                $actionUrl,
                $urgent
            ));
        }
        */
    }

    /**
     * Nettoyer les anciennes notifications
     */
    public function cleanupOldNotifications(int $days = 30): int
    {
        return AdminNotification::cleanOldNotifications($days);
    }

    /**
     * Marquer toutes les notifications d'un utilisateur comme lues
     */
    public function markAllAsReadForUser(int $userId): int
    {
        return AdminNotification::forUser($userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Obtenir les notifications non lues pour un utilisateur
     */
    public function getUnreadForUser(int $userId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return AdminNotification::forUser($userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
} 