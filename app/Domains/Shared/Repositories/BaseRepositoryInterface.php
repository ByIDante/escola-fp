<?php

declare(strict_types=1);

namespace App\Domains\Shared\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Exception;

interface BaseRepositoryInterface
{
    /*** SETTERS ***/

    /**
     * Save the model data.
     *
     * Pass in an array of input, and either an existing model or an id. Passing null to the
     * second argument will create a new instance.
     *
     * @param array $input
     * @param Model|int|null $model
     *
     * @return Model|null
     */
    public function save(array $input, Model|int|null $model = null): ?Model;

    /**
     * Mass upsert model data.
     *
     * Pass in an array of arrays of input containing the values to insert.
     *
     * @param array $data
     * @param array $uniqueKey
     * @param array $column
     *
     * @return int|bool The number of elements inserted
     */
    public function upsert(array $data, array $uniqueKey, array $column): int|bool;

    /**
     * Mass insert model data.
     *
     * Pass in an array of arrays of input containing the values to insert.
     *
     * @param array $input
     *
     * @return int|bool The number of elements inserted
     */
    public function insert(array $input): int|bool;

    /**
     * Update a record matching the attributes, and fill it with values.
     *
     * @param  array  $input
     * @param  array  $data
     * @return bool
     */
    public function update(array $input, array $data): bool;

    /*** GETTERS ***/

    /**
     * Retrieve a Collection with all the found Models. Can be optionally filtered by column values.
     *
     * @param array|null $filters (optional) An array containing the <column> <values> to filter the data for
     * @param array|null $with (optional) An array containing the <relations> we want to load data from
     *
     * @return Collection|null
     */
    public function getAll(array $filters = null, array $with = null): ?Collection;

    /**
     * Retrieve a Collection with all the found Models. Can be optionally filtered by column values.
     *
     * @param array|null $filters
     * @param array|null $with (optional) An array containing the <relations> we want to load data from
     *
     * @return Collection|null
     */
    public function getAllFiltered(array $filters = null, array $with = null): ?Collection;

    /**
     * Get a paginated (or not) list of Models
     *
     * @param array $with
     * @param array $with_count
     * @param array $joins
     * @param array $filters
     * @param array $pagination
     * @param array $orders
     * @param array $order_by_relationship
     * @param bool $return_builder
     * @return Paginator|LengthAwarePaginator|Builder
     */
    public function list(array $with = [], array $with_count = [], array $joins = [], array $filters = [], array $pagination = [], array $orders = [], array $order_by_relationship = [], bool $return_builder = false): Paginator|LengthAwarePaginator|Builder;

    /**
     * Retrieve the first Model found. Can be optionally filtered by column values.
     *
     * @param array|null $filters (optional) An array containing the <column> <values> to filter the data for
     * @param array|null $with (optional) An array containing the <relations> we want to load data from
     * @param array|null $with_count (optional) An array containing the <relation> we want to recover a counter from
     * @param array|null $order (optional) An array containing the column:order key value pairs
     * @return Model|null
     */
    public function getOne(array $filters = null, array $with = null, array $with_count = null, array $order = null): ?Model;

    /**
     * Retrieve the first Model found. Can be optionally filtered by column values.
     *
     * @param array|null $filters (optional) An array containing the <column> <values> to filter the data for
     * @param array|null $with (optional) An array containing the <relations> we want to load data from
     * @param array|null $with_count (optional) An array containing the <relation> we want to recover a counter from
     * @param array|null $order (optional) An array containing the column:order key value pairs
     * @return Model|null
     */
    public function getOneFiltered(array $filters = null, array $with = null, array $with_count = null, array $order = null): ?Model;


    /**
     * Retrieve the last Model found ordered by id. Can be optionally filtered by column values.
     *
     * @param array|null $filters (optional) An array containing the <column> <values> to filter the data for
     * @param array|null $with (optional) An array containing the <relations> we want to load data from
     *
     * @return Model|null
     */
    public function getLast(array $filters = null, array $with = null): ?Model;

    /**
     * Retrieve the number of Models found. Can be optionally filtered by column values.
     *
     * @param array|null $filters (optional) An array containing the <column> <values> to filter the data for
     *
     * @return int
     */
    public function getCount(array $filters = null): int;

    /*** DELETERS ***/

    /**
     * Deletes the given model.
     *
     * @param int|Model $model Pass either a Model or a model id to delete.
     *
     * @return null|bool|int
     * @throws Exception
     */
    public function delete(Model|int $model): null|bool|int;

}
