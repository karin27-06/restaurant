<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailMovementsInputsTable extends Migration
{
   public function up()
{
    Schema::create('detail_movements_inputs', function (Blueprint $table) {
        $table->id(); // id
        $table->foreignId('idMovementInput')->constrained('movementsInput'); // 'movementsInput' es el nombre de la tabla, no el campo
        $table->foreignId('idInput')->constrained('inputs'); 
        $table->decimal('quantity', 10, 2);
        $table->decimal('totalPrice', 10, 2); 
        $table->decimal('priceUnit', 10, 2);
        $table->string('batch'); // Lote
        $table->date('expirationDate'); 
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('detail_movements_inputs');
    }
}
