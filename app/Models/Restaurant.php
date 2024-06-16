<?php

namespace App\Models;

use App\Models\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory, HasSearch;

    protected $guarded = [];

    public function plates()
    {
        return $this->hasMany(Plate::class);// restaurant_id
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);// restaurant_id
    }

    public function searchFields()
    {
        return ['name', 'description'];
    }
}
