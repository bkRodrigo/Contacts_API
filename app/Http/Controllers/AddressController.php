<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\PostalCode;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * The addresses model.
     *
     * @var Address
     */
    protected $address;

    /**
     * The postal codes model.
     *
     * @var PostalCode
     */
    protected $postalCode;

    /**
     * The cities model.
     *
     * @var City
     */
    protected $city;

    /**
     * The states model.
     *
     * @var State
     */
    protected $state;

    /**
     * The countries model.
     *
     * @var Country
     */
    protected $country;

    /**
     * Create a new controller instance.
     *
     * @param Address $address
     * @param PostalCode $postalCode
     * @param City $city
     * @param State $state
     * @param Country $country
     */
    public function __construct(
        Address $address,
        PostalCode $postalCode,
        City $city,
        State $state,
        Country $country
    ) {
        $this->address = $address;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        // Resolve pagination data
        $addressCount = $this->address->all()->count();
        $perPage = 15;
        $lastPage = 1 + ($addressCount - ($addressCount % $perPage)) / $perPage;
        // Analyze Parameters
        $page = intval(request()->input('page', 1));
        $page = $page < 1 ? 1 : $page;
        $page = $page > $lastPage ? $lastPage : $page;
        $eagerLoad = $this->resolveEagerLoadQuery(request()->input('include', ''));
        // Fetch the data
        $addresses = $this->address->with($eagerLoad)->get()->forPage($page, $perPage);

        return response()->json([
            'data' => $addresses->toArray(),
            'meta' => [
                'current_page' => $page,
                'from' => 1 + $perPage * ($page - 1),
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'to' => $page * $perPage,
                'total' => $addressCount,
            ],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        // Analyze eager loading params
        $eagerLoad = $this->resolveEagerLoadQuery(request()->input('include', ''));

        return response()->json(
            $this->address->with($eagerLoad)->where('id', $id)->first()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'street_address' => 'required|string|max:300',
            'description' => 'string|max:300',
            'latitude' => 'numeric|between:-91,91',
            'longitude' => 'numeric|between:-181,181',
            'postal_code' => 'string|max:50',
            'city' => 'string|max:150',
            'state' => 'string|max:150',
            'country' => 'string|max:150',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // Create the new address
        $newAddress = new $this->address();
        $newAddress->street_address = $request->input('street_address');
        $newAddress->description = $request->input('description', '');
        $newAddress->latitude = $request->input('latitude', null);
        $newAddress->longitude = $request->input('longitude', null);
        // Attach associations to the address (where available)
        $newAddress = $this->associateRelation(
            $newAddress,
            ['code' => $request->input('postal_code', null)],
            $this->postalCode
        );
        $newAddress = $this->associateRelation(
            $newAddress,
            ['name' => $request->input('city', null)],
            $this->city
        );
        $newAddress = $this->associateRelation(
            $newAddress,
            ['name' => $request->input('state', null)],
            $this->state
        );
        $newAddress = $this->associateRelation(
            $newAddress,
            ['name' => $request->input('country', null)],
            $this->country
        );
        // Store the new address
        $newAddress->save();

        return response()->json([
            'address' => $newAddress,
            'message' => 'The address was successfully created',
        ]);
    }

    /**
     * Based on the value of includesString, it will return an array that informs
     * eloquent what values we want to eager load.
     *
     * @param  string  $includeString
     * @return array
     */
    private function resolveEagerLoadQuery(string $includeString)
    {
        $eagerLoad = [];
        $includesItems = array_unique(explode(',', $includeString));
        foreach ($includesItems as $includesItem) {
            switch (strtolower($includesItem)) {
                case 'postalcode':
                    $eagerLoad[] = 'postalCode';
                    break;
                case 'city':
                    $eagerLoad[] = 'city';
                    break;
                case 'state':
                    $eagerLoad[] = 'state';
                    break;
                case 'country':
                    $eagerLoad[] = 'country';
                    break;
            }
        }

        return $eagerLoad;
    }

    /**
     * It takes a relation's data and model, determines if it can be fetched or
     * generated and then associates it to the address when it's successfully
     * resolved (if not resolved, the address remains unchanged).
     *
     * @param Address $address the address to manipulate
     * @param array $relData input data
     * @param mixed $relModel the input's proposed model
     *
     * @return Address
     */
    private function associateRelation(Address $address, array $relData, $relModel)
    {
        $modelInstance = $this->createRelatedInstance($relData, $relModel);
        if (is_null($modelInstance)) {
            return $address;
        }
        $modelClassParts = explode('\\', get_class($modelInstance));
        $relation = lcfirst($modelClassParts[count($modelClassParts) - 1]);
        $address->{$relation}()->associate($modelInstance);

        return $address;
    }

    /**
     * Based on $data, fetch or create an instance of $model; if the $data
     * references a null value, then nothing is done (and null is returned).
     *
     * @param array $data the data that instantiates or is used to fetch a model
     * @param mixed $model reference to the model we're searching or creating
     * @return mixed returns an instance of the model or null
     */
    private function createRelatedInstance(array $data, $model)
    {
        if (count($data) === 0 || is_null($data[array_keys($data)[0]])) {
            return null;
        }

        return $model->firstOrCreate($data);
    }
}
