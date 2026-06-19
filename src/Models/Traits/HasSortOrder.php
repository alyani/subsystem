<?php

namespace Alyani\Subsystem\Models\Traits;

trait HasSortOrder
{
    public static function getSortOrder()
    {
        $latest = static::select('sort_order')
            ->latest('sort_order')
            ->first();
        return ($latest->sort_order ?? 0) + 1;
    }
}
