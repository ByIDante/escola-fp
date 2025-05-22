<?php

declare(strict_types=1);

namespace App\Domains\Evaluations\Providers;

use App\Domains\Evaluations\Repositories\EvaluationRepository;
use App\Domains\Evaluations\Repositories\EvaluationRepositoryInterface;
use Illuminate\Support\ServiceProvider;

final class EvaluationDomainRepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        EvaluationRepositoryInterface::class => EvaluationRepository::class
    ];

    /**
     * @return void
     */
    public function boot(): void
    {

    }
}
