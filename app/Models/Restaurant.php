<?php

namespace App\Models;

use App\Models\Traits\HasSearch;
use App\Models\Traits\HasSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use HasFactory, HasSearch, HasSort, SoftDeletes;

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

    public function sortFields()
    {
        return ['id', 'name', 'description', 'created_at'];
    }
}
