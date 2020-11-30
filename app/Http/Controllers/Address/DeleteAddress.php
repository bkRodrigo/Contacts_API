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
            $contacts = $address->contacts;
            foreach ($contacts as $contact) {
                $contact->address()->dissociate($address);
                $contact->save();
            }
            $address->postalCode()->dissociate();
            $address->city()->dissociate();
            $address->state()->dissociate();
            $address->country()->dissociate();
            $address->save();
            $address->delete();
        }

        return response()->json([
            'message' => 'The address was successfully deleted',
        ]);
    }
}
