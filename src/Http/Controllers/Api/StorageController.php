<?php

namespace Alyani\Subsystem\Http\Controllers\Api;

use Alyani\Subsystem\Http\Requests\Api\Storage\UploadRequest;
use Alyani\Subsystem\Http\Resources\StorageResource;
use Alyani\Subsystem\Models\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class StorageController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function upload(UploadRequest $request)
    {
        $data = $request->validated();

        $storage = Storage::uploadFile($data);

        return $this->success([
            'storage' => StorageResource::make($storage),
        ]);
    }
}
