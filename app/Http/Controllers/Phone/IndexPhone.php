<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Phone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class IndexPhone extends ResourceAbstractClass
{
    /**
     * Create a new single action controller instance.
     *
     * @param Phone $phone
     */
    public function __construct(Phone $phone) {
        $this->model = $phone;
        $this->eagerLoadOptions = [
            'contacts' => [
                'contacts.phones',
                'contacts.photo',
                'contacts.company',
                'contacts.address.postalCode',
                'contacts.address.city',
                'contacts.address.state',
                'contacts.address.country',
            ],
            'location' => 'location',
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
            ->where('number', 'LIKE', '%' . $search . '%')
            ->get();
    }
}
