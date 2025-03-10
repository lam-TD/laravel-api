<?php

namespace App\Supports\Tests\Unit\Helpers\File;

uses(FileEncrypter::class)->group('file-encrypter');

it('can encrypt a file', function () {
    $file = new UploadedFile('test.txt', 'test.txt', 'text/plain', null, true);

    $encrypter = FileEncrypter::encryptFile($file);
    $encryptedFile = $encrypter->toFile();
});

it('can decrypt a file', function () {
    $file = new UploadedFile('test.txt', 'test.txt', 'text/plain', null, true);

    $encrypter = FileEncrypter::encryptFile($file);
    $encryptedFile = $encrypter->toFile();
});


