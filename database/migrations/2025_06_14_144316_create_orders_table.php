<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();  // ID del pedido
            $table->unsignedBigInteger('idCustomer');  // Relación con la tabla customers
            $table->unsignedBigInteger('idTable');  // Relación con la tabla tables
            $table->decimal('totalPrice', 10, 2)->nullable();  // Precio total del pedido (acepta null)
            $table->enum('state', ['pendiente', 'en preparación', 'completado'])->default('pendiente');  // Estado del pedido
            $table->timestamps();  // created_at y updated_at
        });

        // Establecer las relaciones
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('idCustomer')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('idTable')->references('id')->on('tables')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['idCustomer']);
            $table->dropForeign(['idTable']);
        });

        Schema::dropIfExists('orders');
    }
}
