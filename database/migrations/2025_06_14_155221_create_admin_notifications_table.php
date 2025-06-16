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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // new_astuce, new_partenariat, security_alert, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Données additionnelles
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_read']);
            $table->index(['type', 'created_at']);
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
