<?php

namespace App\Modules\Version;

use App\Models\Product;
use App\Models\Version;
use Illuminate\Support\Collection;
use Storage;

class VersionStorage
{
    protected string $path;

    protected Collection $collection;

    public function __construct(protected int|string|Product $product) {}

    public static function fromProduct(int|string|Product $product)
    {
        return new self($product instanceof Product ? $product->getKey() : $product);
    }

    protected function getCollection($from = '', $to = '', $fromOperator = '>=', $toOperator = '<=')
    {
        return VersionQuery::fromModel(new Version)
            ->belongToProduct($this->product)
            ->from($from, $fromOperator)
            ->to($to, $toOperator)
            ->asc()
            ->get();
    }

    public function getByName($from = '', $to = '', $fromOperator = '>=', $toOperator = '<=')
    {
        $this->collection = $this->getCollection($from, $to, $fromOperator, $toOperator);

        $key = $this->collection->implode('name', '~');

        $zipName = $key.'.zip';
        $link = config('version.file.cache').'/'.$this->product.'/'.$zipName;
        $link = Storage::path($link);

        // VersionCache::store()->forget("$this->product~$key");

        $this->path = VersionCache::store()->remember("$this->product~$key", function () use ($zipName, $link) {
            return VersionCompressor::fromCollection($this->collection)
                ->withPassword('123')
                ->withParentDir($this->product)
                ->zip($zipName)
                ->linkTo($link);
        });

        return VersionStorageData::fromFile($this->path)->withCollection($this->collection);
    }

    public function getByModel() {}
}
