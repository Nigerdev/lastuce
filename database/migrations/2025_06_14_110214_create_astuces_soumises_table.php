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
        Schema::create('astuces_soumises', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('email');
            $table->string('titre_astuce');
            $table->text('description');
            $table->string('fichier_joint')->nullable();
            $table->enum('status', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
            $table->text('commentaires_admin')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astuces_soumises');
    }
};
