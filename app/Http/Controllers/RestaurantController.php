<?php

namespace App\Http\Controllers;

use App\Http\Resources\RestaurantCollection;
use App\Http\Resources\RestaurantDetailResource;
use App\Models\Restaurant;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Info(title="API Menus", version="1.0")
 *
 * @OA\Server(url="http://localhost:8000")
 *
 */
class RestaurantController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/restaurants",
     *     summary="Mostrar restaurantes",
     *     description="Listado de restaurantes",
     *     tags={"Menus"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar todos los restaurantes."
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     * )
     */
    public function index()
    {
        $restaurants = auth()->user()->restaurants()->search()->sort()->paginate();
        return jsonResponse(new RestaurantCollection($restaurants));
    }

    /**
     * @OA\POST(
     *     path="/api/v1/restaurants",
     *     summary="Crear restaurante",
     *     description="Crear un restaurante",
     *     tags={"Restaurants"},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Request Body Description",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                   type="object",
     *             )
     *          )
     *      ),
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Crea un restaurante."
     *     ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400, description="Bad request"),
     * )
     */
    public function store(StoreRestaurantRequest $request)
    {
        $restaurant = auth()->user()->restaurants()->create($request->validated());
        return jsonResponse(data: [
            'restaurant' => RestaurantDetailResource::make($restaurant),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/restaurants/{id}",
     *     summary="Mostrar restaurante",
     *     description="Detalle de un restaurante",
     *     tags={"Restaurants"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id del restaurant",
     *          required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar detalle de un restaurante."
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     * )
     */
    public function show(Restaurant $restaurant)
    {
        Gate::authorize('view', $restaurant);
        return jsonResponse([
            'restaurant' => RestaurantDetailResource::make($restaurant)
        ]);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/restaurants/{id}",
     *     summary="Crear restaurante",
     *     description="Crear un restaurante",
     *     tags={"Restaurants"},
     *     @OA\Parameter(
     *           name="id",
     *           in="path",
     *           description="Id del restaurant",
     *           required=true,
     *      ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Request Body Description",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                   type="object",
     *             )
     *          )
     *      ),
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Crea un restaurante."
     *     ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400, description="Bad request"),
     * )
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
    {
        Gate::authorize('update', $restaurant);
        $restaurant->update($request->validated());
        return jsonResponse(data: [
            'restaurant' => RestaurantDetailResource::make($restaurant),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/restaurants/{id}",
     *     summary="Eliminar restaurante",
     *     description="Eliminar un restaurante",
     *     tags={"Restaurants"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter(
     *           name="id",
     *           in="path",
     *           description="Id del restaurant",
     *           required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Eliminar un restaurante."
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     * )
     */
    public function destroy(Restaurant $restaurant)
    {
        Gate::authorize('delete', $restaurant);
        $restaurant->delete();
        return jsonResponse();
    }
}
