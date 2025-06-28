<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'idMesa' => $this->table->id,
            'numeroMesa' => $this->table->tablenum,
            'idCliente' => $this->customer->id,
            'cliente' => $this->customer->name,
            'idUsuario' => $this->user->id,
            'nombreUsuario' => $this->user->name,
            'state' => $this->state,
            'creacion' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'actualizacion' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
            'orderDishes' => OrderDishResource::collection($this->whenLoaded('orderDishes')), // Incluyendo los platos en el pedido

        ];
    }
}
