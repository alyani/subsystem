<?php

namespace Alyani\Subsystem\Http\Controllers\Api;

use Alyani\Subsystem\Enums\ActivationStatus;
use Alyani\Subsystem\Http\Requests\Api\Article\CategoryRequest;
use Alyani\Subsystem\Http\Requests\Api\Article\GetRequest;
use Alyani\Subsystem\Http\Requests\Api\Article\ListRequest;
use Alyani\Subsystem\Http\Requests\Api\Article\RelatedRequest;
use Alyani\Subsystem\Http\Resources\ArticleCategoryResource;
use Alyani\Subsystem\Http\Resources\ArticleResource;
use Alyani\Subsystem\Http\Resources\ArticleSummaryResource;
use Alyani\Subsystem\Models\Article;
use Alyani\Subsystem\Models\ArticleCategory;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    /**
     * @param CategoryRequest $request
     * @link https://docs.google.com/document/d/1rUUow6VW0XOBif-H3TsIgW3HA4MISlXDi9B_LZKufF0/edit?tab=t.0
     */
    public function category(CategoryRequest $request)
    {
        $data = $request->validated();
        $language = $request->language;
        $slug = $data['slug'] ?? null;

        $articleCategory = Cache::tags(ArticleCategory::cacheTag())
            ->remember(
                ArticleCategory::keyCache($language . $slug),
                now()->addMinutes(60),
                function () use ($slug, $language) {
                    return ArticleCategory::query()
                        ->with('storage')
                        ->orderBy('sort_order')
                        ->where('status', ActivationStatus::Active)
                        ->where('language', $language)
                        ->when($slug, function ($query, $slug) {
                            $query->where('slug', $slug);
                        })
                        ->get();
                }
            );

        if ($slug && $articleCategory->isEmpty()) {
            return $this->error(1, st('Article category not found'));
        }

        return $this->success([
            'categories' => ArticleCategoryResource::collection($articleCategory),
        ]);
    }

    /**
     * @param GetRequest $request
     * @link
     */
    public function get(GetRequest $request)
    {
        $data = $request->validated();
        $language = $request->language;
        $slug = $data['slug'];

        $cacheKey = Article::keyCache('_' . $slug);
        $article = Cache::tags(Article::cacheTag())
            ->remember(
                $cacheKey,
                now()->addMinutes(15),
                function () use ($slug, $language) {
                    return Article::query()
                        ->with(['storage', 'categories:id,title,slug', 'manager:id,name,family,avatarSID', 'manager.storage'])
                        ->where('slug', $slug)
                        ->where('language', $language)
                        ->first();
                }
            );

        if (empty($article)) {
            return $this->error(1, st('record not found'));
        }

        return $this->success([
            'article' => ArticleResource::make($article),
        ]);
    }

    /**
     * @param ListRequest $request
     * @link
     */
    public function list(ListRequest $request)
    {
        $data = $request->validated();
        $language = $request->language;

        if (!empty($data['article_category_id']) || !empty($data['article_category_slug'])) {
            $articleCategory = ArticleCategory::query()
                ->when(!empty($data['article_category_id']), function ($query) use ($data) {
                    return $query->where('id', $data['article_category_id']);
                })
                ->when(!empty($data['article_category_slug']), function ($query) use ($data) {
                    return $query->where('slug', $data['article_category_slug']);
                })
                ->where('status', ActivationStatus::Active)
                ->first();
            if (!$articleCategory) {
                return $this->error(1, st('The selected category is invalid'));
            }
            $data['article_category_id'] = $articleCategory->id;
        }

        $cacheKey = Article::keyCache('_list_' . $language . '_' . md5(json_encode(array_filter($data))));
        $articles = Cache::tags(Article::cacheTag())
            ->remember(
                $cacheKey,
                now()->addMinutes(5),
                function () use ($data, $language) {
                    return Article::query()
                        ->select([
                            'id',
                            'manager_id', 
                            'title',
                            'slug',
                            'reading_time',
                            'posterSID',
                            'created_at'
                        ])
                        ->with(['storage', 'categories:id,title,slug', 'manager:id,name,family,avatarSID', 'manager.storage'])
                        ->when(!empty($data['title']), function ($query) use ($data) {
                            return $query->where('title', 'like', '%' . $data['title'] . '%');
                        })
                        ->when(!empty($data['article_category_id']), function ($query) use ($data) {
                            $query->whereHas('categories', function ($query) use ($data) {
                                $query->where('article_category_id', $data['article_category_id']);
                            });
                        })
                        ->where('language', $language)
                        ->orderBy('created_at', 'desc')
                        ->pageLimit($data['page'] ?? null, $data['items_per_page'] ?? null);
                }
            );

        return $this->success([
            'articles' => ArticleSummaryResource::collection($articles),
            'totalRecords' => $articles->totalRecords,
            'hasNextPage' => $articles->hasNextPage,
        ]);
    }

    public function related(RelatedRequest $request)
    {
        $data = $request->validated();
        $slug = $data['slug'];
        $language = $request->language;

        $cacheKey = Article::keyCache('_' . $slug);
        $article = Cache::tags(Article::cacheTag())->remember(
            $cacheKey,
            now()->addMinutes(15),
            function () use ($slug, $language) {
                return Article::query()
                    ->with(['storage', 'categories:ID,title,slug', 'manager:id,name,family,avatarSID'])
                    ->where('slug', $slug)
                    ->where('language', $language)
                    ->first();
            }
        );

        if (empty($article)) {
            return $this->error(1, st('record not found'));
        }

        $categoriesID = $article->categories
            ->pluck('id')
            ->toArray();

        $article_ids = ArticleCategoryPivot::query()
            ->whereIn('category_id', $categoriesID)
            ->where('article_id', '!=', $article->id)
            ->orderByDesc('id')
            ->pageLimit($data['page'] ?? null, $data['itemsPerPage'] ?? 5);

        $hasNextPage = $article_ids->hasNextPage;
        $article_ids = $article_ids->pluck('article_id')
            ->toArray();

        $cacheKey = Article::keyCache('_relatedArticles_' . md5(json_encode($article_ids)));
        $articles = Cache::tags(Article::cacheTag())->remember(
            $cacheKey,
            now()->addMinutes(10),
            function () use ($article_ids, $language) {
                return Article::query()
                    ->whereIn('id', $article_ids)
                    ->where('language', $language)
                    ->with('storage', 'categories:ID,title,slug', 'manager:id,name,family,avatarSID')
                    ->get();
            }
        );

        return $this->success([
            'articles' => ArticleSummaryResource::collection($articles),
            'hasNextPage' => $hasNextPage,
        ]);
    }
}
