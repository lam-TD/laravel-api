<?php

namespace App\Modules\Version\Infrastructure\Repositories;

use App\Modules\Version\Domain\Models\Version;
use App\Modules\Version\Domain\Repositories\VersionRepository;
use Illuminate\Database\Eloquent\Collection;

class EloquentVersionRepository implements VersionRepository
{
    public function findById(int $id): ?Version
    {
        return Version::find($id);
    }

    public function findByName(string $name): ?Version
    {
        return Version::where('name', $name)->first();
    }

    public function findByProductId(int $productId): Collection
    {
        return Version::where('product_id', $productId)
            ->orderBy('order')
            ->get();
    }

    public function create(array $data): Version
    {
        return Version::create($data);
    }

    public function update(Version $version, array $data): Version
    {
        $version->update($data);
        return $version;
    }

    public function delete(Version $version): bool
    {
        return $version->delete();
    }

    public function getVersionsBetween(int $productId, string $fromVersion, string $toVersion): Collection
    {
        $query = Version::where('product_id', $productId);

        if ($fromVersion) {
            $fromOrder = Version::where('product_id', $productId)
                ->where('name', $fromVersion)
                ->value('order');

            $query->where('order', '>=', $fromOrder);
        }

        if ($toVersion) {
            $toOrder = Version::where('product_id', $productId)
                ->where('name', $toVersion)
                ->value('order');

            $query->where('order', '<=', $toOrder);
        }

        return $query->orderBy('order')->get();
    }
} 