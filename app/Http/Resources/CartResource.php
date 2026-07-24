<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalPrice = $this->items->sum(function ($item) {
            return $item->product ? $item->product->price * $item->quantity : 0;
        });

        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'total_items' => $this->items->sum('quantity'),
            'total_price' => round($totalPrice, 2),
        ];
    }
}
