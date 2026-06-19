<?php

namespace Alyani\Subsystem\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleCategorySummaryResource extends JsonResource
{
    /**
     * @link https://docs.google.com/document/d/1JvF1mWzQSB6gfjQEF3KigcVxbjOOUVyUrcLmc3C68CU/edit?tab=t.0
     *
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'article_category_id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug
        ];
    }
}
