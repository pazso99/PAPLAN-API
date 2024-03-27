<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    use HasFactory;

    protected $table = 'inventory.item_types';
    protected $fillable = ['status', 'name', 'slug'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
