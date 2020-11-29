<?php

namespace App\Http\Controllers\Address;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DeleteAddress extends ResourceAbstractClass
{
    /**
     * Create a new single action controller instance.
     *
     * @param Address $address
     */
    public function __construct(Address $address) {
        $this->model = $address;
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
        // Validate request
        $address = $this->model->where('id', $id)->first();
        if (!is_null($address)) {
            $address->delete();
        }

        return response()->json([
            'message' => 'The address was successfully deleted',
        ]);
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
