<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    /**
     * The companies model.
     *
     * @var Company
     */
    protected $company;

    /**
     * Create a new controller instance.
     *
     * @param Company $company
     */
    public function __construct(Company $company) {
        $this->company = $company;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        // Check for eager loading
        $eagerLoad = $this->resolveEagerLoadQuery(request()->input('include', ''));
        // Check for search criteria in the parameters
        $search = request()->input('search', '');
        $companies = null;
        if (strlen($search) > 3) {
            $companies = $this->company->with($eagerLoad)
                ->where('name', 'LIKE', '%' . $search . '%')
                ->get();
        }
        if (strlen($search) > 0 && strlen($search) <= 3) {
            $companies = collect([]);
        }
        // Resolve pagination data
        $companyCount = is_null($companies) ?
            $this->company->all()->count() : $companies->count();
        $perPage = 15;
        $lastPage = 1 + ($companyCount - ($companyCount % $perPage)) / $perPage;
        // Get the current requested page and validate it
        $page = intval(request()->input('page', 1));
        $page = $page < 1 ? 1 : $page;
        $page = $page > $lastPage ? $lastPage : $page;
        // If no search was performed, then fetch the data
        if (is_null($companies)) {
            $companies = $this->company->with($eagerLoad)
                ->get()->forPage($page, $perPage);
        } else {
            $companies->forPage($page, $perPage);
        }

        return response()->json([
            'data' => $companies->toArray(),
            'meta' => [
                'current_page' => $page,
                'from' => 1 + $perPage * ($page - 1),
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'to' => $page * $perPage,
                'total' => $companyCount,
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
            $this->company->with($eagerLoad)->where('id', $id)->first()
        );
    }

    /**
     * Search for a company by its name.
     *
     * @param  string  $name
     * @return JsonResponse
     */
    public function find(string $name)
    {
        $companies = [];
        if (strlen($name) > 3) {
            $companies = $this->company->where('name', 'LIKE', '%' . $name . '%')
                ->take(10)->get();
        }

        return response()->json($companies);
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
            switch (strtolower($includesItem)) {
                case 'contacts':
                    $eagerLoad[] = 'contacts';
                    break;
            }
        }

        return $eagerLoad;
    }
}
