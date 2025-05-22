<?php

declare(strict_types=1);

namespace App\Domains\Students\Providers;

use App\Domains\Students\Repositories\StudentRepository;
use App\Domains\Students\Repositories\StudentRepositoryInterface;
use Illuminate\Support\ServiceProvider;

final class StudentDomainRepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        StudentRepositoryInterface::class => StudentRepository::class
    ];

    /**
     * @return void
     */
    public function boot(): void
    {

    }
}
