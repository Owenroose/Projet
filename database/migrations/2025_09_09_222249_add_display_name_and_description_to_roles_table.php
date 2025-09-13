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
        Schema::table('roles', function (Blueprint $table) {
            // Ajouter display_name si elle n'existe pas
            if (!Schema::hasColumn('roles', 'display_name')) {
                $table->string('display_name')->nullable()->after('name');
            }

            // Ajouter description si elle n'existe pas
            if (!Schema::hasColumn('roles', 'description')) {
                $table->text('description')->nullable()->after('display_name');
            }

            // Ajouter color pour l'interface (optionnel)
            if (!Schema::hasColumn('roles', 'color')) {
                $table->string('color', 7)->nullable()->after('description')->comment('Couleur hex pour l\'interface');
            }

            // Ajouter is_default pour marquer un rôle par défaut (optionnel)
            if (!Schema::hasColumn('roles', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $columnsToRemove = [];

            if (Schema::hasColumn('roles', 'display_name')) {
                $columnsToRemove[] = 'display_name';
            }

            if (Schema::hasColumn('roles', 'description')) {
                $columnsToRemove[] = 'description';
            }

            if (Schema::hasColumn('roles', 'color')) {
                $columnsToRemove[] = 'color';
            }

            if (Schema::hasColumn('roles', 'is_default')) {
                $columnsToRemove[] = 'is_default';
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
