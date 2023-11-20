<?php

namespace App\Models\Spending;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    use HasFactory;

    protected $table = 'spending.transaction_categories';
    protected $fillable = ['status', 'name', 'slug', 'transaction_type'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
