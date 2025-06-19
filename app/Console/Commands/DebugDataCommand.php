<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Episode;
use App\Models\AstucesSoumise;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DebugDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:data {--table=all : Quelle table déboguer (episodes|astuces|all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Déboguer les données de l\'application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->option('table');

        $this->info('=== DÉBOGAGE DES DONNÉES ===');
        $this->newLine();

        if ($table === 'all' || $table === 'episodes') {
            $this->debugEpisodes();
        }

        if ($table === 'all' || $table === 'astuces') {
            $this->debugAstuces();
        }

        if ($table === 'all') {
            $this->debugDatabase();
        }

        return 0;
    }

    private function debugEpisodes()
    {
        $this->info('📺 ÉPISODES:');
        
        // Vérifier la structure de la table
        $columns = Schema::getColumnListing('episodes');
        $this->line('Colonnes disponibles: ' . implode(', ', $columns));
        
        // Compter tous les épisodes
        $totalEpisodes = DB::table('episodes')->count();
        $this->line("Total des épisodes en base: {$totalEpisodes}");
        
        // Compter par statut si la colonne existe
        if (in_array('statut', $columns)) {
            $byStatus = DB::table('episodes')
                ->select('statut', DB::raw('count(*) as count'))
                ->groupBy('statut')
                ->get();
            
            $this->line('Répartition par statut:');
            foreach ($byStatus as $status) {
                $this->line("  - {$status->statut}: {$status->count}");
            }
        }
        
        if (in_array('is_published', $columns)) {
            $published = DB::table('episodes')->where('is_published', true)->count();
            $draft = DB::table('episodes')->where('is_published', false)->count();
            $this->line("Par is_published: Publiés={$published}, Brouillons={$draft}");
        }
        
        // Tester le scope published
        try {
            $publishedViaScope = Episode::published()->count();
            $this->line("Épisodes via scope published(): {$publishedViaScope}");
        } catch (\Exception $e) {
            $this->error("Erreur avec le scope published(): {$e->getMessage()}");
        }
        
        // Afficher quelques épisodes
        $this->line('Premiers épisodes:');
        $episodes = DB::table('episodes')->limit(5)->get();
        foreach ($episodes as $episode) {
            $this->line("  - ID: {$episode->id}, Titre: {$episode->titre}");
        }
        
        $this->newLine();
    }

    private function debugAstuces()
    {
        $this->info('💡 ASTUCES SOUMISES:');
        
        // Vérifier la structure de la table
        $columns = Schema::getColumnListing('astuces_soumises');
        $this->line('Colonnes disponibles: ' . implode(', ', $columns));
        
        // Compter toutes les astuces
        $totalAstuces = DB::table('astuces_soumises')->count();
        $this->line("Total des astuces en base: {$totalAstuces}");
        
        // Compter par statut si la colonne existe
        if (in_array('status', $columns)) {
            $byStatus = DB::table('astuces_soumises')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();
            
            $this->line('Répartition par statut:');
            foreach ($byStatus as $status) {
                $this->line("  - {$status->status}: {$status->count}");
            }
        }
        
        // Tester les scopes
        try {
            $enAttente = AstucesSoumise::enAttente()->count();
            $this->line("Astuces en attente via scope: {$enAttente}");
        } catch (\Exception $e) {
            $this->error("Erreur avec le scope enAttente(): {$e->getMessage()}");
        }
        
        // Afficher quelques astuces
        $this->line('Premières astuces:');
        $astuces = DB::table('astuces_soumises')->limit(5)->get();
        foreach ($astuces as $astuce) {
            $this->line("  - ID: {$astuce->id}, Titre: {$astuce->titre_astuce}");
        }
        
        $this->newLine();
    }

    private function debugDatabase()
    {
        $this->info('🗄️ BASE DE DONNÉES:');
        
        // Configuration de la base
        $config = config('database.connections.' . config('database.default'));
        $this->line("Connexion: {$config['driver']}");
        $this->line("Host: {$config['host']}:{$config['port']}");
        $this->line("Database: {$config['database']}");
        $this->line("Username: {$config['username']}");
        
        // Test de connexion
        try {
            DB::connection()->getPdo();
            $this->info('✅ Connexion à la base de données OK');
        } catch (\Exception $e) {
            $this->error("❌ Erreur de connexion: {$e->getMessage()}");
        }
        
        // Lister toutes les tables
        $tables = DB::select('SHOW TABLES');
        $this->line('Tables disponibles:');
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            $count = DB::table($tableName)->count();
            $this->line("  - {$tableName}: {$count} enregistrements");
        }
        
        $this->newLine();
    }
} 