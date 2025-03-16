<?php

namespace App\Modules\Version\Domain\Repositories;

use App\Modules\Version\Domain\Models\Version;
use Illuminate\Database\Eloquent\Collection;

interface VersionRepository
{
    public function findById(int $id): ?Version;
    public function findByName(string $name): ?Version;
    public function findByProductId(int $productId): Collection;
    public function create(array $data): Version;
    public function update(Version $version, array $data): Version;
    public function delete(Version $version): bool;
    public function getVersionsBetween(int $productId, string $fromVersion, string $toVersion): Collection;
} 