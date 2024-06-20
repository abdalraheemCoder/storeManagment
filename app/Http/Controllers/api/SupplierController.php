<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\supplier;
use Illuminate\Support\Facades\Validator;
use App\Models\Account;
use Illuminate\Routing\Controller as RoutingController;

class SupplierController extends RoutingController
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplier = supplier::get();
        return $this->apiresponse($supplier,'This all Suppliers ',200);
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
        'supplier_name' => 'required|string|max:255|unique:suppliers',
        'supplier_phone' => 'nullable|integer',
        'supplier_company' => 'nullable|string',
        'note' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return $this->apiresponse(null, $validator->errors(), 400);
    }

    $account = Account::create([
        'account_name' => $request->supplier_name,
    ]);

    if (!$account) {
        return $this->apiresponse(null, 'Failed to create account', 400);
    }

    $supplier = Supplier::create([
        'supplier_name' => $request->supplier_name,
        'supplier_phone' => $request->supplier_phone,
        'supplier_company' => $request->supplier_company,
        'acc_supplier_id' => $account->id,
        'note' => $request->note,
    ]);

    if ($supplier) {
        return $this->apiresponse($supplier, 'Supplier saved successfully', 201);
    } else {

        $account->delete();
        return $this->apiresponse(null, 'Failed to save supplier', 400);
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // if ($id=null) {
        //     return $this->apiresponse(null,'This id Not found ',401);
        // }

        $supplier = supplier::find($id);

        if ($supplier) {
          return $this->apiresponse($supplier,'This your Suppliers ',200);
        }
        return $this->apiresponse(null,'This Supplier Not found ',401);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::find($id);

    if (!$supplier) {
        return $this->apiresponse(null, 'Supplier not found', 404);
    }

    $validator = Validator::make($request->all(), [
        'supplier_name' => 'sometimes|required|string|max:255|unique:suppliers,supplier_name,' . $id,
        'supplier_phone' => 'sometimes|nullable|integer',
        'supplier_company' => 'sometimes|nullable|string',
        'note' => 'sometimes|nullable|string',
    ]);

    if ($validator->fails()) {
        return $this->apiresponse(null, $validator->errors(), 400);
    }

    if ($request->has('supplier_name')) {
        $supplier->supplier_name = $request->supplier_name;

        $account = Account::find($supplier->acc_supplier_id);
        if ($account) {
            $account->account_name = $request->supplier_name;
            $account->save();
        }
    }

    $supplier->fill($request->only([
        'supplier_phone',
        'supplier_company',
        'note',
    ]));

    $supplier->save();

    return $this->apiresponse($supplier, 'Supplier updated successfully', 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = supplier::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$supplier) {
            return $this->apiresponse(null,'This supplier Not found to deleted ',401);
        }

        $supplier->delete($id);

        if ($supplier) {
            return $this->apiresponse($supplier,'This supplier is deleted ',200);
        }
    }
}
