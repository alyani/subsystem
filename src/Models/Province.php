<?php

namespace Alyani\Subsystem\Models;

use Alyani\Subsystem\Casts\AsArray;

class Province extends Model
{
    protected $fillable = [
        'country_id',
        'title_localized',
        'archived',
    ];
    protected $casts = [
        'id' => 'integer',
        'country_id' => 'integer',
        'title_localized' => AsArray::class,
        'archived' => 'integer',
    ];
}
