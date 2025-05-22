<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Http\Responses\Concerns\ReturnJsonResponse;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

final class NoContentResponse implements Responsable
{
    use ReturnJsonResponse;

    /**
     * @param array $data
     * @param int $status
     */
    public function __construct(
        private readonly array $data = [],
        private readonly int $status = Response::HTTP_NO_CONTENT,
    ) {
    }
}
