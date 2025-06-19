<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Episode;
use App\Models\BlogArticle;
use App\Models\NewsletterAbonne;
use App\Models\AstucesSoumise;
use App\Models\Partenariat;

class TestHomeDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:home-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester les données de la page d\'accueil';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TEST DES DONNÉES PAGE D\'ACCUEIL ===');
        $this->newLine();

        try {
            // Test des épisodes
            $this->info('📺 ÉPISODES:');
            $episodesCount = Episode::published()->count();
            $this->line("Total épisodes publiés: {$episodesCount}");
            
            $latestEpisodes = Episode::published()->recent()->limit(6)->get();
            $this->line("Épisodes récupérés pour la page d'accueil: {$latestEpisodes->count()}");
            
            if ($latestEpisodes->count() > 0) {
                $this->line("Premiers épisodes:");
                foreach ($latestEpisodes->take(3) as $episode) {
                    $this->line("  - {$episode->titre} (ID: {$episode->id})");
                }
            } else {
                $this->error("AUCUN épisode récupéré - C'est le problème !");
            }
            $this->newLine();

            // Test des articles de blog
            $this->info('📝 ARTICLES DE BLOG:');
            $blogCount = BlogArticle::count();
            $this->line("Total articles: {$blogCount}");
            
            try {
                $publishedBlogCount = BlogArticle::publishedAndVisible()->count();
                $this->line("Articles publiés et visibles: {$publishedBlogCount}");
            } catch (\Exception $e) {
                $this->error("Erreur avec BlogArticle::publishedAndVisible(): {$e->getMessage()}");
            }
            $this->newLine();

            // Test des statistiques
            $this->info('📊 STATISTIQUES:');
            $stats = [
                'total_episodes' => Episode::published()->count(),
                'newsletter_subscribers' => NewsletterAbonne::actif()->count(),
                'approved_astuces' => AstucesSoumise::approuve()->count(),
            ];
            
            foreach ($stats as $key => $value) {
                $this->line("{$key}: {$value}");
            }
            $this->newLine();

            // Test de l'épisode vedette
            $this->info('⭐ ÉPISODE VEDETTE:');
            $featuredEpisode = Episode::published()
                ->whereNotNull('youtube_url')
                ->recent()
                ->first();
            
            if ($featuredEpisode) {
                $this->line("Épisode vedette trouvé: {$featuredEpisode->titre}");
            } else {
                $this->line("Aucun épisode vedette avec URL YouTube");
            }
            $this->newLine();

            // Test des partenaires
            $this->info('🤝 PARTENAIRES:');
            $partnersCount = Partenariat::where('statut', 'accepte')->count();
            $this->line("Total partenaires acceptés: {$partnersCount}");

        } catch (\Exception $e) {
            $this->error("ERREUR: {$e->getMessage()}");
            $this->error("Fichier: {$e->getFile()}:{$e->getLine()}");
            $this->error("Stack trace:");
            $this->line($e->getTraceAsString());
        }

        return 0;
    }
} 