<?php

declare(strict_types=1);

namespace App\Domains\Shared\Providers;

use App\Domains\Shared\Repositories\BaseRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Domains\Shared\Repositories\BaseRepository;

final class SharedDomainRepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        BaseRepositoryInterface::class => BaseRepository::class,
    ];

    /**
     * @return void
     */
    public function boot(): void
    {
    }
}
