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
        Schema::create('newsletter_abonnes', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('date_inscription')->useCurrent();
            $table->enum('status', ['actif', 'inactif', 'desabonne'])->default('actif');
            $table->string('token_desabonnement')->unique()->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('date_inscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_abonnes');
    }
};
