<?php

namespace Alyani\Subsystem\Models;

use Illuminate\Support\Facades\Cache;

class Faq extends Model
{
    protected $table = 'faqs';
    protected $fillable = [
        'category_id',
        'question',
        'answer',
        'language',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'sort_order',
        'created',
        'updated',
    ];
    protected $casts = [
        'category_id' => 'integer',
        'question' => 'string',
        'answer' => 'string',
        'language' => 'string',
        'meta_title' => 'string',
        'meta_description' => 'string',
        'meta_keyword' => 'string',
        'sort_order' => 'integer',
        'created' => 'integer',
        'updated' => 'integer',
    ];

    public static function cacheTag($key = ''): string
    {
        return 'faqs' . $key;
    }

    public static function keyCache($key = ''): string
    {
        return 'faq' . $key;
    }

    protected static function booted()
    {
        static::saved(function () {
            self::clearCache();
            FaqCategory::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
            FaqCategory::clearCache();
        });
    }

    public static function clearCache(): void
    {
        Cache::tags(self::cacheTag())->flush();
    }

    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }
}
