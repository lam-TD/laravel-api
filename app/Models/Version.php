<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'importance',
        'product_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
