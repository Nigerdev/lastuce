<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class CheckSecurityAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:check-security {--detailed : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for security alerts and create notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $detailed = $this->option('detailed');
        
        if ($detailed) {
            $this->info('🔍 Vérification des alertes de sécurité...');
        }

        $notificationService = app(NotificationService::class);
        
        try {
            $notificationService->checkAndCreateAlerts();
            
            if ($detailed) {
                $this->info('✅ Vérification terminée avec succès');
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la vérification: ' . $e->getMessage());
            
            // Créer une notification d'erreur système
            $notificationService->notifySystemEvent(
                'security_check_failed',
                'Échec de la vérification automatique des alertes de sécurité: ' . $e->getMessage(),
                ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]
            );
            
            return Command::FAILURE;
        }
    }
}
