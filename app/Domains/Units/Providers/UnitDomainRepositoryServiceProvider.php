<?php

declare(strict_types=1);

namespace App\Domains\Units\Providers;

use App\Domains\Units\Repositories\UnitRepository;
use App\Domains\Units\Repositories\UnitRepositoryInterface;
use Illuminate\Support\ServiceProvider;

final class UnitDomainRepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        UnitRepositoryInterface::class => UnitRepository::class,
    ];

    /**
     * @return void
     */
    public function boot(): void
    {
    }
}
