<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

abstract class ResourceAbstractClass extends Controller
{
    /**
     * The current instance's model.
     *
     * @var mixed
     */
    protected $model;

    /**
     * Dictionary that defines what types of relations can be eager loaded with
     * eloquent relationships.
     *
     * @var array
     */
    protected $eagerLoadOptions = [];

    /**
     * Used for pagination, specifies how many results to show per page.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * Get a paginated listing of the resource.
     *
     * @param array $eagers
     * @param string $search
     *
     * @return Collection
     */
    protected function searchQuery(array $eagers, string $search) : Collection
    {
        return $this->model->with($eagers)
            ->where('name', 'LIKE', '%' . $search . '%')
            ->get();
    }

    /**
     * Resource index actions largely share the same logic, we have a shared
     * method to get the index action data
     *
     * @param Request $request
     *
     * @return array
     */
    protected function indexAction(Request $request)
    {
        // Check for eager loading
        $eagerLoad = $this->resolveEagerLoadQuery($request->input('include', ''));
        // Check for search criteria in the parameters
        $search = $request->input('search', '');
        $items = null;
        if (strlen($search) > 0) {
            $items = $this->searchQuery($eagerLoad, $search);
        }
        // Resolve pagination data
        $paginationMetadata = $this->determinePaginationMetadata(
            intval($request->input('page', 1)),
            is_null($items) ? $this->model->all()->count() : $items->count()
        );
        // Get the correct page from the model
        if (is_null($items)) {
            // No search was performed, then fetch the data
            $items = $this->model->with($eagerLoad)
                ->get()->forPage(
                    $paginationMetadata['current_page'],
                    $paginationMetadata['per_page'],
                );
        } else {
            // A search was performed, get the right page from the result
            $items = $items->forPage(
                $paginationMetadata['current_page'],
                $paginationMetadata['per_page'],
            );
        }

        return [
            'data' => $items->toArray(),
            'meta' => $paginationMetadata,
        ];
    }

    /**
     * Determines the pagination metadata for a query.
     *
     * @param int $page
     * @param int $total
     *
     * @return array
     */
    protected function determinePaginationMetadata(int $page, int $total)
    {
        // Resolve pagination data
        $lastPage = 1 + ($total - ($total % $this->perPage)) / $this->perPage;
        // Get the current requested page and validate it
        $page = $page < 1 ? 1 : $page;
        $page = $page > $lastPage ? $lastPage : $page;


        return [
            'current_page' => $page,
            'from' => 1 + $this->perPage * ($page - 1),
            'last_page' => $lastPage,
            'per_page' => $this->perPage,
            'to' => $page * $this->perPage,
            'total' => $total,
        ];
    }

    /**
     * Based on the value of includesString, it will return an array that informs
     * eloquent what values we want to eager load.
     *
     * @param  string  $includeString
     * @return array
     */
    protected function resolveEagerLoadQuery(string $includeString) : array
    {
        $eagerLoad = [];
        $includesItems = array_unique(explode(',', $includeString));
        foreach ($includesItems as $includesItem) {
            $includes = strtolower($includesItem);
            if (array_key_exists($includes, $this->eagerLoadOptions)) {
                $eagerLoad = $this->addIncludesToEagers(
                    $eagerLoad, $this->eagerLoadOptions[$includes]
                );
            }
        }

        return $eagerLoad;
    }

    /**
     * Adds new criteria (when $newItem is valid) to the eager loads array that's
     * then used for eloquent queries.
     *
     * @param array $currentEagers
     * @param array|string $newItem
     *
     * @return array
     */
    private function addIncludesToEagers(array $currentEagers, $newItem)
    {
        if (gettype($newItem) === 'array') {
            foreach ($newItem as $item) {
                $currentEagers[] = $item;
            }
        }
        if (gettype($newItem) === 'string') {
            $currentEagers[] = $newItem;
        }

        return $currentEagers;
    }
}
