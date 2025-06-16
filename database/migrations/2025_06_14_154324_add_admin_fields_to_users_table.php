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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email_verified_at');
            $table->string('role')->default('user')->after('is_admin'); // admin, moderator, user
            $table->json('permissions')->nullable()->after('role');
            $table->timestamp('last_login_at')->nullable()->after('permissions');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->integer('failed_login_attempts')->default(0)->after('last_login_ip');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->boolean('two_factor_enabled')->default(false)->after('locked_until');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->json('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_admin',
                'role',
                'permissions',
                'last_login_at',
                'last_login_ip',
                'failed_login_attempts',
                'locked_until',
                'two_factor_enabled',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'deleted_at'
            ]);
        });
    }
};
