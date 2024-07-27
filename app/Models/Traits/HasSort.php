<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSort
{
    public function sortFields()
    {
        return ['id'];
    }

    public function scopeSort(Builder $builder,  $sortDirection = '')
    {
        $sortBy        = request()->get('sortBy') ?: 'created_at';
        $sortDirection = $sortDirection ?: request()->get('sortDirection');
        $sortDirection = $sortDirection === 'asc' ? 'asc' : 'desc';

        if (!in_array($sortBy, $this->sortFields())) {
            abort(400, 'Invalid sortBy');
        }
        $builder->orderBy($sortBy, $sortDirection);
    }
}
