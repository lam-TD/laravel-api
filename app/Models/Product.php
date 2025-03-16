<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'status',
        'logo_color',
        'description',
    ];

    public function versions()
    {
        return $this->hasMany(Version::class);
    }
}
