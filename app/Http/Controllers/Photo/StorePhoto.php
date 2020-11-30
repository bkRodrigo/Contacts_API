<?php

namespace App\Http\Controllers\Photo;

use App\Http\Controllers\ResourceAbstractClass;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class StorePhoto extends ResourceAbstractClass
{
    /**
     * Create a new single action controller instance.
     *
     * @param Photo $photo
     */
    public function __construct(Photo $photo) {
        $this->model = $photo;
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $path = Storage::disk('public')->putFile('images', $request->file('photo'));
        $photo = new $this->model();
        $photo->name = $path;

        $photo->save();

        return response()->json([
            'photo' => $photo,
            'message' => 'Successfully stored image',
        ]);
    }
}
