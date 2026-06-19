<?php

namespace Alyani\Subsystem\Database\Seeders;

use Alyani\Subsystem\Models\Storage as SubsystemStorage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AvatarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $avatarPath = resource_path('images/avatars');
        if (is_dir($avatarPath)) {
            $files = File::files($avatarPath);

            $dest = Config::get('subsystem.storage.path') . 'user';

            if (!Storage::disk('public')->exists($dest)) {
                Storage::disk('public')->makeDirectory($dest);
            }
            $defaultAvatarSIDFormat = 'dA000000-0000-0000-0000-'; //000000000000;
            foreach ($files as $file) {
                $avatarName = str_replace('.' . $file->getExtension(), '', $file->getBasename());
                $avatarSID = $defaultAvatarSIDFormat . str_pad($avatarName, 12, '0', STR_PAD_LEFT);

                Storage::disk('public')->putFileAs(
                    $dest,
                    new \Illuminate\Http\File($file->getPathname()),
                    $avatarSID,
                );

                [$width, $height] = getimagesize($file);


                SubsystemStorage::updateOrCreate([
                    'SID' => $avatarSID,
                ], [
                    'morphable_id' => null,
                    'morphable_type' => User::class,
                    'user_id' => 0,
                    'extension' => $file->getExtension(),
                    'fileSize' => filesize($file->getPathname()),
                    'fileName' => $avatarName,
                    'fileType' => 'image',
                    'width' => $width,
                    'height' => $height,
                    'duration' => 0,
                    'isPublic' => true,
                ]);
            }
        }
    }
}
