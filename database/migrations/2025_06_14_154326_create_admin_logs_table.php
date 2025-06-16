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
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->string('model_type')->nullable(); // Episode, AstucesSoumise, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE
            $table->text('description')->nullable();
            $table->string('severity')->default('info'); // info, warning, error, critical
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index('severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
