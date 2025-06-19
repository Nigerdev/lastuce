<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mettre à jour les épisodes existants
        DB::statement("
            UPDATE episodes 
            SET 
                statut = 'published',
                date_publication = COALESCE(date_publication, date_diffusion, NOW())
            WHERE statut IS NULL OR statut = 'draft'
        ");
        
        // S'assurer que tous les épisodes ont une date de publication
        DB::statement("
            UPDATE episodes 
            SET date_publication = COALESCE(date_publication, created_at, NOW())
            WHERE date_publication IS NULL
        ");
        
        // Mettre à jour les astuces soumises
        DB::statement("
            UPDATE astuces_soumises 
            SET status = 'en_attente'
            WHERE status IS NULL OR status = ''
        ");
        
        // Approuver quelques astuces pour tester
        DB::statement("
            UPDATE astuces_soumises 
            SET status = 'approuve'
            WHERE id IN (
                SELECT id FROM (
                    SELECT id FROM astuces_soumises 
                    ORDER BY created_at DESC 
                    LIMIT 10
                ) as tmp
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionnel : reverter les changements si nécessaire
        DB::statement("UPDATE episodes SET statut = 'draft' WHERE statut = 'published'");
        DB::statement("UPDATE astuces_soumises SET status = 'en_attente'");
    }
}; 