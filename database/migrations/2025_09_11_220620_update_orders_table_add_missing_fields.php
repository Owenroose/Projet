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
        Schema::table('orders', function (Blueprint $table) {
            // Ajouter la référence unique pour la commande
            $table->string('reference')->unique()->nullable()->after('id');

            // Modifier le champ phone pour le rendre obligatoire
            $table->string('phone')->nullable(false)->change();

            // Modifier le champ address pour être de type text (plus long)
            $table->text('address')->change();

            // Changer le statut en enum avec plus d'options
            $table->dropColumn('status');
        });

        // Ajouter le nouveau champ status avec enum
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])
                  ->default('pending')
                  ->after('total_price');
        });

        // Ajouter les nouveaux champs
        Schema::table('orders', function (Blueprint $table) {
            // Priorité de la commande
            $table->boolean('is_priority')->default(false)->after('status');

            // Notes internes (JSON pour stocker un array de notes)
            $table->json('notes')->nullable()->after('is_priority');

            // Horodatage du dernier changement de statut
            $table->timestamp('status_updated_at')->nullable()->after('notes');

            // Index pour optimiser les performances
            $table->index(['status', 'created_at']);
            $table->index(['is_priority', 'status']);
            $table->index(['product_id', 'status']);
            $table->index('email');
            $table->index('phone');
            $table->index('created_at');
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Supprimer les index
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['is_priority', 'status']);
            $table->dropIndex(['product_id', 'status']);
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['reference']);

            // Supprimer les nouveaux champs
            $table->dropColumn([
                'reference',
                'is_priority',
                'notes',
                'status_updated_at'
            ]);

            // Remettre le status original
            $table->dropColumn('status');
        });

        // Recréer l'ancien champ status
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('total_price');

            // Remettre phone nullable
            $table->string('phone')->nullable()->change();

            // Remettre address en string
            $table->string('address')->change();
        });
    }
};
