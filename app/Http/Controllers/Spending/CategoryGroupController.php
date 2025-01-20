<?php

namespace App\Http\Controllers\Spending;

use App\Http\Controllers\Controller;
use App\Models\Spending\CategoryGroup;
use App\Http\Resources\Spending\CategoryGroupResource;
use App\Http\Requests\Spending\CategoryGroupCreateRequest;
use App\Http\Requests\Spending\CategoryGroupUpdateRequest;
use Illuminate\Support\Str;

class CategoryGroupController extends Controller
{
    /**
     * Get all category groups
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CategoryGroupResource::collection(CategoryGroup::all());
    }

    /**
     * Save category group
     *
     * @param \App\Http\Requests\Spending\CategoryGroupCreateRequest $request
     * @return \App\Http\Resources\Spending\CategoryGroupResource
     */
    public function store(CategoryGroupCreateRequest $request)
    {
        $categoryGroup = CategoryGroup::create([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        $categoryGroup->transactionCategories()->attach($request->transactionCategoryIds);

        return CategoryGroupResource::make($categoryGroup);
    }

    /**
     * Get one category group
     *
     * @param \App\Models\Spending\CategoryGroup $categoryGroup
     * @return \App\Http\Resources\Spending\CategoryGroupResource
     */
    public function show(CategoryGroup $categoryGroup)
    {
        return CategoryGroupResource::make($categoryGroup);
    }

    /**
     * Update one category group
     *
     * @param \App\Http\Requests\Spending\CategoryGroupUpdateRequest $request
     * @param \App\Models\Spending\CategoryGroup $categoryGroup
     * @return \App\Http\Resources\Spending\CategoryGroupResource
     */
    public function update(CategoryGroupUpdateRequest $request, CategoryGroup $categoryGroup)
    {
        $categoryGroup->update([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        $categoryGroup->transactionCategories()->detach();
        $categoryGroup->transactionCategories()->attach($request->transactionCategoryIds);

        return CategoryGroupResource::make($categoryGroup);
    }

    /**
     * Delete category group
     *
     * @param \App\Models\Spending\CategoryGroup $categoryGroup
     * @return \App\Http\Resources\Spending\CategoryGroupResource
     */
    public function destroy(CategoryGroup $categoryGroup)
    {
        $categoryGroup->transactionCategories()->detach();
        $categoryGroup->delete();

        return CategoryGroupResource::make($categoryGroup);
    }
}
