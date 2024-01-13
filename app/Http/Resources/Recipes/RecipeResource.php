<?php

namespace App\Http\Resources\Recipes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
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
            'difficulty' => $this->difficulty,
            'time' => $this->time,
            'portion' => $this->portion,
            'ingredients' => $this->ingredients,
            'instructions' => $this->instructions,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
