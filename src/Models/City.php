<?php

namespace Alyani\Subsystem\Models;

use Alyani\Subsystem\Casts\AsArray;

class City extends Model
{
    protected $table = 'cities';
    protected $fillable = [
        'province_id',
        'title_localized',
        'archived',
    ];
    protected $casts = [
        'id' => 'integer',
        'province_id' => 'integer',
        'title_localized' => AsArray::class,
        'archived' => 'integer',
    ];
}
