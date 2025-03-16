<?php

namespace App\Modules\Version\Domain\Services;

use App\Modules\Version\Domain\DTOs\VersionData;
use App\Modules\Version\Domain\Models\Version;
use App\Modules\Version\Domain\Repositories\VersionRepository;
use App\Modules\Version\Infrastructure\Services\FileStorageService;
use Illuminate\Support\Facades\DB;

class VersionService
{
    public function __construct(
        private readonly VersionRepository $repository,
        private readonly FileStorageService $fileStorage
    ) {}

    public function createVersion(VersionData $data): Version
    {
        return DB::transaction(function () use ($data) {
            $version = $this->repository->create([
                'name' => $data->name,
                'description' => $data->description,
                'status' => $data->status,
                'importance' => $data->importance,
                'product_id' => $data->productId,
                'order' => $data->order ?? $this->getNextOrder($data->productId),
            ]);

            if ($data->updatePatch) {
                $this->fileStorage->storeUpdatePatch($version, $data->updatePatch);
            }

            if ($data->releaseNote) {
                $this->fileStorage->storeReleaseNote($version, $data->releaseNote);
            }

            return $version;
        });
    }

    public function updateVersion(Version $version, VersionData $data): Version
    {
        return DB::transaction(function () use ($version, $data) {
            $version = $this->repository->update($version, [
                'name' => $data->name,
                'description' => $data->description,
                'status' => $data->status,
                'importance' => $data->importance,
                'product_id' => $data->productId,
                'order' => $data->order ?? $version->order,
            ]);

            if ($data->updatePatch) {
                $this->fileStorage->updateUpdatePatch($version, $data->updatePatch);
            }

            if ($data->releaseNote) {
                $this->fileStorage->updateReleaseNote($version, $data->releaseNote);
            }

            return $version;
        });
    }

    public function deleteVersion(Version $version): bool
    {
        return DB::transaction(function () use ($version) {
            $this->fileStorage->deleteVersionFiles($version);
            return $this->repository->delete($version);
        });
    }

    public function getVersionsBetween(int $productId, string $fromVersion, string $toVersion)
    {
        return $this->repository->getVersionsBetween($productId, $fromVersion, $toVersion);
    }

    private function getNextOrder(int $productId): int
    {
        $maxOrder = Version::where('product_id', $productId)->max('order');
        return ($maxOrder ?? 0) + 1;
    }
} 