<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\ItemTypeCreateRequest;
use App\Http\Requests\Inventory\ItemTypeUpdateRequest;
use App\Http\Resources\Inventory\ItemTypeResource;
use App\Models\Inventory\ItemType;

class ItemTypeController extends Controller
{
    /**
     * Get all item types
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return ItemTypeResource::collection(ItemType::all());
    }

    /**
     * Save a item type
     *
     * @param \App\Http\Requests\Inventory\ItemTypeCreateRequest $request
     * @return \App\Http\Resources\Inventory\ItemTypeResource
     */
    public function store(ItemTypeCreateRequest $request)
    {
        $itemType = ItemType::create([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        return ItemTypeResource::make($itemType);
    }

    /**
     * Get one item type
     *
     * @param \App\Models\Inventory\ItemType $itemType
     * @return \App\Http\Resources\Inventory\ItemTypeResource
     */
    public function show(ItemType $itemType)
    {
        return ItemTypeResource::make($itemType);
    }

    /**
     * Update a item type
     *
     * @param \App\Http\Requests\Inventory\ItemTypeUpdateRequest $request
     * @param \App\Models\Inventory\ItemType $itemType
     * @return \App\Http\Resources\Inventory\ItemTypeResource
     */
    public function update(ItemTypeUpdateRequest $request, ItemType $itemType)
    {
        $itemType->update([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        return ItemTypeResource::make($itemType);
    }

    /**
     * Remove a item type
     *
     * @param \App\Models\Inventory\ItemType $itemType
     * @return \App\Http\Resources\Inventory\ItemTypeResource
     */
    public function destroy(ItemType $itemType)
    {
        $itemType->delete();

        return ItemTypeResource::make($itemType);
    }
}
