<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'idSale' => $this->idSale,
            'idOrder' => $this->idOrder,
            'sale' => new SaleResource($this->sale),  // Relación con la venta
            'order' => new OrderResource($this->order),  // Usando un recurso para la orden
            'subtotal' => number_format($this->subtotal, 2),
        ];
    }
}
