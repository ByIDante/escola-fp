<?php

declare(strict_types=1);

namespace App\Domains\Teachers\Providers;

use App\Domains\Teachers\Repositories\TeacherRepository;
use App\Domains\Teachers\Repositories\TeacherRepositoryInterface;
use Illuminate\Support\ServiceProvider;

final class TeacherDomainRepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        TeacherRepositoryInterface::class => TeacherRepository::class
    ];

    /**
     * @return void
     */
    public function boot(): void
    {

    }
}
