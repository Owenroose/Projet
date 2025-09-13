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
            // Vérifier et ajouter les colonnes seulement si elles n'existent pas

            if (!Schema::hasColumn('users', 'last_seen_at')) {
                $table->timestamp('last_seen_at')->nullable()->after('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'login_count')) {
                $table->integer('login_count')->default(0)->after('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToRemove = [];

            if (Schema::hasColumn('users', 'last_seen_at')) {
                $columnsToRemove[] = 'last_seen_at';
            }

            if (Schema::hasColumn('users', 'is_active')) {
                $columnsToRemove[] = 'is_active';
            }

            if (Schema::hasColumn('users', 'login_count')) {
                $columnsToRemove[] = 'login_count';
            }

            if (Schema::hasColumn('users', 'last_login_at')) {
                $columnsToRemove[] = 'last_login_at';
            }

            if (Schema::hasColumn('users', 'phone')) {
                $columnsToRemove[] = 'phone';
            }

            if (Schema::hasColumn('users', 'avatar')) {
                $columnsToRemove[] = 'avatar';
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
