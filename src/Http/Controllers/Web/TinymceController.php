<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\Http\Requests\Admin\Tinymce\UploadRequest;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use Alyani\Subsystem\Models\Storage;

class TinymceController extends Controller
{
    /**
     * @param UploadRequest $request
     * @return array
     */
    public function upload(UploadRequest $request): array
    {
        $data = $request->validated();

        $namespaces = [
            'App\\Models\\',
            'Alyani\\Subsystem\\Models\\',
        ];

        $modelPath = null;
        foreach ($namespaces as $namespace) {
            $candidate = $namespace . $data['modelName'];
            if (class_exists($candidate)) {
                $modelPath = $candidate;
                break;
            }
        }

        if (!$modelPath) {
            return ['location' => st('Class not found', ['class' => $data['modelName']])];
        }
        $modelInstance = new $modelPath;

        $storage = Storage::uploadFile($data, Config::get('subsystem.storage.tinymceCustomDirectory'));

        $storage->used($modelInstance, true);
        $storage->isUsed = 0;
        $storage->save();

        return ['location' => Storage::getStorageUrl($storage)];
    }

    /**
     * Compares old and new TinyMCE content to identify added/removed image SIDs.
     * @param string $oldContent HTML content before update
     * @param string $newContent HTML content after update
     * @return array{removed: array, added: array}
     */
    public static function getImageSIDs($oldContent, $newContent): array
    {
        if (!$oldContent || !$newContent) {
            throw new InvalidArgumentException('Inputs cannot be empty');
        }

        // Regex pattern for SIDs (8-4-4-4-12 hex format)
        $sidPattern = '/storage\/(?:original|thumbnail)\/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/i';

        preg_match_all($sidPattern, $oldContent, $oldSIDs);
        preg_match_all($sidPattern, $newContent, $newSIDs);

        $removedImagesSID = array_diff($oldSIDs[1], $newSIDs[1]);
        $addedImagesSID = array_diff($newSIDs[1], $oldSIDs[1]);

        return [
            'removed' => $removedImagesSID,
            'added' => $addedImagesSID
        ];
    }
}
