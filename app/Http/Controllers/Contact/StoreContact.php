<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class StoreContact extends ResourceAbstractClass
{
    use ContactTrait;

    /**
     * The default avatar's file name.
     *
     * @var string
     */
    protected $defaultAvatar;

    /**
     * Create a new single action controller instance.
     *
     * @param Contact $contact
     */
    public function __construct(Contact $contact)
    {
        $this->defaultAvatar = 'b80b22d6-32f6-11eb-adc1-0242ac120002.png';
        $this->model = $contact;
    }

    /**
     * Create a new resource based on the request data.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:75',
            'last_name' => 'required|string|max:150',
            'email' => 'email',
            'birthday' => 'date_format:Y-m-d',
            'company' => 'string|max:150',
            'phones' => 'array',
            'address_id' => 'exists:addresses,id',
            'photo_id' => 'exists:photos,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // Create the new contact
        return $this->updateContact(
            null, $request, 'The contact was successfully created',
        );
    }
}
