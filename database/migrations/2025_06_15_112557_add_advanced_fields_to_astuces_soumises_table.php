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
        Schema::table('astuces_soumises', function (Blueprint $table) {
            $table->string('categorie')->nullable()->after('titre_astuce');
            $table->enum('difficulte', ['facile', 'moyen', 'difficile'])->nullable()->after('categorie');
            $table->integer('temps_estime')->nullable()->after('difficulte'); // en minutes
            $table->text('materiel_requis')->nullable()->after('description');
            $table->json('etapes')->nullable()->after('materiel_requis');
            $table->text('conseils')->nullable()->after('etapes');
            $table->json('images')->nullable()->after('fichier_joint');
            
            // Renommer commentaires_admin en commentaire_admin pour cohérence
            $table->renameColumn('commentaires_admin', 'commentaire_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astuces_soumises', function (Blueprint $table) {
            $table->dropColumn([
                'categorie',
                'difficulte', 
                'temps_estime',
                'materiel_requis',
                'etapes',
                'conseils',
                'images'
            ]);
            
            $table->renameColumn('commentaire_admin', 'commentaires_admin');
        });
    }
};
