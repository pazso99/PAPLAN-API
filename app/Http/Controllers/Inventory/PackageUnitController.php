<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\PackageUnitCreateRequest;
use App\Http\Requests\Inventory\PackageUnitUpdateRequest;
use App\Http\Resources\Inventory\PackageUnitResource;
use App\Models\Inventory\PackageUnit;

class PackageUnitController extends Controller
{
    /**
     * Get all package units
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return PackageUnitResource::collection(PackageUnit::all());
    }

    /**
     * Save a package unit
     *
     * @param \App\Http\Requests\Inventory\PackageUnitCreateRequest $request
     * @return \App\Http\Resources\Inventory\PackageUnitResource
     */
    public function store(PackageUnitCreateRequest $request)
    {
        $packageUnit = PackageUnit::create([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        return PackageUnitResource::make($packageUnit);
    }

    /**
     * Get one package unit
     *
     * @param \App\Models\Inventory\PackageUnit $packageUnit
     * @return \App\Http\Resources\Inventory\PackageUnitResource
     */
    public function show(PackageUnit $packageUnit)
    {
        return PackageUnitResource::make($packageUnit);
    }

    /**
     * Update a package unit
     *
     * @param \App\Http\Requests\Inventory\PackageUnitUpdateRequest $request
     * @param \App\Models\Inventory\PackageUnit $packageUnit
     * @return \App\Http\Resources\Inventory\PackageUnitResource
     */
    public function update(PackageUnitUpdateRequest $request, PackageUnit $packageUnit)
    {
        $packageUnit->update([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        return PackageUnitResource::make($packageUnit);
    }

    /**
     * Remove a package unit
     *
     * @param \App\Models\Inventory\PackageUnit $packageUnit
     * @return \App\Http\Resources\Inventory\PackageUnitResource
     */
    public function destroy(PackageUnit $packageUnit)
    {
        $packageUnit->delete();

        return PackageUnitResource::make($packageUnit);
    }
}
