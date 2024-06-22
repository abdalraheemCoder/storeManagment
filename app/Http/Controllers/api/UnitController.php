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
          $validator = Validator::make($request->all(), [
            'unit_name' => 'required|string',
            'unit_equal' => 'required|numeric|min:0.000001',
            'unit_mat_id' => 'nullable|exists:materials,id',
            'Quantity' => 'nullable|integer|min:0',
            'Quan_return' => 'nullable|integer|min:0',
            'unitSalse_price' => 'nullable|numeric|min:0',
            'unitbuy_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        $materialId = $request->unit_mat_id ? $request->unit_mat_id : Material::latest()->first()->id;

        if ($request->unit_mat_id && !Material::find($request->unit_mat_id)) {
            return $this->apiresponse(null, 'Invalid material', 400);
        }

        $unitCount = Unit::where('unit_mat_id', $materialId)->count();
        $unitEqual = $unitCount == 0 ? 1 : $request->unit_equal;
        $unit = Unit::create([
            'unit_name' => $request->unit_name,
            'unit_equal' => $unitEqual,
            'unit_mat_id' => $materialId,
            'Quantity' => $request->Quantity ?? 0,
            'Quan_return' => $request->Quan_return ?? 0,
            'unitSalse_price' => $request->unitSalse_price ?? 0,
            'unitbuy_price' => $request->unitbuy_price ?? 0,
        ]);

        return $this->apiresponse($unit, 'This unit  saved', 400);
    }




    public function update(Request $request, string $id)
    {
        $unit = Unit::find($id);

        if (!$unit) {
            return $this->apiresponse(null, 'This unit not found to update', 404);
        }

        $firstUnit = Unit::where('unit_mat_id', $unit->unit_mat_id)->orderBy('created_at')->first();

        if ($firstUnit && $firstUnit->id == $unit->id) {
            $request->merge(['unit_equal' => $unit->unit_equal]);
        }

        $validator = Validator::make($request->all(), [
            'unit_name' => 'sometimes|required|string|max:255|unique:units,unit_name,' . $id,
            'unit_equal' => 'sometimes|required|integer',
            'Quantity' => 'sometimes|required|integer',
            'Quan_return' => 'sometimes|required|integer',
            'unitSalse_price' => 'sometimes|required|numeric|min:0',
            'unitbuy_price' => 'sometimes|required|numeric|min:0',
            'unit_mat_id' => 'sometimes|required|exists:materials,id',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        $unit->fill($request->only([
            'unit_name',
            'unit_equal',
            'Quantity',
            'Quan_return',
            'unitSalse_price',
            'unitbuy_price',
            'unit_mat_id',
        ]));

        $unit->save();
        return $this->apiresponse($unit, 'This unit is updated', 200);

    }
}
