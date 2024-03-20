<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\ItemCreateRequest;
use App\Http\Requests\Inventory\ItemUpdateRequest;
use App\Http\Resources\Inventory\ItemResource;
use App\Models\Inventory\Item;

class ItemController extends Controller
{
    /**
     * Get all items
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return ItemResource::collection(Item::all());
    }

    /**
     * Save a item
     *
     * @param \App\Http\Requests\Inventory\ItemCreateRequest $request
     * @return \App\Http\Resources\Inventory\ItemResource
     */
    public function store(ItemCreateRequest $request)
    {
        $item = Item::create([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'item_type_id' => $request->itemTypeId,
            'expected_lifetime_in_days' => $request->expectedLifetimeInDays,
            'recommended_stock' => $request->recommendedStock,
        ]);

        $item->packageUnits()->attach($request->packageUnitIds);

        return ItemResource::make($item);
    }

    /**
     * Get one item
     *
     * @param \App\Models\Inventory\Item $item
     * @return \App\Http\Resources\Inventory\ItemResource
     */
    public function show(Item $item)
    {
        return ItemResource::make($item);
    }

    /**
     * Update a item
     *
     * @param \App\Http\Requests\Inventory\ItemUpdateRequest $request
     * @param \App\Models\Inventory\Item $item
     * @return \App\Http\Resources\Inventory\ItemResource
     */
    public function update(ItemUpdateRequest $request, Item $item)
    {
        $item->update([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'item_type_id' => $request->itemTypeId,
            'expected_lifetime_in_days' => $request->expectedLifetimeInDays,
            'recommended_stock' => $request->recommendedStock,
        ]);

        $item->packageUnits()->detach();
        $item->packageUnits()->attach($request->packageUnitIds);

        return ItemResource::make($item);
    }

    /**
     * Remove a item
     *
     * @param \App\Models\Inventory\Item $item
     * @return \App\Http\Resources\Inventory\ItemResource
     */
    public function destroy(Item $item)
    {
        $item->packageUnits()->detach();
        $item->delete();

        return ItemResource::make($item);
    }
}
