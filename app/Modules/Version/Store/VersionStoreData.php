<?php

namespace App\Modules\Version\Store;

use App\Models\Version;

class VersionStoreData
{
    public function __construct(
        public readonly Version $version,
        public readonly array $files,
    ) {}

    public static function fromRequest(VersionStoreRequest $request): self
    {
        $request->validated();

        $productId = $request->input('product_id');

        $version = new Version;

        $order = $version->newQuery()->where('product_id', $productId)->orderBy('order', 'desc')->first();
        $order = isset($order['order']) ? $order['order'] + 1 : 1;
        $version->fill([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status', 0),
            'importance' => $request->input('importance', 0),
            'product_id' => $productId,
            'order' => $order,
        ]);

        return new self($version, $request->file('files'));
    }
}
