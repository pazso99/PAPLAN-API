<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'inventory.items';
    protected $fillable = ['status', 'name', 'slug', 'item_type_id', 'expected_lifetime_in_days', 'recommended_stock', 'is_essential'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function packageUnits()
    {
        return $this->belongsToMany(PackageUnit::class, 'inventory.item_package_unit');
    }

    public function itemType()
    {
        return $this->belongsTo(ItemType::class);
    }

    public function purchasedItems()
    {
        return $this->hasMany(PurchasedItem::class);
    }
}
