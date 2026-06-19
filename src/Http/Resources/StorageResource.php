<?php

namespace Alyani\Subsystem\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StorageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'SID' => $this->SID,
            'fullSID' => $this->SID . '.' . $this->extension,
            'extension' => $this->extension,
            'file_name' => $this->fileName,
            'file_size' => $this->fileSize,
            'file_type' => $this->fileType,
            'width' => $this->width,
            'height' => $this->height,
            'duration' => $this->duration,
        ];
    }
}
