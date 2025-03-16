<?php

namespace App\Modules\Version\Update;

use App\Models\Version;

class VersionUpdateData
{
    public function __construct(public readonly Version $version, public readonly ?array $files) {}

    public static function fromRequest(VersionUpdateRequest $request): self
    {
        $request->validated();
        $version = $request->route('version');

        $version->fill($request->only([
            'name',
            'description',
            'status',
            'importance',
        ]));

        return new self($version, $request->file('files') ?? []);
    }
}
