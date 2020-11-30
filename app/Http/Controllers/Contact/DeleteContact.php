<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;

class DeleteContact extends ResourceAbstractClass
{
    /**
     * Create a new single action controller instance.
     *
     * @param Contact $contact
     */
    public function __construct(Contact $contact) {
        $this->model = $contact;
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
        $contact = $this->model->where('id', $id)->first();
        if (!is_null($contact)) {
            $contact->phones()->detach();
            $contact->photo()->dissociate();
            $contact->company()->dissociate();
            $contact->address()->dissociate();
            $contact->save();
            $contact->delete();
        }

        return response()->json([
            'message' => 'The address was successfully deleted',
        ]);
    }
}
