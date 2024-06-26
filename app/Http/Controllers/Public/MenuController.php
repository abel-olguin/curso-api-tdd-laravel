<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuPublicResource;
use App\Models\Menu;

class MenuController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        return jsonResponse(['menu' => MenuPublicResource::make($menu)]);
    }
}
