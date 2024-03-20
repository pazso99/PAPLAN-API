<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedItem extends Model
{
    use HasFactory;

    protected $table = 'inventory.purchased_items';
    protected $fillable = ['status', 'item_id', 'package_unit_id', 'price', 'amount', 'purchase_date', 'expiration_date', 'leftover_amount_percentage'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function packageUnit()
    {
        return $this->belongsTo(PackageUnit::class);
    }
}
