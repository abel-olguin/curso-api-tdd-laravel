<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
                'self' => route('restaurants.menus.show', [$this->restaurant_id, $this->id]),
                'public' => route('public.menu.show', $this->id),
                'qr'     => $this->qr,
            ],
        ];
    }
}
