<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

class ShowLocation extends ResourceAbstractClass
{
    /**
     * Create a new single action controller instance.
     *
     * @param Location $location
     */
    public function __construct(Location $location) {
        $this->model = $location;
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
        return response()->json(
            $this->model->where('id', $id)->first()
        );
    }
}
