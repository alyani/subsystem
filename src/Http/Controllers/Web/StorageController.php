<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\Http\Controllers\Api\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage as StorageSupport;

class StorageController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function download(Request $request)
    {
        $storage = $request->attributes->get('storage');
        $type = $request->route('type');
        $pathFile = $this->getFilePath($storage, $type === 'thumbnail');

        if (!$pathFile) {
            abort(404, 'Error 201');
        }

        if ($storage->isPublic) {
            $cacheHeaders = [
                'Cache-Control'     => 'max-age=2592000, public, immutable',
                'CDN-Cache-Control' => 'max-age=2592000',
            ];
        } else {
            $cacheHeaders = [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
                'Pragma'        => 'no-cache',
                'Expires'       => '0',
            ];
        }

        $corsHeaders = [
            'Access-Control-Allow-Origin'  => '*', // در تولید (Production) بهتر است آدرس فرانت باشد
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Range, Authorization, Content-Type',
            'Access-Control-Expose-Headers' => 'Content-Range, Content-Length, Accept-Ranges',
        ];

        return StorageSupport::disk('public')->response(
            $pathFile,
            $storage->SID . '.' . $storage->extension,
            array_merge($cacheHeaders, $corsHeaders)
        );
    }

    protected function fileExists($pathFile)
    {
        return StorageSupport::disk('public')->exists($pathFile);
    }

    protected function getFilePath($storage, $isThumbnail = false)
    {
        $modelFolder = Config::get('subsystem.storage.path') . strtolower(class_basename($storage->morphable_type));
        $baseFilePath = "{$modelFolder}/" . ($storage->additionalPath ?? '') . $storage->SID;

        // Check for file exists
        if (!$this->fileExists($baseFilePath)) {
            return false;
        }

        $thumbnailDir = Config::get('subsystem.storage.image.thumbnail.pathThumbnail', false);
        if (!$isThumbnail || empty($thumbnailDir)) {
            return $baseFilePath;
        }

        $thumbnailPath = "{$modelFolder}/" . ($storage->additionalPath ?? '') . "{$thumbnailDir}/{$storage->SID}";
        return $this->fileExists($thumbnailPath) ? $thumbnailPath : $baseFilePath;
    }
}
