<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class ShowCompany extends ResourceAbstractClass
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
        // Analyze eager loading params
        $eagerLoad = $this->resolveEagerLoadQuery(request()->input('include', ''));

        return response()->json(
            $this->model->with($eagerLoad)->where('id', $id)->first()
        );
    }
}
