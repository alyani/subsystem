<?php

namespace Alyani\Subsystem\Models;

use Alyani\Subsystem\Casts\AsArray;

class Country extends Model
{
    protected $table = 'countries';
    protected $fillable = [
        'title_localized',
        'archived',
    ];
    protected $casts = [
        'id' => 'integer',
        'title_localized' => AsArray::class,
        'archived' => 'integer',
    ];
}
