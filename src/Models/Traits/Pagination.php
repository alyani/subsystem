<?php

namespace Alyani\Subsystem\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait Pagination
{
    public function scopePageLimit(Builder $builder, $page = null, $itemsPerPage = null): Collection
    {
        $page = $page ?: 1;
        $itemsPerPage = $itemsPerPage ?: 25;

        $totalRecords = $builder->count();

        $results = $builder->skip((($page ?: 1) - 1) * $itemsPerPage)
            ->take($itemsPerPage)
            ->get();

        $hasNextPage = $totalRecords > ($page * $itemsPerPage);

        $results->totalRecords = $totalRecords;
        $results->hasNextPage = $hasNextPage;

        return $results;
    }
}
