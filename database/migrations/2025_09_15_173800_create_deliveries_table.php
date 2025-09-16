<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('order_group')->unique();
            $table->unsignedBigInteger('driver_id'); // ID du livreur (utilisateur)
            $table->string('status')->default('assigned'); // assigned, in_delivery, delivered, cancelled
            $table->timestamps();

            $table->foreign('order_group')->references('order_group')->on('orders')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
};
