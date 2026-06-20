<?php

namespace Alyani\Subsystem\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * @link https://docs.google.com/document/d/1JfFO3TldXu1CaeuO-QS6UQls4hH2CriE8Wy5fT1CIlU/edit?tab=t.0
     *
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'article_id' => $this->id,
            'author' => [
                'name' => $this->manager->name,
                'family' => $this->manager->family,
                'avatarSID' => StorageResource::make($this->manager->storage),
            ],
            'title' => $this->title,
            'slug' => $this->slug,
            'introduction' => $this->introduction,
            'content' => $this->content,
            'reading_time' => $this->reading_time,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keyword' => $this->meta_keyword,
            'created_at' => $this->created_at->timestamp,
            'posterSID' => StorageResource::make($this->storage),
            'categories' => ArticleCategorySummaryResource::collection($this->categories),
        ];
    }
}
