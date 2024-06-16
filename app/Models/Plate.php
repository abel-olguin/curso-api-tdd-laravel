<?php

namespace App\Models;

use App\Models\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plate extends Model
{
    use HasFactory, HasSearch;

    protected $guarded = [];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menus_plates');
    }

    public function searchFields()
    {
        return ['name', 'description'];
    }
}
