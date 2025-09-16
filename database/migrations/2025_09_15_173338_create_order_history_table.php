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
        Schema::create('order_history', function (Blueprint $table) {
            $table->id();
            $table->string('order_group');
            $table->string('status_before')->nullable();
            $table->string('status_after');
            $table->string('action'); // e.g., 'status_update', 'delivery_assignment'
            $table->unsignedBigInteger('user_id'); // L'utilisateur (admin ou livreur) qui a fait l'action
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('order_group')->references('order_group')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_history');
    }
};
