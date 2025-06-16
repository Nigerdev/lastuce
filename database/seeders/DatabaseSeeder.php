<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Episode;
use App\Models\AstucesSoumise;
use App\Models\Partenariat;
use App\Models\NewsletterAbonne;
use App\Models\BlogArticle;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Début du seeding de la base de données L\'Astuce...');

        // Créer les épisodes
        $this->seedEpisodes();
        
        // Créer les astuces soumises
        $this->seedAstucesSoumises();
        
        // Créer les demandes de partenariat
        $this->seedPartenariats();
        
        // Créer les abonnés newsletter
        $this->seedNewsletterAbonnes();
        
        // Créer les articles de blog
        $this->seedBlogArticles();

        $this->command->info('✅ Seeding terminé avec succès !');
        $this->displayStatistics();
    }

    /**
     * Seed episodes with different types and statuses.
     */
    private function seedEpisodes(): void
    {
        $this->command->info('📺 Création des épisodes...');

        // Épisodes publiés récents (15)
        Episode::factory(15)
            ->published()
            ->episode()
            ->recent()
            ->create();

        // Épisodes coulisses publiés (8)
        Episode::factory(8)
            ->published()
            ->coulisse()
            ->create();

        // Épisodes bonus publiés (5)
        Episode::factory(5)
            ->published()
            ->bonus()
            ->create();

        // Épisodes programmés (3)
        Episode::factory(3)
            ->scheduled()
            ->episode()
            ->create();

        // Brouillons (4)
        Episode::factory(4)
            ->draft()
            ->create();

        // Quelques épisodes sans YouTube (2)
        Episode::factory(2)
            ->published()
            ->withoutYoutube()
            ->create();

        $this->command->info('   ✓ ' . Episode::count() . ' épisodes créés');
    }

    /**
     * Seed astuces soumises with various statuses.
     */
    private function seedAstucesSoumises(): void
    {
        $this->command->info('💡 Création des astuces soumises...');

        // Astuces en attente (12)
        AstucesSoumise::factory(12)
            ->enAttente()
            ->create();

        // Astuces approuvées (8)
        AstucesSoumise::factory(8)
            ->approuve()
            ->withAdminComments()
            ->create();

        // Astuces rejetées (4)
        AstucesSoumise::factory(4)
            ->rejete()
            ->withAdminComments()
            ->create();

        // Astuces récentes avec fichiers joints (6)
        AstucesSoumise::factory(6)
            ->recent()
            ->withAttachment()
            ->create();

        // Astuces sans fichiers joints (5)
        AstucesSoumise::factory(5)
            ->withoutAttachment()
            ->create();

        $this->command->info('   ✓ ' . AstucesSoumise::count() . ' astuces soumises créées');
    }

    /**
     * Seed partenariats with different statuses.
     */
    private function seedPartenariats(): void
    {
        $this->command->info('🤝 Création des demandes de partenariat...');

        // Nouvelles demandes (5)
        Partenariat::factory(5)
            ->nouveau()
            ->recent()
            ->create();

        // Demandes en cours (3)
        Partenariat::factory(3)
            ->enCours()
            ->withDetailedNotes()
            ->create();

        // Partenariats acceptés (4)
        Partenariat::factory(4)
            ->accepte()
            ->withDetailedNotes()
            ->create();

        // Partenariats refusés (2)
        Partenariat::factory(2)
            ->refuse()
            ->withDetailedNotes()
            ->create();

        $this->command->info('   ✓ ' . Partenariat::count() . ' demandes de partenariat créées');
    }

    /**
     * Seed newsletter subscribers with realistic distribution.
     */
    private function seedNewsletterAbonnes(): void
    {
        $this->command->info('📧 Création des abonnés newsletter...');

        // Abonnés actifs de cette semaine (15)
        NewsletterAbonne::factory(15)
            ->cetteSemaine()
            ->create();

        // Abonnés actifs de ce mois (25)
        NewsletterAbonne::factory(25)
            ->ceMois()
            ->create();

        // Abonnés récents (30)
        NewsletterAbonne::factory(30)
            ->recent()
            ->create();

        // Abonnés anciens actifs (80)
        NewsletterAbonne::factory(80)
            ->ancien()
            ->actif()
            ->create();

        // Abonnés inactifs (15)
        NewsletterAbonne::factory(15)
            ->inactif()
            ->create();

        // Abonnés désabonnés (10)
        NewsletterAbonne::factory(10)
            ->desabonne()
            ->create();

        $this->command->info('   ✓ ' . NewsletterAbonne::count() . ' abonnés newsletter créés');
    }

    /**
     * Seed blog articles with different statuses and dates.
     */
    private function seedBlogArticles(): void
    {
        $this->command->info('📝 Création des articles de blog...');

        // Articles publiés récents (10)
        BlogArticle::factory(10)
            ->published()
            ->recent()
            ->withImage()
            ->create();

        // Articles de coulisses (5)
        BlogArticle::factory(5)
            ->published()
            ->coulisses()
            ->withImage()
            ->create();

        // Articles programmés (3)
        BlogArticle::factory(3)
            ->scheduled()
            ->withImage()
            ->create();

        // Brouillons (4)
        BlogArticle::factory(4)
            ->draft()
            ->create();

        // Articles populaires (anciens mais bons) (6)
        BlogArticle::factory(6)
            ->popular()
            ->withImage()
            ->longContent()
            ->create();

        // Articles sans images (3)
        BlogArticle::factory(3)
            ->published()
            ->withoutImage()
            ->shortContent()
            ->create();

        // Articles distribués sur plusieurs mois pour les archives
        for ($month = 1; $month <= 6; $month++) {
            BlogArticle::factory(2)
                ->fromMonth(2024, $month)
                ->withImage()
                ->create();
        }

        $this->command->info('   ✓ ' . BlogArticle::count() . ' articles de blog créés');
    }

    /**
     * Display statistics after seeding.
     */
    private function displayStatistics(): void
    {
        $this->command->info('📊 Statistiques de la base de données :');
        $this->command->line('');
        
        // Episodes
        $episodeStats = [
            'Total' => Episode::count(),
            'Publiés' => Episode::published()->count(),
            'Brouillons' => Episode::where('is_published', false)->count(),
            'Programmés' => Episode::scheduled()->count(),
        ];
        
        $this->command->info('📺 Épisodes :');
        foreach ($episodeStats as $label => $count) {
            $this->command->line("   {$label}: {$count}");
        }
        
        // Astuces soumises
        $astuceStats = AstucesSoumise::countByStatus();
        $this->command->info('💡 Astuces soumises :');
        $this->command->line("   En attente: {$astuceStats['en_attente']}");
        $this->command->line("   Approuvées: {$astuceStats['approuve']}");
        $this->command->line("   Rejetées: {$astuceStats['rejete']}");
        
        // Partenariats
        $partenaritStats = Partenariat::countByStatus();
        $this->command->info('🤝 Partenariats :');
        $this->command->line("   Nouveaux: {$partenaritStats['nouveau']}");
        $this->command->line("   En cours: {$partenaritStats['en_cours']}");
        $this->command->line("   Acceptés: {$partenaritStats['accepte']}");
        $this->command->line("   Refusés: {$partenaritStats['refuse']}");
        
        // Newsletter
        $newsletterStats = NewsletterAbonne::countByStatus();
        $this->command->info('📧 Newsletter :');
        $this->command->line("   Actifs: {$newsletterStats['actif']}");
        $this->command->line("   Inactifs: {$newsletterStats['inactif']}");
        $this->command->line("   Désabonnés: {$newsletterStats['desabonne']}");
        
        // Blog
        $blogStats = [
            'Total' => BlogArticle::count(),
            'Publiés' => BlogArticle::published()->count(),
            'Brouillons' => BlogArticle::draft()->count(),
            'Programmés' => BlogArticle::scheduled()->count(),
        ];
        
        $this->command->info('📝 Blog :');
        foreach ($blogStats as $label => $count) {
            $this->command->line("   {$label}: {$count}");
        }
        
        $this->command->line('');
        $this->command->info('🎉 La base de données L\'Astuce est maintenant peuplée avec des données de test !');
        $this->command->info('💡 Utilisez ces commandes pour explorer :');
        $this->command->line('   php artisan tinker');
        $this->command->line('   Episode::published()->recent()->get()');
        $this->command->line('   AstucesSoumise::enAttente()->get()');
        $this->command->line('   NewsletterAbonne::actif()->count()');
    }
}
