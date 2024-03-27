<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\PurchasedItemCreateRequest;
use App\Http\Requests\Inventory\PurchasedItemUpdateRequest;
use App\Http\Resources\Inventory\PurchasedItemResource;
use App\Models\Inventory\PurchasedItem;
use App\Models\Inventory\ItemType;

class PurchasedItemController extends Controller
{
    /**
     * Get all Purchased items
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return PurchasedItemResource::collection(PurchasedItem::all());
    }

    /**
     * Save a Purchased item
     *
     * @param \App\Http\Requests\Inventory\PurchasedItemCreateRequest $request
     * @return \App\Http\Resources\Inventory\PurchasedItemResource
     */
    public function store(PurchasedItemCreateRequest $request)
    {
        $purchasedItem = null;

        for ($i = 0; $i < $request->createAmount; $i++) {
            $purchasedItem = PurchasedItem::create([
                'status' => $request->status,
                'item_id' => $request->itemId,
                'package_unit_id' => $request->packageUnitId,
                'amount' => $request->amount,
                'price' => $request->price,
                'purchase_date' => $request->purchaseDate,
                'expiration_date' => $request->expirationDate,
                'leftover_amount_percentage' => $request->leftoverAmountPercentage,
                'comment' => $request->comment,
            ]);
        }

        return PurchasedItemResource::make($purchasedItem);
    }

    /**
     * Get one Purchased item
     *
     * @param \App\Models\Inventory\PurchasedItem $purchasedItem
     * @return \App\Http\Resources\Inventory\PurchasedItemResource
     */
    public function show(PurchasedItem $purchasedItem)
    {
        return PurchasedItemResource::make($purchasedItem);
    }

    /**
     * Update a purchasedItem
     *
     * @param \App\Http\Requests\Inventory\PurchasedItemUpdateRequest $request
     * @param \App\Models\Inventory\PurchasedItem $purchasedItem
     * @return \App\Http\Resources\Inventory\PurchasedItemResource
     */
    public function update(PurchasedItemUpdateRequest $request, PurchasedItem $purchasedItem)
    {
        $purchasedItem->update([
            'status' => $request->status,
            'item_id' => $request->itemId,
            'package_unit_id' => $request->packageUnitId,
            'amount' => $request->amount,
            'price' => $request->price,
            'purchase_date' => $request->purchaseDate,
            'expiration_date' => $request->expirationDate,
            'leftover_amount_percentage' => $request->leftoverAmountPercentage,
            'comment' => $request->comment,
        ]);

        return PurchasedItemResource::make($purchasedItem);
    }

    /**
     * Remove a purchasedItem
     *
     * @param \App\Models\Inventory\PurchasedItem $purchasedItem
     * @return \App\Http\Resources\Inventory\PurchasedItemResource
     */
    public function destroy(PurchasedItem $purchasedItem)
    {
        $purchasedItem->delete();

        return PurchasedItemResource::make($purchasedItem);
    }

    /**
     * Use item
     *
     * @param \App\Models\Inventory\PurchasedItem $purchasedItem
     * @return \App\Http\Resources\Inventory\PurchasedItemResource
     */
    public function useItem(PurchasedItem $purchasedItem)
    {
        $purchasedItem->update([
            'leftover_amount_percentage' => 0,
            'expiration_date' => now(),
        ]);

        return PurchasedItemResource::make($purchasedItem);
    }

}
