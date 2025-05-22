<?php

declare(strict_types=1);

namespace App\Http\Resources\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method currentPage()
 * @method firstItem()
 * @method lastPage()
 * @method perPage()
 * @method lastItem()
 * @method total()
 * @method hasMorePages()
 */
trait PaginationConcern
{
    public function addPaginationIndexes(array $collection_data): array
    {
        $pagination = [
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
        ];

        // LengthAwarePaginator Fields
        if ($this->resource instanceof LengthAwarePaginator) {
            $pagination['last_page'] = $this->lastPage();
            $pagination['total'] = $this->total();

        } else { // Paginator Fields
            $pagination['next_page'] = $this->hasMorePages() ? ($this->currentPage() + 1) : null;
            $pagination['prev_page'] = $this->currentPage() > 1 ? ($this->currentPage() - 1) : null;
        }

        return array_merge($collection_data, ['pagination' => $pagination]);
    }
}
