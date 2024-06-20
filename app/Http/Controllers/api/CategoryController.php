<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\category;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;


class CategoryController extends RoutingController
{

    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = category::get();
        return $this->apiresponse($category,'This all Custmer ',200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        $category = Category::create($request->all());

        if ($category) {
            return $this->apiresponse($category, 'This category is saved', 201);
        }

        return $this->apiresponse(null, 'This category is not saved', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        // if ($id=null) {
        //     return $this->apiresponse(null,'This id Not found ',401);
        // }

         $category = category::find($id);
         if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }
        if ($category) {
         return $this->apiresponse($category,'This your category ',200);
        }

        return $this->apiresponse(null,'This Category Not found ',401);


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->apiresponse(null, 'This category not found to update', 404);
        }

        if ($request->filled('category_name')) {
            $validator = Validator::make($request->all(), [
                'category_name' => 'required|string|max:255|unique:categories,category_name,' . $id,
                'note' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->apiresponse(null, $validator->errors(), 400);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'note' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->apiresponse(null, $validator->errors(), 400);
            }
        }
        $category->update($request->all());

        return $this->apiresponse($category, 'This category is updated', 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = category::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$category) {
            return $this->apiresponse(null,'This category Not found to deleted ',401);
        }

        $category->delete($id);

        if ($category) {
            return $this->apiresponse($category,'This category is deleted ',200);

        }
    }
}
