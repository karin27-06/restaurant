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
    Schema::create('sales', function (Blueprint $table) {
        $table->id();  // ID de la venta
        $table->foreignId('idCustomer')->constrained('customers');  // Relación con la tabla customers
        $table->foreignId('idOrder')->constrained('orders');  // Relación con la tabla orders
        $table->string('documentType');  // Tipo de pago
        $table->string('paymentType');  // Tipo de pago
        $table->string('operationCode')->nullable();  // Operation code for certain payment methods
        $table->timestamps();  // Fecha de creación y actualización
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
