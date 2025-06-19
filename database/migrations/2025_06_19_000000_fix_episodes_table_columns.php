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
        Schema::table('episodes', function (Blueprint $table) {
            // Ajouter les colonnes manquantes du modèle
            if (!Schema::hasColumn('episodes', 'statut')) {
                $table->enum('statut', ['draft', 'scheduled', 'published', 'archived'])->default('draft')->after('type');
            }
            
            if (!Schema::hasColumn('episodes', 'date_publication')) {
                $table->datetime('date_publication')->nullable()->after('statut');
            }
            
            if (!Schema::hasColumn('episodes', 'vues')) {
                $table->integer('vues')->default(0)->after('date_publication');
            }
            
            if (!Schema::hasColumn('episodes', 'duree')) {
                $table->integer('duree')->nullable()->comment('Durée en secondes')->after('vues');
            }
            
            if (!Schema::hasColumn('episodes', 'contenu')) {
                $table->longText('contenu')->nullable()->after('duree');
            }
            
            if (!Schema::hasColumn('episodes', 'thumbnail_url')) {
                $table->string('thumbnail_url')->nullable()->after('contenu');
            }
            
            if (!Schema::hasColumn('episodes', 'tags')) {
                $table->json('tags')->nullable()->after('thumbnail_url');
            }
            
            if (!Schema::hasColumn('episodes', 'category')) {
                $table->string('category')->nullable()->after('tags');
            }
            
            if (!Schema::hasColumn('episodes', 'audio_url')) {
                $table->string('audio_url')->nullable()->after('youtube_url');
            }
            
            // Mettre à jour les index
            $table->index(['statut', 'date_publication']);
        });
        
        // Migrer les données existantes
        DB::statement("UPDATE episodes SET statut = CASE WHEN is_published = 1 THEN 'published' ELSE 'draft' END WHERE statut IS NULL");
        DB::statement("UPDATE episodes SET date_publication = date_diffusion WHERE date_publication IS NULL AND date_diffusion IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropColumn(['statut', 'date_publication', 'vues', 'duree', 'contenu', 'thumbnail_url', 'tags', 'category', 'audio_url']);
        });
    }
}; 