<?php

namespace App\Modules\Version\Store;

use App\Models\Version;

class VersionStoreData {
  public function __construct(
    public readonly Version $version,
    public readonly array $files,
  )
  {
    
  }

  public static function fromRequest(VersionStoreRequest $request): self
  {
    $request->validated();

    $version = new Version();
    $version->fill([
      'name' => $request->input('name'),
      'description' => $request->input('description'),
      'status' => $request->input('status', 0),
      'importance' => $request->input('importance', 0),
      'product_id' => $request->input('product_id'),
    ]);

    return new self($version, $request->file('files'));
  }
} 