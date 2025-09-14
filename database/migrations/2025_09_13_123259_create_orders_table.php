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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_group')->index(); // Pour grouper les commandes multiples
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2); // Prix unitaire du produit
            $table->decimal('subtotal', 10, 2); // Sous-total pour ce produit
            $table->decimal('shipping_fee', 8, 2)->default(0); // Frais de livraison
            $table->decimal('total_amount', 10, 2); // Montant total de la commande

            // Informations FedaPay
            $table->string('fedapay_transaction_id')->nullable();
            $table->string('fedapay_token')->nullable();

            // Statuts
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])
                  ->default('pending');
            $table->enum('payment_status', ['pending', 'approved', 'declined', 'cancelled'])
                  ->default('pending');

            // Informations client
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->string('customer_city');

            // Dates importantes
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Notes additionnelles
            $table->text('notes')->nullable();

            $table->timestamps();

            // Index pour améliorer les performances
            $table->index(['status', 'created_at']);
            $table->index(['payment_status']);
            $table->index(['customer_email']);
            $table->index(['fedapay_transaction_id']);

            // Clé étrangère
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
