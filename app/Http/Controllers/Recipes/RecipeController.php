<?php

namespace App\Http\Controllers\Recipes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipes\RecipeCreateRequest;
use App\Http\Requests\Recipes\RecipeUpdateRequest;
use App\Http\Resources\Recipes\RecipeResource;
use App\Models\Recipes\Recipe;
use Illuminate\Support\Str;

class RecipeController extends Controller
{
    /**
     * Get all recipes
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return RecipeResource::collection(Recipe::all());
    }

    /**
     * Save a recipe
     *
     * @param \App\Http\Requests\Recipes\RecipeCreateRequest $request
     * @return \App\Http\Resources\Recipes\RecipeResource
     */
    public function store(RecipeCreateRequest $request)
    {
        $recipe = Recipe::create([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'time' => $request->time,
            'description' => $request->description,
        ]);

        return RecipeResource::make($recipe);
    }

    /**
     * Get one recipe
     *
     * @param \App\Models\Recipes\Recipe $recipe
     * @return \App\Http\Resources\Recipes\RecipeResource
     */
    public function show(Recipe $recipe)
    {
        return RecipeResource::make($recipe);
    }

    /**
     * Update a recipe
     *
     * @param \App\Http\Requests\Recipes\RecipeUpdateRequest $request
     * @param \App\Models\Recipes\Recipe $recipe
     * @return \App\Http\Resources\Recipes\RecipeResource
     */
    public function update(RecipeUpdateRequest $request, Recipe $recipe)
    {
        $recipe->update([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'time' => $request->time,
            'description' => $request->description,
        ]);

        return RecipeResource::make($recipe);
    }

    /**
     * Remove a recipe
     *
     * @param \App\Models\Recipes\Recipe $recipe
     * @return \App\Http\Resources\Recipes\RecipeResource
     */
    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return RecipeResource::make($recipe);
    }
}
