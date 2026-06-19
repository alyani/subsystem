<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Alyani\Subsystem\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\Storage as StorageSupport;

class StorageController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function download(Request $request)
    {
        $storage = $request->attributes->get('storage');
        $isAdmin = $request->attributes->get('isAdmin');
        $isExtrenalWebService = $request->attributes->get('isExtrenalWebService');
        $isPublic = $storage->isPublic;
        $authUser = Auth::guard('sanctum')->user();

        // Check auth user if the file is not public
        if (!$this->isAuthorized($isPublic, $storage, $authUser) && !$isExtrenalWebService) {
            return $this->errorResponse(201);
        }

        // Check if the morph table type has the storageCheck method
        if (!$storage->morphable_type) {
            return $this->errorResponse(202);
        }
        if (!$isAdmin && !$isExtrenalWebService && !$this->hasStorageCheckPermission($storage, $authUser)) {
            return $this->errorResponse(203);
        }

        $pathFile = $this->getFilePath($storage, $request->type == 'thumbnail');
        if (!$pathFile) {
            return $this->errorResponse(204);
        }

        $fileName = $storage->SID . '.' . $storage->extension;
        $mimeType = StorageSupport::disk('public')->mimeType($pathFile);
        return response()->download(
            StorageSupport::disk('public')->path($pathFile),
            $fileName,
            [
                'Content-Type' => $mimeType,
                'Cache-Control' => $storage->isPublic ? 'max-age=2592000, public' : 'no-store',
            ]
        );
    }

    protected function isAuthorized($isPublic, $storage, $authUser): bool
    {
        return $isPublic ? true : (!is_null($authUser) && $storage->isUsed);
    }

    protected function hasStorageCheckPermission($storage, $authUser): bool
    {
        $storagable = $storage->morphable_type;

        // Check if the morphable type has the storageCheck method
        if (method_exists($storagable, 'storageCheck')) {
            return $storagable::storageCheck($storage, $authUser);
        }

        return true;
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

    protected function errorResponse($errorCode = 200)
    {
        return response()
            ->view('subsystem::errors.404', ['error' => "Error $errorCode"], 404);
    }
}
