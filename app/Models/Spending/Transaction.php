<?php

namespace App\Models\Spending;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'spending.transactions';
    protected $fillable = ['status', 'date', 'amount', 'account_id', 'transaction_category_id', 'comment', 'meta'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
