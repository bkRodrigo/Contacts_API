<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\JsonResponse;

class ContactsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @var Contact
     */
    protected $contact;

    /**
     * Create a new controller instance.
     *
     * @param Contact $contact
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        // Resolve pagination data
        $contactsCount = $this->contact->all()->count();
        $perPage = 15;
        $lastPage = 1 + ($contactsCount - ($contactsCount % $perPage)) / $perPage;
        // Analyze Parameters
        $page = intval(request()->input('page', 1));
        $page = $page < 1 ? 1 : $page;
        $page = $page > $lastPage ? $lastPage : $page;
        $eagerLoad = $this->resolveEagerLoadQuery(request()->input('include', ''));
        // Fetch the data
        $contacts = $this->contact->with($eagerLoad)->get()->forPage($page, $perPage);

        return response()->json([
            'data' => $contacts->toArray(),
            'meta' => [
                'current_page' => $page,
                'from' => 1 + $perPage * ($page - 1),
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'to' => $page * $perPage,
                'total' => $contactsCount,
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
            $this->contact->with($eagerLoad)->where('id', $id)->first()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store()
    {

    }

    /**
     * Store an avatar image and associate it to the contact.
     *
     * @return JsonResponse
     */
    public function storeImage()
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        //
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
            switch ($includesItem) {
                case 'company':
                    $eagerLoad[] = 'company';
                    break;
                case 'address':
                    $eagerLoad[] = 'address.country';
                    $eagerLoad[] = 'address.state';
                    $eagerLoad[] = 'address.city';
                    $eagerLoad[] = 'address.postalcode';
                    break;
            }
        }

        return $eagerLoad;
    }
}
