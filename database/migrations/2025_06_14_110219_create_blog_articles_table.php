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
        Schema::create('blog_articles', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('contenu');
            $table->string('image')->nullable();
            $table->string('slug')->unique();
            $table->timestamp('date_publication')->nullable();
            $table->boolean('is_published')->default(false);
            $table->text('extrait')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();
            
            $table->index(['is_published', 'date_publication']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_articles');
    }
};
