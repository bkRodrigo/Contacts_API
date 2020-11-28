<?php

namespace App\Http\Controllers\Address;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Address;
use Illuminate\Http\JsonResponse;

class ShowAddress extends ResourceAbstractClass
{
    /**
     * Create a new single action controller instance.
     *
     * @param Address $address
     */
    public function __construct(Address $address) {
        $this->model = $address;
        $this->eagerLoadOptions = [
            'postalcode' => 'postalCode',
            'city' => 'city',
            'state' => 'state',
            'country' => 'country',
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function __invoke(int $id) : JsonResponse
    {
        // Analyze eager loading params
        $eagerLoad = $this->resolveEagerLoadQuery(request()->input('include', ''));

        return response()->json(
            $this->model->with($eagerLoad)->where('id', $id)->first()
        );
    }
}
