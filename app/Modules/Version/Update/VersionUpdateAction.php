<?php

namespace App\Modules\Version\Update;

use App\Models\Version;
use App\Modules\Version\Exceptions\VersionUpdateException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VersionUpdateAction
{
    public function execute(VersionUpdateData $data): Version
    {
        $version = $data->version;
        $files = $data->files;

        try {
            DB::beginTransaction();

            $version->saveOrFail();

            $timestamp = Str::uuid();
            $tempFiles = [];
            $fileRollback = [];

            foreach ($files as $type => $file) {
                $tempFiles[] = $file->storeAs("versions/{$version->id}", $file->hashName());
                $version->files()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => "versions/{$version->id}/{$file->hashName()}",
                    'type' => $type,
                    'size' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension(),
                ]);

                if (in_array($type, ['update_patch', 'release_note'])) {
                    $f = $version->files->where('type', $type)->first();
                    $f->delete();

                    $trashPath = "trash/{$timestamp}/{$f->path}";
                    Storage::move($f['path'], $trashPath);
                    $fileRollback[] = $trashPath;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            foreach ($tempFiles as $file) {
                Storage::delete($file);
            }

            foreach ($fileRollback as $file) {
                $path = explode('/', $file);
                $path = array_slice($path, 0, count($path) - 1);
                $path = implode('/', $path);
                Storage::move($file, $path);
            }

            throw $e;
            // throw new VersionUpdateException('Failed to update the version: ' . $e->getMessage());
        }

        return $data->version;
    }
}
