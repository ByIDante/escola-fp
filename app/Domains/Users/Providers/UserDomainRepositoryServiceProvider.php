<?php

declare(strict_types=1);

namespace App\Domains\Users\Providers;

use App\Domains\Users\Repositories\UserRepository;
use App\Domains\Users\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

final class UserDomainRepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
    ];

    /**
     * @return void
     */
    public function boot(): void
    {
    }
}
