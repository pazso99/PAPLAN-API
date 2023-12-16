<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'balance' => $this->balance,
            'monthlyTotalIncome' => 'TODO',
            'monthlyTotalExpense' => 'TODO',
            'monthlyBasicExpense' => 'TODO',
            'monthlyPremiumExpense' => 'TODO',
            'latestTransactions' => TransactionResource::collection($this->transactions()->orderByDesc('date')->limit(5)->get()),
        ];
    }
}
