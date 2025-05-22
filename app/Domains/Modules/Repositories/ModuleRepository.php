<?php

declare(strict_types=1);

namespace App\Domains\Modules\Repositories;

use App\Domains\Shared\Repositories\BaseRepository;
use App\Models\Module;

final class ModuleRepository extends BaseRepository implements ModuleRepositoryInterface
{
    protected string $model = Module::class;

    /*** SETTERS ***/

    /*** GETTERS ***/

    /*** DELETERS ***/
}
