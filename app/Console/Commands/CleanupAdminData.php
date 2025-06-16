<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdminLog;
use App\Models\FailedLoginAttempt;
use App\Models\AdminNotification;
use App\Services\NotificationService;

class CleanupAdminData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:cleanup 
                            {--days=30 : Number of days to keep data}
                            {--type=all : Type of data to cleanup (logs,attempts,notifications,all)}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old admin data (logs, failed attempts, notifications)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');

        $this->info("🧹 Nettoyage des données admin (conservation: {$days} jours)");
        
        if ($dryRun) {
            $this->warn('Mode simulation - aucune donnée ne sera supprimée');
        }

        $totalDeleted = 0;

        // Nettoyer les logs admin
        if ($type === 'all' || $type === 'logs') {
            $deleted = $this->cleanupAdminLogs($days, $dryRun);
            $totalDeleted += $deleted;
            $this->line("📋 Logs admin: {$deleted} enregistrement(s) " . ($dryRun ? 'seraient supprimés' : 'supprimés'));
        }

        // Nettoyer les tentatives de connexion échouées
        if ($type === 'all' || $type === 'attempts') {
            $deleted = $this->cleanupFailedAttempts($days, $dryRun);
            $totalDeleted += $deleted;
            $this->line("🔒 Tentatives échouées: {$deleted} enregistrement(s) " . ($dryRun ? 'seraient supprimés' : 'supprimés'));
        }

        // Nettoyer les notifications
        if ($type === 'all' || $type === 'notifications') {
            $deleted = $this->cleanupNotifications($days, $dryRun);
            $totalDeleted += $deleted;
            $this->line("🔔 Notifications: {$deleted} enregistrement(s) " . ($dryRun ? 'seraient supprimés' : 'supprimés'));
        }

        if (!$dryRun && $totalDeleted > 0) {
            // Créer une notification système
            $notificationService = app(NotificationService::class);
            $notificationService->notifySystemEvent(
                'cleanup_completed',
                "Nettoyage automatique terminé: {$totalDeleted} enregistrement(s) supprimé(s)",
                [
                    'deleted_count' => $totalDeleted,
                    'retention_days' => $days,
                    'cleanup_type' => $type,
                ]
            );
        }

        $this->info("✅ Nettoyage terminé: {$totalDeleted} enregistrement(s) au total " . ($dryRun ? 'seraient supprimés' : 'supprimés'));

        return Command::SUCCESS;
    }

    /**
     * Nettoyer les logs admin
     */
    private function cleanupAdminLogs(int $days, bool $dryRun): int
    {
        $query = AdminLog::where('created_at', '<', now()->subDays($days));
        
        if ($dryRun) {
            return $query->count();
        }

        return $query->delete();
    }

    /**
     * Nettoyer les tentatives de connexion échouées
     */
    private function cleanupFailedAttempts(int $days, bool $dryRun): int
    {
        $query = FailedLoginAttempt::where('attempted_at', '<', now()->subDays($days));
        
        if ($dryRun) {
            return $query->count();
        }

        return $query->delete();
    }

    /**
     * Nettoyer les notifications lues anciennes
     */
    private function cleanupNotifications(int $days, bool $dryRun): int
    {
        $query = AdminNotification::where('created_at', '<', now()->subDays($days))
            ->where('is_read', true);
        
        if ($dryRun) {
            return $query->count();
        }

        return $query->delete();
    }
}
