<?php

namespace App\Models;

use App\Models\Traits\HasSearch;
use App\Models\Traits\HasSort;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    use HasFactory, HasSearch, HasSort;

    protected $guarded = [];

    public function plates()
    {
        return $this->belongsToMany(Plate::class, 'menus_plates');
    }

    public function searchFields()
    {
        return ['name', 'description'];
    }

    public function sortFields()
    {
        return ['id', 'name', 'description', 'created_at'];
    }

    public function qr(): Attribute
    {
        return new Attribute(get: fn($attr) => $attr ? Storage::disk('public')->url($attr) : null);
    }
}
