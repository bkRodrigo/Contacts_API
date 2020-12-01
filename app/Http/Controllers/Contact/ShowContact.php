<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;

class ShowContact extends ResourceAbstractClass
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
            'phones' => 'phones.location',
            'photo' => 'photo',
            'address' => [
                'address.country',
                'address.state',
                'address.city',
                'address.postalcode',
            ],
        ];
    }

    /**
     * Get a paginated listing of the resource.
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
