<?php

namespace App\Modules\Version\Domain\DTOs;

use Illuminate\Http\UploadedFile;

class VersionData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $status,
        public readonly int $importance,
        public readonly int $productId,
        public readonly ?int $order = null,
        public readonly ?UploadedFile $updatePatch = null,
        public readonly ?UploadedFile $releaseNote = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            name: $request->input('name'),
            description: $request->input('description'),
            status: (int) $request->input('status', 0),
            importance: (int) $request->input('importance', 0),
            productId: (int) $request->input('product_id'),
            order: $request->input('order'),
            updatePatch: $request->file('files.update_patch'),
            releaseNote: $request->file('files.release_note'),
        );
    }
} 