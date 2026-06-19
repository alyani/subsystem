<?php

namespace Alyani\Subsystem\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Hekmatinasser\Verta\Verta;

class PersianTimestamp implements CastsAttributes
{
    /**
     * Cast DB value (int timestamp) to Persian formatted string.
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (empty($value)) {
            return null;
        }

        // Value is stored as UNIX timestamp
        return (new Verta($value))->format('Y/m/d H:i:s');
    }

    /**
     * Cast Persian date string (or timestamp) to DB integer.
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (empty($value)) {
            return null;
        }

        // If already integer, assume it's a timestamp
        if (is_numeric($value)) {
            return (int) $value;
        }

        // Parse Persian date string into Verta -> timestamp
        $verta = Verta::parse($value);
        return $verta->timestamp; // save as int
    }
}
