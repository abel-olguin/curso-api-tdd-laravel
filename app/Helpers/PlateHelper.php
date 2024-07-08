<?php

namespace App\Helpers;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Storage;

class PlateHelper
{
    public function uploadImage(string $dataBase64, $restaurantId)
    {
        list($imageData, $extension) = Base64Helper::getDataImage($dataBase64);
        $path = "restaurants/{$restaurantId}/plates/".uniqid('plate_').'.'.$extension;
        Storage::disk('public')->put($path, $imageData);
        return $path;
    }
}
