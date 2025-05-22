<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Pagination\LengthAwarePaginator;
use JsonSerializable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Concerns\PaginationConcern;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class TeacherResourceCollection extends ResourceCollection
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
        $collection_data = ['data' => TeacherResource::collection($this->collection)];

        if ( ! $this->resource instanceof Paginator && ! $this->resource instanceof LengthAwarePaginator) {
            return $collection_data;
        }

        return $this->addPaginationIndexes($collection_data);
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request): JsonResponse
    {
        return JsonResource::toResponse($request);
    }
}
