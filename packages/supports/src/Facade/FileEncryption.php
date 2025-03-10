<?php

namespace App\Supports\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string encrypt(string $file)
 * @method static string decrypt(string $encryptedContent)
 * @method static bool isEncrypted(string $file)
 * @method static string toString()
 * @method static bool toFile(string $file)
 */
class FileEncryption extends Facade {
    protected static function getFacadeAccessor() {
        return 'FileEncryption';
    }
}

