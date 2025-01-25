<?php

namespace App\Models\Spending;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryGroup extends Model
{
    use HasFactory;

    protected $table = 'spending.category_groups';
    protected $fillable = ['status', 'name', 'slug'];

    public function transactionCategories()
    {
        return $this->belongsToMany(TransactionCategory::class, 'spending.category_group_transaction_category');
    }
}
