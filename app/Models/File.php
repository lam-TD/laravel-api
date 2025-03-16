<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name',
        'path',
        'type',
        'size',
        'extension',
    ];

    protected $hidden = [
        'fileable_type',
        'fileable_id',
        'encrypt_key',
        'encrypt_iv',
    ];

    public function version()
    {
        return $this->morphTo();
    }
}
