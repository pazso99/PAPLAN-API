<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'date' => $this->date,
            'transactionType' => $this->transactionCategory->transaction_type,
            'comment' => $this->comment,
            'meta' => $this->meta,
            'transactionCategory' => [
                'id' => $this->transactionCategory->id,
                'name' => $this->transactionCategory->name,
                'slug' => $this->transactionCategory->slug
            ],
            'amount' => $this->amount,
        ];
    }
}
