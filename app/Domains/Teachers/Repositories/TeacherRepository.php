<?php

declare(strict_types=1);

namespace App\Domains\Teachers\Repositories;

use App\Domains\Shared\Repositories\BaseRepository;
use App\Models\Teacher;

final class TeacherRepository extends BaseRepository implements TeacherRepositoryInterface
{
    protected string $model = Teacher::class;

    /*** SETTERS ***/

    /*** GETTERS ***/

    /*** DELETERS ***/

}
