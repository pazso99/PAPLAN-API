<?php

namespace App\Models\Spending;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    use HasFactory;

    protected $table = 'spending.transaction_categories';
    protected $fillable = ['status', 'name', 'slug', 'transaction_type'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function categoryGroups()
    {
        return $this->belongsToMany(CategoryGroup::class, 'spending.category_group_transaction_category');
    }
}
