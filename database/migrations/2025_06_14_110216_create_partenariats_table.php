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
        Schema::create('partenariats', function (Blueprint $table) {
            $table->id();
            $table->string('nom_entreprise');
            $table->string('contact');
            $table->string('email');
            $table->text('message');
            $table->enum('status', ['nouveau', 'en_cours', 'accepte', 'refuse'])->default('nouveau');
            $table->text('notes_internes')->nullable();
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
        Schema::dropIfExists('partenariats');
    }
};
