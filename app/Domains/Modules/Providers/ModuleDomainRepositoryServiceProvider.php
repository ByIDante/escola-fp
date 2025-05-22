<?php

declare(strict_types=1);

namespace App\Domains\Modules\Providers;

use App\Domains\Modules\Repositories\ModuleRepository;
use App\Domains\Modules\Repositories\ModuleRepositoryInterface;
use Illuminate\Support\ServiceProvider;

final class ModuleDomainRepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        ModuleRepositoryInterface::class => ModuleRepository::class,
    ];

    /**
     * @return void
     */
    public function boot(): void
    {
    }
}
