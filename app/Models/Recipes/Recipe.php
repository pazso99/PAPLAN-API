<?php

namespace App\Models\Recipes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $table = 'recipes.recipes';
    protected $fillable = ['status', 'name', 'slug', 'description', 'time'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
