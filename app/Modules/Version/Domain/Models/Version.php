<?php

namespace App\Modules\Version\Domain\Models;

use App\Models\File;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Version extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'importance',
        'product_id',
        'order',
    ];

    protected $casts = [
        'status' => 'integer',
        'importance' => 'integer',
        'order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function isActive(): bool
    {
        return $this->status === 1;
    }

    public function activate(): void
    {
        $this->status = 1;
        $this->save();
    }

    public function deactivate(): void
    {
        $this->status = 0;
        $this->save();
    }

    public function setImportance(int $level): void
    {
        if ($level < 0 || $level > 3) {
            throw new \InvalidArgumentException('Importance level must be between 0 and 3');
        }
        $this->importance = $level;
        $this->save();
    }
} 