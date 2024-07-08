<?php

namespace App\Http\Controllers;

use App\Helpers\Base64Helper;
use App\Helpers\PlateHelper;
use App\Http\Resources\PlateCollection;
use App\Http\Resources\PlateDetailResource;
use App\Models\Plate;
use App\Http\Requests\StorePlateRequest;
use App\Http\Requests\UpdatePlateRequest;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PlateController extends Controller
{

    public function __construct(public PlateHelper $helper)
    {

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Restaurant $restaurant)
    {

        Gate::authorize('viewPlates', $restaurant);
        $plates = $restaurant->plates()->search()->sort()->paginate();
        return jsonResponse(new PlateCollection($plates));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlateRequest $request, Restaurant $restaurant)
    {
        $data = $request->safe()->except('image');

        $data['image'] = $this->helper->uploadImage($request->get('image'), $restaurant->id);

        $plate = $restaurant->plates()->create($data);
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
        $data = $request->safe()->except('image');

        if($request->get('image')){
            $data['image'] = $this->helper->uploadImage($request->get('image'), $restaurant->id);
        }

        $plate->update($data);
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
