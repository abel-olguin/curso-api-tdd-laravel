<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuCollection;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuRequest $request, Restaurant $restaurant)
    {
        Gate::authorize('view', $restaurant);
        $menu = $restaurant->menus()->create($request->only('name', 'description'));
        $menu->plates()->sync($request->get('plate_ids'));
        return jsonResponse(['menu' => MenuResource::make($menu)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant, Menu $menu)
    {
        Gate::authorize('view', $restaurant);
        return jsonResponse(['menu' => MenuResource::make($menu)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Restaurant $restaurant, Menu $menu)
    {
        Gate::authorize('view', $restaurant);
        $menu->update($request->only('name', 'description'));
        $menu->plates()->sync($request->get('plate_ids'));
        return jsonResponse(['menu' => MenuResource::make($menu)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Menu $menu)
    {
        Gate::authorize('view', $restaurant);
        $menu->plates()->sync([]);
        $menu->delete();
        return jsonResponse();
    }
}
