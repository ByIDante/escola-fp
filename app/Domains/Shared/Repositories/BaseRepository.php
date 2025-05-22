<?php

declare(strict_types=1);

namespace App\Domains\Shared\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use function is_array;

/**
 * Base Repository
 *
 * @package Ollieread\Toolkit\Repositories
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    protected string $model;

    /**
     * @return Model
     */
    protected function make(): Model
    {
        return new $this->model();
    }

    /**
     * @return Builder
     */
    protected function query(): Builder
    {
        return $this->make()->newQuery();
    }

    /**
     * Accepts either the id or model. It's a safety method so that you can just pass arguments in
     * and receive the id back.
     *
     * @param $model
     *
     * @return int
     */
    protected function getId($model): int
    {
        return $model instanceof Model ? $model->getKey() : $model;
    }

    /**
     * Accepts either the id or model. It's a safety method so that you can just pass arguments in
     * and receive the model back.
     *
     * @param $model
     *
     * @return Model|null
     */
    protected function getOneById($model): ?Model
    {
        return $model instanceof Model ? $model : $this->getOne(['id' => $model]);
    }

    /**
     * Add filters to an Eloquent QueryBuilder instance
     *
     * @param Builder $query A Builder instance where to add the filters
     * @param array $filters (optional) An array containing the <column> <values> to filter the data for
     *
     * @return void
     */
    protected function addFilters(Builder &$query, array $filters): void
    {
        foreach ($filters as $column => $value) {
            $method = is_array($value) ? 'whereIn' : 'where';
            $query = $query->$method($column, $value);
        }
    }

    /**
     * Add with to an Eloquent QueryBuilder instance
     *
     * @param Builder $query A Builder instance where to add the filters
     * @param array $with (optional) An array containing the <relations> we want to load data from
     *
     * @return void
     */
    protected function addWith(Builder &$query, array $with): void
    {
        $query = $query->with($with);
    }


    /*** PUBLIC METHODS ***/

    /*** SETTERS ***/

    public function save(array $input, Model|int|null $model = null): ?Model
    {
        if (null !== $model) {
            $model = $this->getOneById($model);
        } else {
            $model = $this->make();
        }

        if ($model instanceof $this->model) {
            $model->fill($input);

            if ($model->save()) {
                return $model;
            }
        }

        return null;
    }

    public function update(array $input, array $data): bool
    {
        $model = $this->make();
        return $model->update($input, $data);
    }

    public function upsert(array $data, array $uniqueKey, ?array $column): bool|int
    {
        $model = $this->make();
        return $model->upsert($data, $uniqueKey, $column);
    }

    public function insert(array $input): int|bool
    {
        $model = $this->make();
        return $model->insert($input);
    }

    /*** GETTERS ***/

    public function getAll(array $filters = null, array $with = null): ?Collection
    {
        $query = $this->query();
        if (null !== $filters) {
            $this->addFilters($query, $filters);
        }
        if (null !== $with) {
            $this->addWith($query, $with);
        }

        return $query->get();
    }

    public function getAllFiltered(array $filters = null, array $with = null): ?Collection
    {
        $query = $this->query();
        if ( ! empty($filters)) {
            $query->filter($filters);
        }
        if (null !== $with) {
            $this->addWith($query, $with);
        }

        return $query->get();
    }

    public function list(array $with = [], array $with_count = [], array $joins = [], array $filters = [], array $pagination = [], array $orders = [], array $order_by_relationship = [], bool $return_builder = false): Paginator|LengthAwarePaginator|Builder
    {
        $query = $this->query();

        if ( ! empty($with_count)) {
            $query->withCount($with_count);
        }

        if ( ! empty($with)) {
            $query->with($with);
        }

        if ( ! empty($joins)) {
            if ('leftJoin' === $joins['type']) {
                $query->leftJoin($joins['table'], $joins['callback']);
            } else {
                $query->rightJoin($joins['table'], $joins['callback']);
            }
        }

        if ( ! empty($filters)) {
            $query->filter($filters);
        }

        if ( ! empty($orders)) {
            foreach ($orders as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        if ( ! empty($order_by_relationship)) {
            foreach ($order_by_relationship as $order) {
                $query->join($order['table'], $order['first'], $order['operator'], $order['second']);

                foreach ($order['orders'] as $column_order) {
                    $query->orderBy($column_order['column'], $column_order['direction']);
                }
            }
        }

        if (isset($pagination['limit'])) {
            $query->limit((int)$pagination['limit']);
        }

        if ($return_builder) {
            return $query;
        }

        if ( ! isset($pagination['simple']) || ! $pagination['simple']) {
            return $query->paginate(perPage: isset($pagination['per_page']) ? (int)$pagination['per_page'] : 15);
        } else {
            return $query->simplePaginate(perPage: isset($pagination['per_page']) ? (int)$pagination['per_page'] : 15);
        }
    }

    public function getCount(array $filters = null): int
    {
        $query = $this->query();
        if (null !== $filters) {
            $this->addFilters($query, $filters);
        }

        return $query->count();
    }

    public function getOne(array $filters = null, array $with = null, array $with_count = null, array $orders = null): ?Model
    {
        $query = $this->query();
        if (null !== $filters) {
            $this->addFilters($query, $filters);
        }

        if (null !== $with_count) {
            $query->withCount($with_count);
        }

        if (null !== $with) {
            $this->addWith($query, $with);
        }

        if ( ! empty($orders)) {
            foreach ($orders as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        return $query->first();
    }

    public function getOneFiltered(array $filters = null, array $with = null, array $with_count = null, array $order = null): ?Model
    {
        $query = $this->query();
        if (null !== $filters) {
            $query->filter($filters);
        }

        if (null !== $with_count) {
            $query->withCount($with_count);
        }

        if (null !== $with) {
            $this->addWith($query, $with);
        }

        if ( ! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        return $query->first();
    }

    public function getLast(array $filters = null, array $with = null): ?Model
    {
        $query = $this->query();
        if (null !== $filters) {
            $this->addFilters($query, $filters);
        }
        if (null !== $with) {
            $this->addWith($query, $with);
        }

        return $query->latest('id')->first();
    }

    /*** DELETERS ***/

    public function delete($model): null|bool|int
    {
        if ($model instanceof Model) {
            return $model->delete();
        }

        $id = $model;
        $model = $this->make();

        return $model->newQuery()
            ->where($model->getKeyName(), $id)
            ->delete();
    }

    /*** HELPERS ***/
}
