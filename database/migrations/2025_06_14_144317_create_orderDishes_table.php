<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('order_dishes', function (Blueprint $table) {
            $table->id();  // ID del detalle del pedido
            $table->unsignedBigInteger('idOrder');  // Relación con la tabla orders
            $table->unsignedBigInteger('idDishes');  // Relación con la tabla dishes
            $table->integer('quantity');  // Cantidad de platos
            $table->decimal('price', 10, 2);  // Precio del plato
            $table->timestamps();  // created_at y updated_at
        });

        // Establecer la relación con la tabla orders
        Schema::table('order_dishes', function (Blueprint $table) {
            $table->foreign('idOrder')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('idDishes')->references('id')->on('dishes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('order_dishes', function (Blueprint $table) {
            $table->dropForeign(['idOrder']);
            $table->dropForeign(['idDishes']);
        });

        Schema::dropIfExists('order_dishes');
    }
}
