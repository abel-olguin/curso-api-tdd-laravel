<?php

namespace App\Models;

use App\Models\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory, HasSearch;

    protected $guarded = [];

    public function plates()
    {
        return $this->belongsToMany(Plate::class, 'menus_plates');
    }

    public function searchFields()
    {
        return ['name', 'description'];
    }
}
