<?php

namespace App\Modules\Version\Compressor;

use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class ZipDriver implements CompressorInerface
{
    public function __construct(protected string $target, protected string $destination, protected string $password = '', protected int $timeout = 300) {}

    public function run()
    {
        $cmd = ['zip'];

        if (File::isDirectory($this->target)) {
            $cmd[] = '-r';
        }

        if ($this->password) {
            $cmd = [...$cmd, '-P', $this->password];
        }

        $cmd = [...$cmd, $this->destination, $this->target];

        $process = new Process(command: $cmd, timeout: $this->timeout);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \Exception('Can not zip');
        }

        return true;
    }
}
