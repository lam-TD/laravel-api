<?php

namespace App\Modules\Version;

use App\Modules\Version\Compressor\ZipDriver;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VersionCompressor
{
    protected int $timeout = 300;

    protected string $password = '';

    protected string $parentDir = '';

    protected string $workingDir = '';

    protected string $driver;

    public function __construct(protected Collection $collection)
    {
        $this->workingDir = config('version.file.tmp').'/'.Str::uuid();
        $this->driver = 'zip';
    }

    public function driver(string $driver = 'zip')
    {
        $this->driver = $driver;

        return $this;
    }

    public static function fromCollection(Collection $collection)
    {
        return new self($collection);
    }

    public function withPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    public function withParentDir(string $dir)
    {
        $this->parentDir = $dir;

        return $this;
    }

    public function withWorkingDir(string $workingDir)
    {
        $this->workingDir = $workingDir;

        return $this;
    }

    public function zip(string $zipName)
    {
        $targetFolder = Storage::path($this->prepareFiles());

        $zipDir = sprintf('%s/%s', Storage::path($this->workingDir), $this->parentDir);
        $zipFile = sprintf('%s/%s', $zipDir, $zipName);

        $ziper = new ZipDriver($targetFolder, $zipFile, $this->password, $this->timeout);
        $ziper->run();

        $data = new VersionCompressorData($zipFile);

        return $data;
    }

    protected function prepareFiles()
    {
        $tmp = "$this->workingDir/$this->parentDir";
        $this->collection->each(function ($version) use ($tmp) {
            $version->files->each(function ($file) use ($version, $tmp) {
                $destination = "$tmp/$version->name/$file->type/$file->name";
                throw_unless(Storage::copy($file->path, $destination), new Exception('Can not copy file to tmp.'));
            });
        });

        return $tmp;
    }
}
