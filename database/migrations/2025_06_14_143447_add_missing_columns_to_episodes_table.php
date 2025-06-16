<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            // Ajouter les colonnes manquantes
            $table->enum('statut', ['draft', 'scheduled', 'published', 'archived'])->default('draft')->after('type');
            $table->datetime('date_publication')->nullable()->after('date_diffusion');
            $table->text('contenu')->nullable()->after('description');
            $table->integer('duree')->nullable()->comment('Durée en secondes')->after('contenu');
            $table->integer('vues')->default(0)->after('duree');
            $table->string('thumbnail_url')->nullable()->after('youtube_url');
            $table->json('tags')->nullable()->after('thumbnail_url');
            $table->string('category')->nullable()->after('tags');
            
            // Ajouter des index pour les performances
            $table->index(['statut', 'date_publication']);
            $table->index('vues');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropIndex(['statut', 'date_publication']);
            $table->dropIndex(['vues']);
            $table->dropColumn([
                'statut',
                'date_publication', 
                'contenu',
                'duree',
                'vues',
                'thumbnail_url',
                'tags',
                'category'
            ]);
        });
    }
};
