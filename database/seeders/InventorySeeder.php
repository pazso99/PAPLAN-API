<?php

namespace Database\Seeders;

use App\Models\Inventory\Item;
use App\Models\Inventory\ItemType;
use App\Models\Inventory\PackageUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inventory.package_units')->truncate();
        DB::table('inventory.item_types')->truncate();
        DB::table('inventory.items')->truncate();
        DB::table('inventory.item_package_unit')->truncate();

        $inventoryData = include storage_path('/inventoryData.php');

        $packageUnitIds = [];

        foreach ($inventoryData['packageUnits'] as $packageUnitData) {
            $packageUnit = PackageUnit::create([
                'name' => $packageUnitData['name'],
                'slug' => Str::slug($packageUnitData['name'], '-'),
            ]);

            $packageUnitIds[$packageUnitData['name']] = $packageUnit->id;
        }

        foreach ($inventoryData['itemTypes'] as $itemTypeData) {
            $itemType = ItemType::create([
                'name' => $itemTypeData['name'],
                'slug' => Str::slug($itemTypeData['name'], '-'),
            ]);

            foreach ($itemTypeData['items'] as $itemData) {
                $item = Item::create([
                    'status' => false,
                    'name' => $itemData['name'],
                    'slug' => Str::slug($itemData['name'], '-'),
                    'item_type_id' => $itemType->id,
                ]);

                $itemUnitIds = [];
                foreach ($itemData['units'] as $unitName) {
                    $itemUnitIds[] = $packageUnitIds[$unitName];
                }
                $item->packageUnits()->attach($itemUnitIds);
            }
        }
    }
}
