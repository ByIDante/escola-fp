<?php

use App\Domains\Evaluations\Providers\EvaluationDomainRepositoryServiceProvider;
use App\Domains\Modules\Providers\ModuleDomainRepositoryServiceProvider;
use App\Domains\Shared\Providers\SharedDomainRepositoryServiceProvider;
use App\Domains\Students\Providers\StudentDomainRepositoryServiceProvider;
use App\Domains\Teachers\Providers\TeacherDomainRepositoryServiceProvider;
use App\Domains\Units\Providers\UnitDomainRepositoryServiceProvider;
use App\Domains\Users\Providers\UserDomainRepositoryServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RouteServiceProvider::class,

    // Domain Providers
    UserDomainRepositoryServiceProvider::class,
    SharedDomainRepositoryServiceProvider::class,
    EvaluationDomainRepositoryServiceProvider::class,
    ModuleDomainRepositoryServiceProvider::class,
    StudentDomainRepositoryServiceProvider::class,
    TeacherDomainRepositoryServiceProvider::class,
    UnitDomainRepositoryServiceProvider::class,
];
