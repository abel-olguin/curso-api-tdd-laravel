<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuCollection;
use App\Http\Resources\MenuDetailResource;
use App\Jobs\GenerateQrJob;
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
    public function index(Restaurant $restaurant)
    {
        $menu = $restaurant->menus()->search()->sort()->paginate();

        return jsonResponse(new MenuCollection($menu));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuRequest $request, Restaurant $restaurant)
    {

        $menu = $restaurant->menus()->create($request->only('name', 'description'));
        $menu->plates()->sync($request->get('plate_ids'));

        GenerateQrJob::dispatch($menu);

        return jsonResponse(['menu' => MenuDetailResource::make($menu)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant, Menu $menu)
    {

        return jsonResponse(['menu' => MenuDetailResource::make($menu)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Restaurant $restaurant, Menu $menu)
    {

        $menu->update($request->only('name', 'description'));
        $menu->plates()->sync($request->get('plate_ids'));
        return jsonResponse(['menu' => MenuDetailResource::make($menu)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Menu $menu)
    {

        $menu->plates()->sync([]);
        $menu->delete();
        return jsonResponse();
    }
}
