<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\material;
use App\Models\unit;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;

class MaterialController extends RoutingController
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $material = material::get();
        return $this->apiresponse($material,'This all Materials ',200);
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
            'material_name' => 'required|string|max:255|unique:materials,material_name,' ,
            'discount_mat' => 'nullable|numeric|min:0|max:100',
            'note' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        $material = Material::create($request->all());

        if ($material) {
            return $this->apiresponse($material, 'This material is saved', 201);
        }

        return $this->apiresponse(null, 'This material is not saved', 400);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // if ($id=null) {
        //     return $this->apiresponse(null,'This id Not found ',401);
        // }
        $material = material::find($id);

        if ( $material) {
          return $this->apiresponse($material,'This your Materials ',200);
        }
        return $this->apiresponse(null,'This Material Not found ',401);



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
        $material = Material::find($id);

        if (!$material) {
            return $this->apiresponse(null, 'This material not found to update', 404);
        }

        $validator = Validator::make($request->all(), [
            'material_name' => 'sometimes|required|string|max:255|unique:materials,material_name,' . $id,
            'discount_mat' => 'sometimes|nullable|numeric|min:0|max:100',
            'note' => 'sometimes|nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        $material->fill($request->only([
            'material_name',
            'discount_mat',
            'note',
            'category_id',
        ]));

        $material->save();

        return $this->apiresponse($material, 'This material is updated', 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $material = material::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$material) {
            return $this->apiresponse(null,'This material Not found to deleted ',401);
        }

        $material->delete($id);

        if ($material) {
            return $this->apiresponse($material,'This material is deleted ',200);
        }
    }
}
