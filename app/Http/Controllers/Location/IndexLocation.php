<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

class IndexLocation extends ResourceAbstractClass
{
    /**
     * Create a new single action controller instance.
     *
     * @param Location $location
     */
    public function __construct(Location $location) {
        $this->model = $location;
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
}
