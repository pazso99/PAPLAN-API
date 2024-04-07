<?php

namespace App\Models\Spending;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyMetadata extends Model
{
    use HasFactory;

    protected $table = 'spending.monthly_metadata';
    protected $fillable = ['year', 'month', 'total_balance', 'total_income', 'total_basic_expense', 'total_premium_expense'];

    public function monthlyMetadataAccounts()
    {
        return $this->hasMany(MonthlyMetadataAccount::class);
    }
}
