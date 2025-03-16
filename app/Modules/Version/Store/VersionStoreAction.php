<?php

namespace App\Modules\Version\Store;

use App\Models\Version;
use App\Modules\Version\Exceptions\VersionCreateException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VersionStoreAction
{
    public function execute(VersionStoreData $data): Version
    {
        $version = $data->version;
        $files = $data->files;

        try {
            DB::beginTransaction();

            $version->saveOrFail();

            $tempFiles = [];

            foreach ($files as $type => $file) {
                $tempFiles[] = $file->storeAs("versions/{$version->id}", $file->hashName());
                $version->files()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => "versions/{$version->id}/{$file->hashName()}",
                    'type' => $type,
                    'size' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            foreach ($tempFiles as $file) {
                Storage::delete($file);
            }
            throw new VersionCreateException('Failed to create the version: '.$e->getMessage());
        }

        return $version->refresh();
    }
}
