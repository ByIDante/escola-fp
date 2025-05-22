<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Pagination\LengthAwarePaginator;
use JsonSerializable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use App\Http\Resources\Concerns\PaginationConcern;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class ModuleResourceCollection extends ResourceCollection
{
    use PaginationConcern;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        $collection_data = ['data' => ModuleResource::collection($this->collection)];

        if ( ! $this->resource instanceof Paginator && ! $this->resource instanceof LengthAwarePaginator) {
            return $collection_data;
        }

        return $this->addPaginationIndexes($collection_data);
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param Request $request
     * @param JsonResponse $response
     * @return void
     */
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode(200);
    }
}
