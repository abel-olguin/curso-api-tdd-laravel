<?php

namespace App\Models;

use App\Models\Traits\HasSearch;
use App\Models\Traits\HasSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plate extends Model
{
    use HasFactory, HasSearch, HasSort;

    protected $guarded = [];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menus_plates');
    }

    public function searchFields()
    {
        return ['name', 'description'];
    }

    public function sortFields()
    {
        return ['id', 'name', 'description', 'created_at'];
    }
}
