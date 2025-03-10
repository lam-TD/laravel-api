<?php

namespace App\Supports\Helpers\File;

use Exception;
use Illuminate\Http\UploadedFile;

/**
 * Class for encrypting and decrypting files using AES-256-CBC
 */
class FileEncryter {
    private $content;
    private $encrypted = false;
    private $chunkSize = 10485760; // 10MB in bytes
    private $originalFilename;

    private $key;
    private $iv;

    private $algorithm = 'AES-256-CBC';

    /**
     * Constructor
     */
    public function __construct($key, $iv, $algorithm = 'AES-256-CBC') {
        $this->content = null;
        $this->originalFilename = null;

        if ($key) {
            $this->key = $key;
        }

        if ($iv) {
            $this->iv = $iv;
        }

        $this->setAlgorithm($algorithm);
    }

    public function setAlgorithm($algorithm)
    {
        if (!in_array($algorithm, openssl_get_cipher_methods())) {
            throw new Exception('Invalid algorithm');
        }

        $this->algorithm = $algorithm;
    }

    /**
     * Encrypt file content
     * 
     * @param string|UploadedFile $file Path to file, file content, or UploadedFile
     * @return string Encrypted content
     * @throws Exception If encryption fails
     */
    public function encrypt($file) {
        // Handle UploadedFile objects
        if ($file instanceof UploadedFile) {
            $this->originalFilename = $file->getClientOriginalName();
            return $this->encryptUploadedFile($file);
        }
        
        // Check if $file is a file path or content
        if (is_string($file) && file_exists($file) && is_file($file)) {
            $fileSize = filesize($file);
            $this->originalFilename = basename($file);
            
            // Handle large files by chunking
            if ($fileSize > $this->chunkSize) {
                return $this->encryptLargeFile($file);
            }
            
            $content = file_get_contents($file);
        } else {
            $content = $file;
        }

        if (!$this->key || !$this->iv) {
            throw new Exception('Encryption key or IV not set in environment variables');
        }

        $encrypted = openssl_encrypt($content, $this->algorithm, $this->key, OPENSSL_RAW_DATA, $this->iv);
        
        if ($encrypted === false) {
            throw new Exception('Failed to encrypt content: ' . openssl_error_string());
        }
        
        $this->content = $encrypted;
        $this->encrypted = true;
        
        return $encrypted;
    }
    
    /**
     * Encrypt an uploaded file
     * 
     * @param UploadedFile $file The uploaded file
     * @return string Encrypted content
     * @throws Exception If encryption fails
     */
    private function encryptUploadedFile(UploadedFile $file) {
        $tempPath = $file->getRealPath();
        
        if (filesize($tempPath) > $this->chunkSize) {
            return $this->encryptLargeFile($tempPath);
        }
        
        $content = file_get_contents($tempPath);
        return $this->encrypt($content);
    }
    
    /**
     * Encrypt large file by chunking
     * 
     * @param string $filePath Path to file
     * @return string Encrypted content
     * @throws Exception If encryption fails
     */
    private function encryptLargeFile(string $filePath) {
        if (!$this->key || !$this->iv) {
            throw new Exception('Encryption key or IV not set in environment variables');
        }
        
        $fileHandle = fopen($filePath, 'rb');
        if ($fileHandle === false) {
            throw new Exception("Failed to open file: $filePath");
        }
        
        $encryptedContent = '';
        
        while (!feof($fileHandle)) {
            $chunk = fread($fileHandle, $this->chunkSize);
            $encryptedChunk = openssl_encrypt($chunk, $this->algorithm, $this->key, OPENSSL_RAW_DATA, $this->iv);
            
            if ($encryptedChunk === false) {
                fclose($fileHandle);
                throw new Exception('Failed to encrypt chunk: ' . openssl_error_string());
            }
            
            $encryptedContent .= $encryptedChunk;
        }
        
        fclose($fileHandle);
        
        $this->content = $encryptedContent;
        $this->encrypted = true;
        
        return $encryptedContent;
    }
    
    /**
     * Decrypt content with chunking
     * 
     * @param string $encryptedContent Encrypted content
     * @return string Decrypted content
     * @throws Exception If decryption fails
     */
    public function decrypt(string $encryptedContent) {
        if (!$this->key || !$this->iv) {
            throw new Exception('Encryption key or IV not set in environment variables');
        }
        
        $decrypted = '';
        while ($encryptedContent) { 
            $decrypted .= openssl_decrypt($encryptedContent, $this->algorithm, $this->key, OPENSSL_RAW_DATA, $this->iv);
            $encryptedContent = substr($encryptedContent, strlen($decrypted));
        }
        
        if ($decrypted === false) {
            throw new Exception('Failed to decrypt content: ' . openssl_error_string());
        }
        
        return $decrypted;
    }

    /**
     * Check if file is encrypted
     * 
     * @param string $file Path to file
     * @return bool True if file is encrypted
     */
    public function isEncrypted(string $file) {
        if (!file_exists($file) || !is_file($file)) {
            return false;
        }
        
        // Try to decrypt the first few bytes to check if it's encrypted
        try {
            $sample = file_get_contents($file, false, null, 0, 100);
            $this->decrypt($sample);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Save encrypted content to file
     * 
     * @param string|null $file Path to output file, if null uses original filename with .enc extension
     * @return string Path to the saved file
     * @throws Exception If saving fails
     */
    public function toFile(?string $file = null) {  
        if (!$this->content) {
            throw new Exception('No content to save. Encrypt a file first.');
        }
        
        // If no file path is provided, use the original filename with .enc extension
        if ($file === null) {
            if (!$this->originalFilename) {
                throw new Exception('No original filename available and no output path provided');
            }
            
            $file = sys_get_temp_dir() . '/' . pathinfo($this->originalFilename, PATHINFO_FILENAME) . '.enc';
        }
        
        $result = file_put_contents($file, $this->content);
        
        if ($result === false) {
            throw new Exception("Failed to write encrypted content to file: $file");
        }
        
        return $file;
    }

    /**
     * Get encrypted content as string
     * 
     * @return string Encrypted content
     * @throws Exception If no content is available
     */
    public function toString() {
        if (!$this->content) {
            throw new Exception('No content available. Encrypt a file first.');
        }
        
        return $this->content;
    }
    
    /**
     * Get the original filename
     * 
     * @return string|null Original filename
     */
    public function getOriginalFilename() {
        return $this->originalFilename;
    }
    
    /**
     * Static method to create a new instance and encrypt a file
     * 
     * @param string|UploadedFile $file Path to file or UploadedFile
     * @return FileEncryter Instance with encrypted content
     * @throws Exception If encryption fails
     */
    public static function encryptFile($file) {
        $instance = new self();
        
        if (is_string($file) && $instance->isEncrypted($file)) {
            $instance->content = file_get_contents($file);
            $instance->encrypted = true;
            $instance->originalFilename = basename($file);
            return $instance;
        }
        
        $instance->encrypt($file);
        return $instance;
    }
}