<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'details'    => $this->details,
                        'state' => $this->state,

            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y H:i') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d/m/Y H:i') : null,

            'category'   => [
                'id'   => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'almacen'    => [
                'id'   => $this->almacen?->id,
                'name' => $this->almacen?->name,
            ],
        ];
    }
}
