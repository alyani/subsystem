<?php

namespace Alyani\Subsystem\Models;

use Alyani\Subsystem\Casts\Slug;
use Alyani\Subsystem\Models\Traits\Pagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Article extends Model
{
    use HasFactory;
    use Pagination;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'introduction',
        'content',
        'posterSID',
        'reading_time',
        'language',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'manager_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'slug' => Slug::class,
        'introduction' => 'string',
        'content' => 'string',
        'posterSID' => 'string',
        'reading_time' => 'integer',
        'language' => 'string',
        'meta_title' => 'string',
        'meta_description' => 'string',
        'meta_keyword' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'manager_id' => 'integer',
    ];

    protected static function booted()
    {
        static::saved(fn () => self::clearCache());
    }

    protected static function clearCache(): void
    {
        Cache::tags(self::cacheTag())->flush();
    }

    public static function cacheTag($key = '')
    {
        return 'articles' . $key;
    }

    public static function keyCache($key = '')
    {
        return 'articles' . $key;
    }

    public function categories()
    {
        return $this->belongsToMany(ArticleCategory::class, 'article_category');
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class, 'manager_id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'posterSID', 'SID');
    }

    // public function comments(): morphMany
    // {
    //     return $this->morphMany(Comment::class, 'commentable');
    // }
}
