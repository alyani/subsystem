<?php

namespace Alyani\Subsystem\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Alyani\Subsystem\Casts\Slug;
use Alyani\Subsystem\Models\Traits\HasArchive;

class FaqCategory extends Model
{
    use HasArchive;

    protected $table = 'faqsCategories';
    protected $fillable = [
        'title',
        'slug',
        'sort_order',
        'language',
        'morphable_id',
        'morphable_type',
        'archived',
        'created',
        'updated',
    ];
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'slug' => Slug::class,
        'language' => 'string',
        'sort_order' => 'integer',
        'morphable_id' => 'integer',
        'morphable_type' => 'string',
        'archived' => 'integer',
        'created' => 'integer',
        'updated' => 'integer',
    ];

    public static function cacheTag($key = ''): string
    {
        return 'faqsCategories' . $key;
    }

    public static function keyCache($key = ''): string
    {
        return 'faqCategory' . $key;
    }

    protected static function booted()
    {
        static::saved(function () {
            self::clearCache();
            Faq::clearCache();
        });
    }

    public static function clearCache(): void
    {
        Cache::tags(self::cacheTag())->flush();
    }

    public function faq()
    {
        return $this->hasMany(Faq::class, 'category_id');
    }

    public function morphable(): MorphTo
    {
        return $this->morphTo();
    }
}
