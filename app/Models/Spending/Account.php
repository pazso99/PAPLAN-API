<?php

namespace App\Models\Spending;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'spending.accounts';
    protected $fillable = ['status', 'name', 'slug', 'balance', 'start_balance'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
