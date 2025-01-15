<?php

namespace App\Http\Resources\Spending;

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
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'date' => $this->date,
            'comment' => $this->comment,
            'meta' => $this->meta,
            'transactionCategory' => [
                'id' => $this->transactionCategory->id,
                'name' => $this->transactionCategory->name,
                'transactionType' => $this->transactionCategory->transaction_type,
            ],
            'account' => [
                'id' => $this->account->id,
                'status' => $this->account->status,
                'name' => $this->account->name,
                'balance' => $this->account->balance,
            ],
            'amount' => $this->amount,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
