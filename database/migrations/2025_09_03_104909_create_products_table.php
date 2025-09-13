<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('specifications')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('brand')->nullable();
            $table->string('category');
            $table->string('image')->nullable();
            $table->boolean('in_stock')->default(true);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
