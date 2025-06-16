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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('youtube_url')->nullable();
            $table->enum('type', ['episode', 'coulisse', 'bonus'])->default('episode');
            $table->date('date_diffusion')->nullable();
            $table->string('slug')->unique();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            
            $table->index(['type', 'date_diffusion']);
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
