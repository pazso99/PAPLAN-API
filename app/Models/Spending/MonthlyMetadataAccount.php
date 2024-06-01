<?php

namespace App\Models\Spending;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyMetadataAccount extends Model
{
    use HasFactory;

    protected $table = 'spending.monthly_metadata_accounts';
    protected $fillable = ['monthly_metadata_id', 'account_id', 'balance', 'income', 'basic_expense', 'premium_expense', 'transfer'];
    public $timestamps = false;

    public function monthlyMetadata()
    {
        return $this->belongsTo(MonthlyMetadata::class);
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }
}
