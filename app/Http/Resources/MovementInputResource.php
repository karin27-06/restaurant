<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementInputResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'issue_date' => Carbon::parse($this->issue_date)->format('d-m-Y'),
            'execution_date' => Carbon::parse($this->execution_date)->format('d-m-Y'),
            'supplier_id' => $this->supplier_id,
            'supplier_name' => $this->supplier ? $this->supplier->name : null,
            'user_id' => $this->user_id,
            'user_name' => $this->user ? $this->user->name : null,
            'movement_type' => $this->movement_type,
            'state' => $this->state,
            'igv_state' => $this->igv_state,
            'payment_type' => $this->payment_type,
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
        ];
    }
}
