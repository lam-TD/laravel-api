<?php

namespace App\Supports\Console\Command;

use Illuminate\Console\Command;

class FileEncryptionCommand extends Command
{
    protected $signature = 'file:encrypt';

    protected $description = 'Encrypt all files in the storage/app directory';

    public function handle()
    {
        $options = [
            'path' => $this->argument('path'),
            'recursive' => $this->option('recursive'),
        ];

        
        
    }
    
}