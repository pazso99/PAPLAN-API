<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'itemType' => [
                'id' => $this->itemType->id,
                'name' => $this->itemType->name
            ],
            'packageUnits' => PackageUnitResource::collection($this->packageUnits),
            'expectedLifetimeInDays' => $this->expected_lifetime_in_days,
            'recommendedStock' => $this->recommended_stock,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
