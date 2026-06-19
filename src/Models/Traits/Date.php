<?php

namespace Alyani\Subsystem\Models\Traits;

trait Date
{
    /**
     * To jalali date
     */
    public function toJalaliDate($field, $format = 'H:i Y/n/j'): string
    {
        $timestamp = $this->$field;
        return !empty($timestamp) ? verta($timestamp)->format($format) : '';
    }
}
