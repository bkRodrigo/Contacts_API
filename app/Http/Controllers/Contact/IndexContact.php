<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Address;
use App\Models\City;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class IndexContact extends ResourceAbstractClass
{
    /**
     * The Address model.
     *
     * @var Address
     */
    protected $address;

    /**
     * Create a new single action controller instance.
     *
     * @param Contact $contact
     * @param Address $address
     */
    public function __construct(Contact $contact, Address $address) {
        $this->model = $contact;
        $this->address = $address;
        $this->eagerLoadOptions = [
            'company' => 'company',
            'phones' => 'phones.location',
            'photo' => 'photo',
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
        $idsFromRelationships = [];
        if (count($params['special']) > 0) {
            $idsFromRelationships = $this->getByJoinedResults(
                $params['special'], $searchStr,
            );
        }
        $query = $this->model->with($eagers);
        $whereConstraintUsed = false;
        if (count($idsFromRelationships) > 0) {
            $query->whereIn('id', $idsFromRelationships);
            $whereConstraintUsed = true;
        }
        foreach ($params['normal'] as $param) {
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
     * This method takes an array of relationships that be composed by the following
     * list of possible items: 'phones', 'city', 'state'. We will then get all of
     * the contacts that have a relationship whose name contains $searchStr.
     *
     * @param array $params
     * @param string $searchStr
     *
     * @return array
     */
    protected function getByJoinedResults(array $params, string $searchStr) : array
    {
        $queryString = 'SELECT c.id ';
        $queryString .= 'FROM contacts AS c ';
        $queryString .= 'JOIN addresses AS a ON a.id=c.address_id ';
        $queryString .= 'JOIN cities AS ci ON ci.id=a.city_id ';
        $queryString .= 'JOIN states AS s ON s.id=a.state_id ';
        $queryString .= 'JOIN contact_phone AS cp ON cp.contact_id=c.id ';
        $queryString .= 'JOIN phones AS p ON p.id=cp.phone_id ';
        $queryString .= 'WHERE ';
        $whereAdded = false;
        if (in_array('state', $params)) {
            $whereAdded = true;
            $queryString .= 's.name LIKE \'%' . $searchStr . '%\' ';
        }
        if (in_array('city', $params)) {
            $queryString .= $whereAdded ? 'OR ' : '';
            $queryString .= 'ci.name LIKE \'%' . $searchStr . '%\' ';
        }
        if (in_array('phones', $params)) {
            $queryString .= $whereAdded ? 'OR ' : '';
            $queryString .= 'p.number LIKE \'%' . $searchStr . '%\' ';
        }
        $queryString = trim($queryString);
        $queryResults = DB::select($queryString);
        $contactIds = [];
        foreach ($queryResults as $queryResult) {
            $contactIds[] = $queryResult->id;
        }

        return $contactIds;
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
    private function resolveSearchParams(string $search) : array
    {
        $validParams = [
            'normal' => ['first_name', 'last_name', 'email'],
            'special' => ['state', 'city', 'phones'],
        ];
        $paramsStart = strpos($search, '[');
        $paramsEnd = strpos($search, ']');
        $searchStr = $search;
        $params = [
            'normal' => [],
            'special' => [],
        ];
        if ($paramsStart !== false && $paramsEnd !== false && $paramsStart < $paramsEnd) {
            $searchStr = substr($search, 0, $paramsStart);
            $paramsStart++;
            $params = array_reduce(
                explode(',', substr($search, $paramsStart, $paramsEnd - $paramsStart)),
                function ($resolvedParams, $proposedParam) use ($validParams) {
                    if (in_array($proposedParam, $validParams['normal'])) {
                        $resolvedParams['normal'][] = $proposedParam;
                    }
                    if (in_array($proposedParam, $validParams['special'])) {
                        $resolvedParams['special'][] = $proposedParam;
                    }

                    return $resolvedParams;
                },
                [
                    'normal' => [],
                    'special' => [],
                ]
            );
        }
        if (count($params['normal']) === 0 && count($params['special']) === 0) {
            $params = $validParams;
        }

        return [
            'search' => $searchStr,
            'params' => $params
        ];
    }
}
