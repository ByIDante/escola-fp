<?php

declare(strict_types=1);

namespace App\Domains\Units\Repositories;

use App\Domains\Shared\Repositories\BaseRepository;
use App\Models\Unit;

final class UnitRepository extends BaseRepository implements UnitRepositoryInterface
{
    protected string $model = Unit::class;

    /*** SETTERS ***/

    /*** GETTERS ***/

    /*** DELETERS ***/
}
