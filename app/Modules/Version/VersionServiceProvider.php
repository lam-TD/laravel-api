<?php

namespace App\Modules\Version;

use App\Modules\Version\Domain\Repositories\VersionRepository;
use App\Modules\Version\Infrastructure\Repositories\EloquentVersionRepository;
use Illuminate\Support\ServiceProvider;

class VersionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(VersionRepository::class, EloquentVersionRepository::class);
    }

    public function boot(): void
    {
        // Boot logic if needed
    }
} 