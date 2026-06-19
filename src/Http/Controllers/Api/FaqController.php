<?php

namespace Alyani\Subsystem\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Alyani\Subsystem\Http\Requests\Api\Faq\ListRequest;
use Alyani\Subsystem\Http\Resources\CategoryWithFaqSummaryResource;
use Alyani\Subsystem\Http\Resources\FaqSummaryResource;
use Alyani\Subsystem\Models\Faq;
use Alyani\Subsystem\Models\FaqCategory;

class FaqController extends Controller
{
    /**
     * @param ListRequest $request
     * @return JsonResponse
     */
    public function list(ListRequest $request)
    {
        $data = $request->validated();
        $language = $request->language;

        $isMorphedRequest = (isset($data['relatedTo']) && isset($data['relatedID']));
        $categorySlug = $isMorphedRequest ? ucfirst($data['relatedTo']) . '_' . $data['relatedID'] : null;
        $notMorphedCategories = null;
        $category = null;

        if ($isMorphedRequest) {
            $slugCandidates = [$categorySlug, ($categorySlug . '_' . $language),];
            $category = FaqCategory::query()
                ->select(
                    'id',
                    'slug'
                )
                ->whereIn('slug', $slugCandidates)
                ->first();
            if (!$category) {
                return $this->success([
                    'faqs' => [],
                ]);
            }
            $categorySlug = $category->slug;
        } else {
            $notMorphedCategories = FaqCategory::query()
                ->select(
                    'id',
                    'morphable_type',
                    'morphable_id',
                )
                ->whereNull(['morphable_type', 'morphable_id'])
                ->pluck('id')
                ->toArray();
        }

        $cacheKey = Faq::keyCache('_' . ($isMorphedRequest ? $categorySlug : $language));
        $faqs = Cache::tags(Faq::cacheTag())->remember(
            $cacheKey,
            now()->addMinutes(60),
            function () use ($language, $category, $isMorphedRequest, $notMorphedCategories) {
                return Faq::query()
                    ->select('id', 'category_id', 'question', 'answer')
                    ->when(!$isMorphedRequest, function ($query) use ($language, $notMorphedCategories) {
                        $query->where(function ($q) use ($language, $notMorphedCategories) {
                            $q->where('language', $language)
                                ->whereIn('category_id', $notMorphedCategories)
                                ->orWhereNull('category_id');
                        });
                    })
                    ->when($isMorphedRequest, function ($query) use ($category) {
                        $query->where('category_id', $category->id);
                    })
                    ->orderBy('sort_order')
                    ->get();
            }
        );

        return $this->success([
            'faqs' => FaqSummaryResource::collection($faqs),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listByCategory(Request $request)
    {
        $language = $request->language;

        $categories = Cache::tags(Faq::cacheTag())->remember(
            Faq::keyCache('_byCategory_' . $language),
            now()->addMinutes(15),
            function () use ($language) {
                return FaqCategory::query()
                    ->select('id', 'title', 'slug')
                    ->whereNull(['morphable_type', 'morphable_id'])
                    ->where('language', $language)
                    ->withWhereHas('faq')
                    ->orderBy('sort_order')
                    ->get();
            }
        );

        return $this->success([
            'categories' => CategoryWithFaqSummaryResource::collection($categories),
        ]);
    }
}
