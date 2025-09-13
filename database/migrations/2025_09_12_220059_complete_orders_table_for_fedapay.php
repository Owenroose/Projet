<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Ajouter les nouveaux champs FedaPay
            $table->string('order_group')->after('id')->nullable();
            $table->string('payment_method')->after('status')->nullable();
            $table->string('fedapay_token')->after('payment_method')->nullable();
            $table->string('fedapay_transaction_id')->after('fedapay_token')->nullable();
            $table->enum('payment_status', [
                'pending', 'processing', 'approved', 'completed', 'failed', 'cancelled', 'refunded'
            ])->default('pending')->after('fedapay_transaction_id');
            $table->decimal('shipping_fee', 8, 2)->default(0)->after('total_price');
            $table->decimal('final_amount', 10, 2)->nullable()->after('shipping_fee');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
            $table->json('callback_data')->nullable()->after('paid_at');
        });

        // Ajouter les index
        Schema::table('orders', function (Blueprint $table) {
            $table->index('order_group');
            $table->index('payment_status');
            $table->index('fedapay_transaction_id');
        });

        // Mettre à jour les enregistrements existants
        DB::table('orders')->whereNull('order_group')->update([
            'order_group' => DB::raw('UUID()'),
            'final_amount' => DB::raw('total_price + shipping_fee')
        ]);

        // Rendre order_group non nullable après mise à jour
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_group')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Supprimer les index
            $table->dropIndex(['order_group']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['fedapay_transaction_id']);

            // Supprimer les colonnes
            $table->dropColumn([
                'order_group',
                'payment_method',
                'fedapay_token',
                'fedapay_transaction_id',
                'payment_status',
                'shipping_fee',
                'final_amount',
                'paid_at',
                'callback_data'
            ]);
        });
    }
};
