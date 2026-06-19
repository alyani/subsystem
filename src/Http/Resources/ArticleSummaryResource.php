<?php

namespace Alyani\Subsystem\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleSummaryResource extends JsonResource
{
    /**
     * @link https://docs.google.com/document/d/12RCK8w8aJ4Z0-A2rykzcb7Ogpx2RHIvx-wr4sES73uE/edit?tab=t.0
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
                'avatarSID' => $this->manager->avatarSID,
            ],
            'title' => $this->title,
            'slug' => $this->slug,
            'reading_time' => $this->reading_time,
            'posterSID' => !empty($this->posterSID) ? StorageResource::make($this->storage) : null,
            'categories' => ArticleCategorySummaryResource::collection($this->categories),
            'created_at' => $this->created_at,
        ];
    }
}
