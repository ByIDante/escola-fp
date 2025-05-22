<?php

declare(strict_types=1);

namespace App\Domains\Students\Repositories;

use App\Domains\Shared\Repositories\BaseRepository;
use App\Models\Student;

final class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    protected string $model = Student::class;

    /*** SETTERS ***/

    /*** GETTERS ***/

    /*** DELETERS ***/

}
