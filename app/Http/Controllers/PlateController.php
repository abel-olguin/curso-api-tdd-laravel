<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlateCollection;
use App\Http\Resources\PlateDetailResource;
use App\Models\Plate;
use App\Http\Requests\StorePlateRequest;
use App\Http\Requests\UpdatePlateRequest;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class PlateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Restaurant $restaurant)
    {

        Gate::authorize('viewPlates', $restaurant);
        $plates = $restaurant->plates()->search()->paginate();
        return jsonResponse(new PlateCollection($plates));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlateRequest $request, Restaurant $restaurant)
    {

        $plate = $restaurant->plates()->create($request->validated());
        return jsonResponse(['plate' => PlateDetailResource::make($plate)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant, Plate $plate)
    {

        return jsonResponse(['plate' => PlateDetailResource::make($plate)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlateRequest $request, Restaurant $restaurant, Plate $plate)
    {

        $plate->update($request->validated());
        return jsonResponse(['plate' => PlateDetailResource::make($plate->fresh())]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Plate $plate)
    {
        $plate->menus()->sync([]);
        $plate->delete();
        return jsonResponse();
    }
}
