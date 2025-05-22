<?php

declare(strict_types=1);

namespace App\Domains\Users\Repositories;

use App\Domains\Shared\Repositories\BaseRepository;
use App\Models\User;

final class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected string $model = User::class;

    /*** SETTERS ***/

    /*** GETTERS ***/

    /*** DELETERS ***/

}
