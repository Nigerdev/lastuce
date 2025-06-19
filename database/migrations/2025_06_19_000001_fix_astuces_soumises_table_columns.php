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
        Schema::table('astuces_soumises', function (Blueprint $table) {
            // Ajouter les colonnes manquantes si elles n'existent pas
            if (!Schema::hasColumn('astuces_soumises', 'status')) {
                $table->enum('status', ['en_attente', 'approuve', 'rejete'])->default('en_attente')->after('images');
            }
            
            if (!Schema::hasColumn('astuces_soumises', 'commentaire_admin')) {
                $table->text('commentaire_admin')->nullable()->after('status');
            }
            
            // S'assurer que toutes les astuces ont un statut par défaut
            DB::statement("UPDATE astuces_soumises SET status = 'en_attente' WHERE status IS NULL OR status = ''");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astuces_soumises', function (Blueprint $table) {
            $table->dropColumn(['status', 'commentaire_admin']);
        });
    }
}; 