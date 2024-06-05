<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlateCollection;
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
        $plates = $restaurant->plates()->paginate();
        return jsonResponse(new PlateCollection($plates));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlateRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Plate $plate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlateRequest $request, Plate $plate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plate $plate)
    {
        //
    }
}
