<?php

namespace App\Http\Controllers\Address;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class IndexAddress extends ResourceAbstractClass
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
     * Get a paginated listing of the resource.
     *
     * @param array $eagers
     * @param string $search
     *
     * @return Collection
     */
    protected function searchQuery(array $eagers, string $search) : Collection
    {
        return $this->model->with($eagers)
            ->where('street_address', 'LIKE', '%' . $search . '%')
            ->get();
    }
}
