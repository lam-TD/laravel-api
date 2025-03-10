<?php

namespace App\Supports\Tests\Unit\Helpers\File;

use App\Supports\Helpers\File\FileEncryter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;

uses(Tests\TestCase::class)->group('file-encryter');

beforeEach(function () {
    // Set up environment variables for testing
    putenv('FILE_ENCRYPTION_KEY=0123456789abcdef0123456789abcdef');
    putenv('FILE_ENCRYPTION_IV=0123456789abcdef');
    
    // Create a test directory
    Storage::fake('local');
    
    // Create a test file with content
    $this->testContent = 'This is test content for encryption';
    $this->testFilePath = Storage::path('test.txt');
    file_put_contents($this->testFilePath, $this->testContent);
    
    // Create a large test file (11MB) for testing chunking
    $this->largeTempFile = Storage::path('large-test.txt');
    $handle = fopen($this->largeTempFile, 'w');
    $largeContent = str_repeat('Large file test content. ', 550000); // ~11MB
    fwrite($handle, $largeContent);
    fclose($handle);
    
    // Create an uploaded file for testing
    $this->uploadedFile = UploadedFile::fake()->create('test-upload.txt', 100);
});

afterEach(function () {
    // Clean up test files
    if (file_exists($this->testFilePath)) {
        unlink($this->testFilePath);
    }
    
    if (file_exists($this->largeTempFile)) {
        unlink($this->largeTempFile);
    }
    
    // Remove environment variables
    putenv('FILE_ENCRYPTION_KEY');
    putenv('FILE_ENCRYPTION_IV');
});

test('can encrypt a string', function () {
    $encrypter = new FileEncryter();
    $encrypted = $encrypter->encrypt($this->testContent);
    
    expect($encrypted)->not->toBe($this->testContent);
    expect($encrypter->toString())->toBe($encrypted);
});

test('can encrypt a file', function () {
    $encrypter = new FileEncryter();
    $encrypted = $encrypter->encrypt($this->testFilePath);
    
    expect($encrypted)->not->toBe($this->testContent);
    expect($encrypter->toString())->toBe($encrypted);
});

test('can encrypt a large file using chunking', function () {
    $encrypter = new FileEncryter();
    $encrypted = $encrypter->encrypt($this->largeTempFile);
    
    expect($encrypted)->not->toBeEmpty();
    expect($encrypter->toString())->toBe($encrypted);
});

test('can encrypt an uploaded file', function () {
    $encrypter = new FileEncryter();
    $encrypted = $encrypter->encrypt($this->uploadedFile);
    
    expect($encrypted)->not->toBeEmpty();
    expect($encrypter->toString())->toBe($encrypted);
    expect($encrypter->getOriginalFilename())->toBe('test-upload.txt');
});

test('can decrypt encrypted content', function () {
    $encrypter = new FileEncryter();
    $encrypted = $encrypter->encrypt($this->testContent);
    $decrypted = $encrypter->decrypt($encrypted);
    
    expect($decrypted)->toBe($this->testContent);
});

test('can save encrypted content to a file', function () {
    $encrypter = new FileEncryter();
    $encrypter->encrypt($this->testContent);
    
    $outputPath = Storage::path('encrypted.bin');
    $savedPath = $encrypter->toFile($outputPath);
    
    expect($savedPath)->toBe($outputPath);
    expect(file_exists($outputPath))->toBeTrue();
    
    // Clean up
    if (file_exists($outputPath)) {
        unlink($outputPath);
    }
});

test('can save encrypted content to a file with default name', function () {
    $encrypter = new FileEncryter();
    $encrypter->encrypt($this->testFilePath);
    
    $savedPath = $encrypter->toFile();
    
    expect(file_exists($savedPath))->toBeTrue();
    
    // Clean up
    if (file_exists($savedPath)) {
        unlink($savedPath);
    }
});

test('can check if a file is encrypted', function () {
    $encrypter = new FileEncryter();
    $encrypter->encrypt($this->testContent);
    
    $outputPath = Storage::path('encrypted-check.bin');
    $encrypter->toFile($outputPath);
    
    $isEncrypted = $encrypter->isEncrypted($outputPath);
    expect($isEncrypted)->toBeTrue();
    
    $isPlainTextEncrypted = $encrypter->isEncrypted($this->testFilePath);
    expect($isPlainTextEncrypted)->toBeFalse();
    
    // Clean up
    if (file_exists($outputPath)) {
        unlink($outputPath);
    }
});

test('static encryptFile method works with file path', function () {
    $encrypter = FileEncryter::encryptFile($this->testFilePath);
    
    expect($encrypter)->toBeInstanceOf(FileEncryter::class);
    expect($encrypter->toString())->not->toBeEmpty();
});

test('static encryptFile method works with uploaded file', function () {
    $encrypter = FileEncryter::encryptFile($this->uploadedFile);
    
    expect($encrypter)->toBeInstanceOf(FileEncryter::class);
    expect($encrypter->toString())->not->toBeEmpty();
    expect($encrypter->getOriginalFilename())->toBe('test-upload.txt');
});

test('throws exception when encryption key is not set', function () {
    putenv('FILE_ENCRYPTION_KEY');
    
    $encrypter = new FileEncryter();
    
    expect(fn() => $encrypter->encrypt($this->testContent))
        ->toThrow(Exception::class, 'Encryption key or IV not set in environment variables');
});

test('throws exception when IV is not set', function () {
    putenv('FILE_ENCRYPTION_IV');
    
    $encrypter = new FileEncryter();
    
    expect(fn() => $encrypter->encrypt($this->testContent))
        ->toThrow(Exception::class, 'Encryption key or IV not set in environment variables');
});

test('throws exception when trying to save without encrypting first', function () {
    $encrypter = new FileEncryter();
    
    expect(fn() => $encrypter->toFile())
        ->toThrow(Exception::class, 'No content to save. Encrypt a file first.');
});

test('throws exception when trying to get string without encrypting first', function () {
    $encrypter = new FileEncryter();
    
    expect(fn() => $encrypter->toString())
        ->toThrow(Exception::class, 'No content available. Encrypt a file first.');
}); 