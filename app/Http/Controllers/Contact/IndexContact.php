<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class IndexContact extends ResourceAbstractClass
{
    /**
     * Create a new single action controller instance.
     *
     * @param Contact $contact
     */
    public function __construct(Contact $contact) {
        $this->model = $contact;
        $this->eagerLoadOptions = [
            'company' => 'company',
            'address' => [
                'address.country',
                'address.state',
                'address.city',
                'address.postalcode',
            ],
        ];
        $this->perPage = 15;
    }

    /**
     * Get a paginated listing of the resource.
     *
     * @return JsonResponse
     */
    public function __invoke() : JsonResponse
    {
        return response()->json($this->indexAction(request()));
    }

    /**
     * Takes a search parameter that can be parametrized by specifying the desired
     * search fields in a comma separated list constrained between brackets and
     * after the search string ('Search string[parameter1,parameter2]'). The method
     * will then use the $eagers definition to make sure that the query is returned
     * with the correctly eager loaded relationships.
     *
     * @param array $eagers
     * @param string $search
     *
     * @return Collection
     */
    protected function searchQuery(array $eagers, string $search) : Collection
    {
        // Check for search params
        $paramsConf = $this->resolveSearchParams($search);
        $searchStr = $paramsConf['search'];
        $params = $paramsConf['params'];
        // Create the query and return the results
        $query = $this->model->with($eagers);
        $whereConstraintUsed = false;
        foreach ($params as $param) {
            if ($whereConstraintUsed) {
                $query->orWhere($param, 'LIKE', '%' . $searchStr . '%');
            } else {
                $whereConstraintUsed = true;
                $query->where($param, 'LIKE', '%' . $searchStr . '%');
            }
        }

        return $query->get();
    }

    /**
     * Gets a search string and evaluates what fields the search should be applied
     * to.
     * Valid fields: 'first_name', 'last_name', 'email'
     * Options
     *  * $search = 'Search string': In this case, 'Search string' will be searched
     *              in all valid fields.
     *  * $search = 'Search string['email']: In this case, 'Search string' will be
     *              searched in the 'email' field.
     *  * $search = 'Search string[first_name,last_name]': In this case 'Search string'
     *              will be searched in both the 'first_name' field and the
     *              'last_name' field.
     *
     * @param string $search
     *
     * @return array
     */
    private function resolveSearchParams($search) : array
    {
        $validParams = ['first_name', 'last_name', 'email'];
        $paramsStart = strpos($search, '[');
        $paramsEnd = strpos($search, ']');
        $searchStr = $search;
        $params = [];
        if ($paramsStart !== false && $paramsEnd !== false && $paramsStart < $paramsEnd) {
            $searchStr = substr($search, 0, $paramsStart);
            $paramsStart++;
            $params = array_reduce(
                explode(',', substr($search, $paramsStart, $paramsEnd - $paramsStart)),
                function ($resolvedParams, $proposedParam) use ($validParams) {
                    if (in_array($proposedParam, $validParams)) {
                        $resolvedParams[] = $proposedParam;
                    }

                    return $resolvedParams;
                },
                []
            );
        }
        $params = count($params) === 0 ? $validParams : $params;

        return [
            'search' => $searchStr,
            'params' => $params
        ];
    }
}
