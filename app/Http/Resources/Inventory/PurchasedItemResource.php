<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchasedItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'item' => [
                'id' => $this->item->id,
                'name' => $this->item->name,
                'itemType' => [
                    'id' => $this->item->itemType->id,
                    'name' => $this->item->itemType->name
                ],
                'expectedLifetimeInDays' => $this->item->expected_lifetime_in_days,
                'recommendedStock' => $this->item->recommended_stock,
                'isEssential' => $this->item->is_essential,
            ],
            'packageUnit' => [
                'id' => $this->packageUnit->id,
                'name' => $this->packageUnit->name
            ],
            'amount' => $this->amount,
            'price' => $this->price,
            'purchaseDate' => $this->purchase_date,
            'expirationDate' => $this->expiration_date,
            'leftoverAmountPercentage' => $this->leftover_amount_percentage,
            'comment' => $this->comment,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
