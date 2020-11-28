<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class IndexCompany extends ResourceAbstractClass
{
    /**
     * Create a new single action controller instance.
     *
     * @param Company $company
     */
    public function __construct(Company $company) {
        $this->model = $company;
        $this->eagerLoadOptions = [
            'contacts' => 'contacts',
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
}
