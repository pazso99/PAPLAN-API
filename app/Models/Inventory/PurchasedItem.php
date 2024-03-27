<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedItem extends Model
{
    use HasFactory;

    protected $table = 'inventory.purchased_items';
    protected $fillable = ['status', 'item_id', 'package_unit_id', 'price', 'amount', 'purchase_date', 'expiration_date', 'leftover_amount_percentage', 'comment'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('leftover_amount_percentage', '>', 0);
    }

    public function scopeOutStock($query)
    {
        return $query->where('leftover_amount_percentage', 0);
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
