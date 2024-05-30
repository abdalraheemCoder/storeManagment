<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\material;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\unit;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;

class UnitController extends RoutingController
{
    use ApiResponseTrait;

    public function index()
    {
        $unit = unit::get();
        return $this->apiresponse($unit,'This all unit ',200);
    }

    public function store(Request $request)
    {
        $validator=Validator::make ($request->all(),[
             'unit_name'=>'required',
             'unit_equal'=>'required',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }
        
        $materialId = $request->unit_mat_id ? $request->unit_mat_id : Material::latest()->first()->id;
        $totalMaterials = Material::count();
        if ($request->unit_mat_id && $request->unit_mat_id > $totalMaterials) {
            return $this->apiresponse(null, 'Invalid material ', 400);
        }
        $unit = Unit::create([
            'unit_name' => $request->unit_name,
            'unit_equal' => $request->unit_equal,
            'unit_mat_id' => $materialId
        ]);

        if ($unit) {
            return $this->apiresponse($unit,'This unit is Save ',201);
        }
        return $this->apiresponse(null,'This unit Not Save ',400);
    }


    public function show(string $id)
    {
        $unit = unit::find($id);
        if (!$id) {
          return $this->apiresponse(null,'This id Not found ',401);
        }
        if ($unit) {
          return $this->apiresponse($unit,'This your unit ',200);
        }
        return $this->apiresponse(null,'This unit Not found ',401);

    }




    public function update(Request $request, string $id)
    {
        $validator=Validator::make ($request->all(),[
            'unit_name'=>'required',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        if($materialId = $request->unit_mat_id){
            $materialId = $request->unit_mat_id;
            $material = Material::find($materialId);

            if (!$material) {
                return response()->json([
                    'error' => 'Material not found'
                ], 404);
            }else{
            $unit = Unit::create([
                'unit_name' => $request->unit_name,
                'unit_equal' => $request->unit_equal,
                'unit_mat_id' => $material->id
            ]);
        }
        }
        $unit = unit::find($id);
        if (!$id) {
          return $this->apiresponse(null,'This id Not found  ',401);
        }

        if ( !$unit) {
            return $this->apiresponse(null,' This unit Not found to updated ',401);
         }



         $unit->update($request->all());

        if ($unit) {
            return $this->apiresponse($unit,'This unit is Update',201);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $unit = unit::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$unit) {
            return $this->apiresponse(null,'This unit Not found to deleted ',401);
        }

        $unit->delete($id);

        if ($unit) {
            return $this->apiresponse($unit,'This unit is deleted ',200);
        }
    }
}
