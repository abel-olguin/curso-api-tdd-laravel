<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'name'          => $this->name,
            'description'   => $this->description,
            'links'         => [
                'parent' => route('restaurants.show', $this->restaurant_id),
                'public' => route('public.menu.show', $this->id),
                'qr'     => $this->qr,
            ],
            'plates'        => MenuPlateResource::collection($this->plates)
        ];
    }
}
