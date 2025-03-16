<?php

namespace App\Modules\Version\Infrastructure\Services;

use App\Models\File;
use App\Modules\Version\Domain\Models\Version;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileStorageService
{
    public function storeUpdatePatch(Version $version, UploadedFile $file): File
    {
        return $this->storeFile($version, $file, 'update_patch');
    }

    public function storeReleaseNote(Version $version, UploadedFile $file): File
    {
        return $this->storeFile($version, $file, 'release_note');
    }

    public function updateUpdatePatch(Version $version, UploadedFile $file): File
    {
        $this->deleteFileByType($version, 'update_patch');
        return $this->storeUpdatePatch($version, $file);
    }

    public function updateReleaseNote(Version $version, UploadedFile $file): File
    {
        $this->deleteFileByType($version, 'release_note');
        return $this->storeReleaseNote($version, $file);
    }

    public function deleteVersionFiles(Version $version): void
    {
        $version->files->each(function (File $file) {
            $this->deleteFile($file);
        });
    }

    private function storeFile(Version $version, UploadedFile $uploadedFile, string $type): File
    {
        $path = $uploadedFile->store("versions/{$version->id}/{$type}");
        
        return File::create([
            'version_id' => $version->id,
            'name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'type' => $type,
            'size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType(),
        ]);
    }

    private function deleteFileByType(Version $version, string $type): void
    {
        $file = $version->files()->where('type', $type)->first();
        if ($file) {
            $this->deleteFile($file);
        }
    }

    private function deleteFile(File $file): void
    {
        Storage::delete($file->path);
        $file->delete();
    }
} 