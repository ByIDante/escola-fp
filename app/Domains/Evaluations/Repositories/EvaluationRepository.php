<?php

declare(strict_types=1);

namespace App\Domains\Evaluations\Repositories;

use App\Domains\Shared\Repositories\BaseRepository;
use App\Models\Evaluation;

final class EvaluationRepository extends BaseRepository implements EvaluationRepositoryInterface
{
    protected string $model = Evaluation::class;

    /*** SETTERS ***/

    /*** GETTERS ***/

    /*** DELETERS ***/

}
