<?php

namespace Alyani\Subsystem\Models;

use Exception;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage as StorageSupport;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Log;

class Storage extends Model
{
    protected $table = 'storage';
    protected $primaryKey = 'SID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'SID',
        'extension',
        'fileName',
        'fileSize',
        'fileType',
        'fileCategory',
        'additionalPath',
        'width',
        'height',
        'duration',
        'morphable_id',
        'morphable_type',
        'isPublic',
        'isUsed',
        'created',
        'updated',
    ];

    protected $casts = [
        'SID' => 'string',
        'isPublic' => 'boolean',
        'additionalPath' => 'string',
    ];

    const FILE_TYPE = [
        'audio',
        'image',
        'excel',
        'pdf',
    ];

    public function morphable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploaderMorphable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function keyCache($SID)
    {
        return "storage_{$SID}";
    }

    public static function findBySID($SID = null)
    {
        $SID = pathinfo($SID ?? '', PATHINFO_FILENAME);
        if (!$SID) {
            return null;
        }
        return cache()->remember(self::keyCache($SID), now()->addMinutes(10), function () use ($SID) {
            return Storage::find($SID);
        });
    }

    public static function validateBeforeUse($data, $options = [])
    {
        $options += [
            'checkOwner' => true,
        ];
        $data += [
            'uploader' => auth()->user()
        ];
        $SID = pathinfo($data['SID'] ?? '', PATHINFO_FILENAME);
        if (empty($SID)) {
            throw new Exception(st('The operation failed'), 1);
        }

        $storage = static::find($SID);
        if (!$storage) {
            throw new Exception(st('SID not found'), 2);
        }

        if ($options['checkOwner'] && !$storage->uploaderMorphable->is($data['uploader'])) {
            throw new Exception(st('SID not found'), 3);
        }

        if ($storage->isUsed) {
            throw new Exception(st('SID has been used'), 4);
        }

        $pathTemporaryUploads = Config::get('subsystem.storage.pathTemporaryUploads');
        if (!StorageSupport::disk('public')->exists($pathTemporaryUploads . $SID)) {
            throw new Exception(st('File not found'), 5);
        }

        return $storage;
    }

    public function used($model, $isPublic = false)
    {
        $pathTemporaryUploads = Config::get('subsystem.storage.pathTemporaryUploads');
        $tempFilePath = $pathTemporaryUploads . $this->SID;

        $modelFolder = strtolower(class_basename($model));
        $this->additionalPath ??= '';
        $this->additionalPath = $this->additionalPath == '/' ? '' : $this->additionalPath;
        $destinationPath = "uploads/{$modelFolder}/" . ($this->additionalPath);

        if (!StorageSupport::disk('public')->exists($destinationPath)) {
            StorageSupport::disk('public')->makeDirectory($destinationPath);
        }
        $newFilePath = $destinationPath . $this->SID;

        StorageSupport::disk('public')->move($tempFilePath, $newFilePath);

        // Thumbnail
        if (in_array($this->fileType, ['image', 'video']) && StorageSupport::disk('public')->exists($newFilePath)) {
            try {
                $configThumbnail = Config::get("subsystem.storage.{$this->fileType}.thumbnail", []);

                if (!empty($configThumbnail)) {
                    $pathThumbnail = $destinationPath . $configThumbnail['pathThumbnail'];

                    StorageSupport::disk('public')->makeDirectory($pathThumbnail);

                    if ($this->fileType == 'image') {
                        if (
                            $this->height > $configThumbnail['height'] &&
                            $this->width > $configThumbnail['width']
                        ) {
                            $image = StorageSupport::disk('public')->path($newFilePath);

                            $manager = new ImageManager(new Driver());
                            $img = $manager->read($image);
                            $img = $img->resize($configThumbnail['width'], $configThumbnail['height']);

                            StorageSupport::disk('public')->put(
                                $pathThumbnail . '/' . $this->SID,
                                (string) $img->toWebp(
                                    Config::get('subsystem.storage.image.thumbnailConversionQuality')
                                )
                            );
                        }
                    } else {
                        $video = StorageSupport::disk('public')->path($newFilePath);
                        $tempThumbnail = rtrim(sys_get_temp_dir(), '/') . '/' . $this->SID . '.' . ($configThumbnail['ext'] ?? 'jpg');

                        $ffmpeg = env('FFMPEG_PATH', '/usr/bin/ffmpeg');
                        exec(
                            $ffmpeg . ' -y -ss 00:00:01 -i ' . escapeshellarg($video) .
                            ' -frames:v 1 -update 1 ' . escapeshellarg($tempThumbnail) . ' 2>&1',
                            $output,
                            $returnCode
                        );

                        if (file_exists($tempThumbnail)) {
                            $manager = new ImageManager(new Driver());
                            $img = $manager->read($tempThumbnail);
                            // $img->resize($configThumbnail['width'], $configThumbnail['height']);
                            // $img->cover(
                            //     $configThumbnail['width'],
                            //     $configThumbnail['height']
                            // );

                            $img->scaleDown(
                                width: $configThumbnail['width'],
                                height: $configThumbnail['height']
                            );

                            StorageSupport::disk('public')->put(
                                $pathThumbnail . '/' . $this->SID,
                                (string) $img->toWebp(
                                    Config::get('subsystem.storage.video.thumbnailConversionQuality')
                                )
                            );

                            @unlink($tempThumbnail);
                        }
                    }
                }
            } catch (Throwable $e) {
                Log::error('Thumbnail generation failed', [
                    'storage_id' => $this->id,
                    'sid' => $this->SID,
                    'path' => $newFilePath,
                    'mime' => $this->mimeType,
                    'extension' => $this->extension,
                    'size' => $this->fileSize,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $this->morphable()->associate($model);
        $this->isUsed = true;
        $this->isPublic = $isPublic;
        $this->save();
        cache()->forget(self::keyCache($this->SID));

        return true;
    }

    public static function deleteBySID($SID)
    {
        if (empty($SID)) {
            return;
        }

        $storage = static::findBySID($SID);
        if (!$storage) {
            return;
        }
        $storage->delete();

        $filePath = static::getStoragePath($storage);
        StorageSupport::disk('public')->delete($filePath);
    }

    public static function deleteBySIDs($SIDs)
    {
        if (empty($SIDs)) {
            return;
        }

        if (!is_array($SIDs)) {
            $SIDs = (array)$SIDs;
        }

        $storages = static::whereIn('SID', $SIDs)->get();
        if (!$storages) {
            return;
        }
        foreach ($storages as $storage) {
            $storage->delete();

            $filePath = static::getStoragePath($storage);
            StorageSupport::disk('public')->delete($filePath);
        }
    }

    /**
     * Get info for each SID , such as fileType, extension, fileName & fileSize
     *
     * @param      $SIDs
     * @return array
     */
    public static function getStorageInfoBySIDs($SIDs): array
    {
        $storageInfo = [];
        if (!is_array($SIDs)) {
            $SIDs = (array)$SIDs;
        }
        $SIDs = array_filter($SIDs);

        if (empty($SIDs)) {
            return $storageInfo;
        }

        $storages = static::whereIn('SID', $SIDs)->get();
        foreach ($storages as $storage) {
            $storageInfo[$storage['SID']] = [
                'SID' => $storage['SID'],
                'fileType' => in_array($storage->fileType, ['image', 'video', 'audio']) ? $storage->fileType : 'file',
                'extension' => $storage->extension,
                'fileName' => $storage->fileName,
                'fileSize' => $storage->fileSize,
            ];
        }
        return $storageInfo;
    }

    public static function makeWebp($image, $SID, $pathFile)
    {
        $manager = new ImageManager(new Driver());
        $img = $manager->read($image);
        $imgToWebp = $img->toWebp(Config::get('subsystem.storage.image.originalConversionQuality'));

        $pathFile = $pathFile . '/' . $SID;
        StorageSupport::disk('public')->put($pathFile, $imgToWebp);
        return $pathFile;
    }

    public static function uploadFile($data, $additionalPath = null)
    {
        $SID = uuid_create();
        $width = 0;
        $height = 0;
        $duration = 0;
        $isImage = $data['type'] == 'image';

        if ($isImage) {
            [$width, $height] = getimagesize($data['file']);
        }

        if ($data['type'] == 'audio') {
            $time = exec(
                "ffmpeg -i " . escapeshellarg($data['file']) . " 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//",
            );
            if ($time > 0) {
                [$hms, $milli] = explode('.', $time);
                [$hours, $minutes, $seconds] = explode(':', $hms);
                $duration = ($hours * 3600) + ($minutes * 60) + $seconds;
            }
        }
        // Extract extension
        $extension = $data['file']->getClientOriginalExtension();

        // Extract original file name
        $originalFileName = $data['file']->getClientOriginalName();

        // Extract file name
        $fileName = pathinfo($originalFileName, PATHINFO_FILENAME);

        // Remove HTML tags and special characters, then extract file name
        preg_match_all(
            "/^(?!.*[@#!%$&*])[A-Za-z\s\x{0600}-\x{06FF}0-9()*_\.\-]+$/u",
            strip_tags(trim($fileName)),
            $matches,
        );
        $fileName = isset($matches[0]) ? implode($matches[0]) : '';

        // Limit file name length to 64 characters
        $fileName = mb_substr($fileName, 0, 255) ?: Str::random(10);

        $pathTemporaryUploads = Config::get('subsystem.storage.pathTemporaryUploads');
        if ($isImage && Config::get('subsystem.storage.image.convertToWebp', false)) {
            Storage::makeWebp($data['file'], $SID, $pathTemporaryUploads);
            $extension = 'webp';
        } else {
            $data['file']->storeAs($pathTemporaryUploads, $SID, 'public');
        }
        $storage = new Storage([
            'SID' => $SID,
            'extension' => $extension,
            'fileName' => $fileName,
            'fileSize' => filesize($data['file']),
            'fileType' => $data['type'],
            'width' => $width,
            'height' => $height,
            'duration' => $duration,
            'additionalPath' => trim($additionalPath, '/') . '/',
        ]);
        $storage->uploaderMorphable()->associate(auth()->user());
        $storage->save();

        return $storage;
    }

    /**
     * Get the file path after public disk
     *
     * @param Storage $storage
     * @return string[]
     */
    public static function getStoragePath(Storage $storage): array
    {
        $modelFolder = strtolower(class_basename($storage->morphable_type));
        $basePath = config('subsystem.storage.path') . $modelFolder . '/' . $storage->additionalPath ?? '';
        $fileName = $storage->SID;

        $path = [$basePath . $fileName];

        // Add thumbnail path to path array
        $configThumbnail = Config::get("subsystem.storage.{$storage->fileType}.thumbnail", []);
        if (!empty($configThumbnail)) {
            $path[] = $basePath . $configThumbnail['pathThumbnail'] . $fileName;
        }

        return $path;
    }

    public static function getStorageUrl(Storage $storage, $isThumbnail = false)
    {
        $appUrl = rtrim(Config::get('app.url'), '/') . '/';
        $fileType = $isThumbnail ? 'thumbnail' : 'original';

        return $appUrl . "storage/{$fileType}/" . $storage->SID . '.' . $storage->extension;
    }

    public function generateThumbnail(): bool
    {
        if (!in_array($this->fileType, ['image', 'video'])) {
            return false;
        }

        $disk = StorageSupport::disk('public');
        $newFilePath = static::getStoragePath($this)[0] ?? '';

        if (!$disk->exists($newFilePath)) {
            return false;
        }

        try {
            $configThumbnail = Config::get(
                "subsystem.storage.{$this->fileType}.thumbnail",
                []
            );

            if (empty($configThumbnail)) {
                return false;
            }

            $destinationPath = dirname($newFilePath) . '/';
            $pathThumbnail = $destinationPath . $configThumbnail['pathThumbnail'];
            $disk->makeDirectory($pathThumbnail);
            $thumbnailPath = rtrim($pathThumbnail, '/') . '/' . $this->SID;
            if ($disk->exists($thumbnailPath)) {
                return true;
            }

            if ($this->fileType == 'image') {
                $image = $disk->path($newFilePath);

                $manager = new ImageManager(new Driver());
                $img = $manager->read($image);

                $img->scaleDown(
                    width: $configThumbnail['width'],
                    height: $configThumbnail['height']
                );

                $disk->put(
                    $thumbnailPath,
                    (string) $img->toWebp(
                        Config::get(
                            'subsystem.storage.image.thumbnailConversionQuality',
                            80
                        )
                    )
                );

                return true;
            }

            // Video
            $video = $disk->path($newFilePath);
            $tempThumbnail = rtrim(sys_get_temp_dir(), '/')
                . '/' . $this->SID
                . '.' . ($configThumbnail['ext'] ?? 'jpg');

            $ffmpeg = env('FFMPEG_PATH', '/usr/bin/ffmpeg');
            exec(
                escapeshellarg($ffmpeg)
                . ' -y -ss 00:00:01 -i ' . escapeshellarg($video)
                . ' -frames:v 1 -update 1 '
                . escapeshellarg($tempThumbnail)
                . ' 2>&1',
                $output,
                $returnCode
            );

            if (
                $returnCode !== 0 ||
                !file_exists($tempThumbnail)
            ) {
                Log::error('Video thumbnail generation failed', [
                    'storage_id' => $this->id,
                    'sid' => $this->SID,
                    'path' => $newFilePath,
                    'output' => $output ?? [],
                    'return_code' => $returnCode,
                ]);

                return false;
            }

            try {
                $manager = new ImageManager(new Driver());
                $img = $manager->read($tempThumbnail);

                $img->scaleDown(
                    width: $configThumbnail['width'],
                    height: $configThumbnail['height']
                );
                $disk->put(
                    $thumbnailPath,
                    (string) $img->toWebp(
                        Config::get(
                            'subsystem.storage.video.thumbnailConversionQuality',
                            80
                        )
                    )
                );
            } finally {
                @unlink($tempThumbnail);
            }

            return true;
        } catch (Throwable $e) {
            Log::error('Thumbnail generation failed', [
                'storage_id' => $this->id,
                'sid' => $this->SID,
                'path' => $newFilePath,
                'mime' => $this->mimeType,
                'extension' => $this->extension,
                'size' => $this->fileSize,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
