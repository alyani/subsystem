<?php

namespace Alyani\Subsystem\Models;

class UserInfo extends Model
{
    protected $table = 'userInfos';

    protected $fillable = [
        'user_id',
        'pictureSID',
        'biography',
        'socialMedia',
        'extraInfo',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'pictureSID' => 'string',
        'biography' => 'string',
        'socialMedia' => 'string',
        'extraInfo' => 'string',
        'created' => 'integer',
        'updated' => 'integer',
    ];

    public function storage()
    {
        return $this->hasOne(Storage::class, 'SID', 'pictureSID');
    }
}
