<?php

namespace App\Http\Controllers;

use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use Illuminate\Support\Facades\Gate;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurants = auth()->user()->restaurants()->get();
        return jsonResponse([
            'restaurants' => RestaurantResource::collection($restaurants),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRestaurantRequest $request)
    {
        $restaurant = auth()->user()->restaurants()->create($request->validated());
        return jsonResponse(data: [
            'restaurant' => RestaurantResource::make($restaurant),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
    {
        Gate::authorize('update', $restaurant);
        $restaurant->update($request->validated());
        return jsonResponse(data: [
            'restaurant' => RestaurantResource::make($restaurant),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        Gate::authorize('delete', $restaurant);
        $restaurant->delete();
        return jsonResponse();
    }
}
