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


    // /**
    //  * Returns the active item categories.
    //  *
    //  * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    //  */
    // public function getStockCategories()
    // {
    //     $categories = [];

    //     foreach (Item::active()->get() as $item) {
    //         if (!isset($categories[$item->category->slug])) {
    //             $categories[$item->category->slug] = [
    //                 'id' => $item->category->id,
    //                 'slug' => $item->category->slug,
    //                 'name' => $item->category->name,
    //             ];
    //         }
    //     }

    //     return response()->json([
    //         'data' => array_values($categories)
    //     ]);
    // }

    // /**
    //  * Get items by category.
    //  *
    //  * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    //  */
    // public function getStockItemsByCategory($category)
    // {

    //     if ($category == 'all') {
    //         $items = Item::active()->get();
    //         $category = 'all';
    //     } else {
    //         $items = Item::active()->where('category_id', Category::where('slug', $category)->first()->id)->get();
    //         $category = Category::where('slug', $category)->first();
    //     }

    //     $stockItems = [];

    //     foreach ($items as $item) {
    //         $purchasedItems = $item->purchasedItems()->orderBy('expiration_date', 'desc')->get();

    //         $inStockItems = [];
    //         $historyStockItems = [];
    //         foreach ($purchasedItems as $purchasedItem) {
    //             $stockItem = [
    //                 'id' => $purchasedItem->id,
    //                 'name' => $purchasedItem->item->name,
    //                 'purchase_date' => $purchasedItem->purchase_date,
    //                 'expiration_date' => $purchasedItem->expiration_date,
    //                 'price' => $purchasedItem->price,
    //             ];

    //             $purchasedItem->status ?
    //                 $inStockItems[] = $stockItem :
    //                 $historyStockItems[] = $stockItem;
    //         }
    //         $inStock = count($inStockItems);

    //         $stockItems[] = [
    //             'id' => $item->id,
    //             'name' => $item->name,
    //             'slug' => $item->slug,
    //             'stock_status' => $inStock >= $item->recommended_stock ? 'in_stock' : ($inStock == 0 ? 'out' : 'running_out'),
    //             'in_stock' => $inStock,
    //             'expected_to_run_out_at' => $inStockItems[0]['expiration_date'] ?? null,
    //             'recommended_stock' => $item->recommended_stock,
    //             'ran_out_at' => $inStock == 0 && count($historyStockItems) > 0 ? $historyStockItems[0]['expiration_date'] : null,
    //             'last_purchase' => null,
    //         ];
    //     }

    //     return response()->json([
    //         'data' => $stockItems,
    //         'category' => $category
    //     ]);
    // }

    // /**
    //  * Get item stock data.
    //  *
    //  * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    //  */
    // public function getStockItem($category, $item)
    // {
    //     $itemParam = Item::where('slug', $item)->first();
    //     if ($category != 'all') {
    //         $categoryParam = Category::where('slug', $category)->first();

    //         if ($categoryParam == null || $itemParam == null || $itemParam->category_id !== $categoryParam->id) {
    //             return response('not found', 404);
    //         }
    //     }

    //     $purchasedItems = Item::where('slug', $item)->first()->purchasedItems()->orderBy('expiration_date', 'desc')->get();
    //     $inStockItems = [];
    //     $historyStockItems = [];

    //     foreach ($purchasedItems as $purchasedItem) {
    //         $stockItem = [
    //             'id' => $purchasedItem->id,
    //             'name' => $purchasedItem->item->name,
    //             'purchase_date' => $purchasedItem->purchase_date,
    //             'expiration_date' => $purchasedItem->expiration_date,
    //             'amount_percentage' => $purchasedItem->amount_percentage,
    //             'price' => $purchasedItem->price,
    //         ];

    //         $purchasedItem->status ?
    //             $inStockItems[] = $stockItem :
    //             $historyStockItems[] = $stockItem;
    //     }

    //     return response()->json([
    //         'data' => [
    //             'current_stock' => $inStockItems,
    //             'stock_history' => $historyStockItems,
    //         ],
    //         'item' => $itemParam
    //     ]);
    // }
}
