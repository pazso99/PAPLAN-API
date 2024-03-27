<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageUnit extends Model
{
    use HasFactory;

    protected $table = 'inventory.package_units';
    protected $fillable = ['status', 'name', 'slug'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'inventory.item_package_unit');
    }

    public function purchasedItems()
    {
        return $this->hasMany(PurchasedItem::class);
    }
}
