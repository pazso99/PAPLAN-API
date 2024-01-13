<?php

namespace App\Models\Recipes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $table = 'recipes.recipes';
    protected $fillable = ['status', 'name', 'slug', 'difficulty', 'time', 'portion', 'ingredients', 'instructions'];
}
