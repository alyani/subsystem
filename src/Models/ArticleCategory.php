<?php

namespace Alyani\Subsystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Alyani\Subsystem\Casts\Slug;
use Alyani\Subsystem\Enums\ActivationStatus;
use Alyani\Subsystem\Models\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasSortOrder;

    protected $fillable = [
        'title',
        'slug',
        'photoSID',
        'description',
        'sort_order',
        'language',
        'status',
        'meta_title',
        'meta_keyword',
        'meta_description',
    ];

    protected $casts = [
        'title' => 'string',
        'slug' => Slug::class,
        'photoSID' => 'string',
        'description' => 'string',
        'sort_order' => 'integer',
        'language' => 'string',
        'status' => ActivationStatus::class,
        'meta_title' => 'string',
        'meta_keyword' => 'string',
        'meta_description' => 'string',
    ];

    protected static function booted()
    {
        static::saved(fn() => self::clearCache());
    }

    protected static function clearCache(): void
    {
        Cache::tags(self::cacheTag())->flush();
    }

    public static function cacheTag($key = ''): string
    {
        return 'articlesCategories' . $key;
    }

    public static function keyCache($key = ''): string
    {
        return 'articlesCategories' . $key;
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_category');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'photoSID', 'SID');
    }

    public static function getForItemPicker()
    {
        return static::select('id', 'title')
            ->orderBy('sort_order', 'asc')
            ->pluck('title', 'id')
            ->toArray();
    }
}
