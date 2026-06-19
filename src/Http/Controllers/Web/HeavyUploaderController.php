<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Alyani\Subsystem\Http\Requests\Admin\HeavyUploader\DeleteRequest;
use Alyani\Subsystem\Http\Requests\Admin\HeavyUploader\UploadRequest;
use Alyani\Subsystem\Models\Storage;

class HeavyUploaderController extends \Alyani\Subsystem\Http\Controllers\Web\Controller
{
    /**
     * Stores file in given model storagePath and return SID for hidden input in component
     *
     * @param UploadRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function upload(UploadRequest $request)
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
            return response()->json(['error' => st('Class not found', ['class' => $data['modelName']])]);
        }

        $mime = $data['file']->getMimeType();

        // Currently only support videos
        $matched = match (true) {
            str_starts_with($mime, 'video/') => [
                'rules' => Config::get('subsystem.storage.video.validate'),
                'type' => 'video'
            ],
            default => null,
        };

        if (!$matched) {
            $error = st('Heavy uploader format error');
            return response()->json(['error' => $error]);
        }

        try {
            Validator::make(['content' => $data['file']], ['content' => $matched['rules']])->validate();
        } catch (ValidationException $e) {
            $error = $e->validator->errors()->first('content');
            return response()->json(['error' => $error]);
        }


        $storage = Storage::uploadFile(['file' => $data['file'], 'type' => $matched['type']],
            Config::get('subsystem.storage.heavyUploaderCustomDirectory'));

        $modelInstance = new $modelPath;
        $storage->used($modelInstance, true);
        $storage->isUsed = 0;
        $storage->save();

        $fileUrl = route('storage.download',['SID' => $storage->SID, 'type' => 'original']) . '.' . $storage->extension;

        return response()->json(['SID' => $storage->SID, 'url' => $fileUrl]);
    }

    /**
     * Delete file by SID
     *
     * @param DeleteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteRequest $request)
    {
        $SID = $request->validated()['SID'];

        try {
            Storage::deleteBySID($SID);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        return response()->json(['success' => true]);
    }
}
