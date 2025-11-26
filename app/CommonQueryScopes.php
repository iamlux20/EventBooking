<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;

trait CommonQueryScopes
{
    public function scopeFilterByDate(Builder $query, ?string $startDate = null, ?string $endDate = null): Builder
    {
        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        return $query;
    }

    public function scopeSearchByTitle(Builder $query, ?string $search = null): Builder
    {
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        return $query;
    }
}
