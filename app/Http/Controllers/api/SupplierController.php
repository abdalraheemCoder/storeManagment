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

        $validator=Validator::make ($request->all(),[
            'supplier_name'=>'required',
            //'supplier_email'=>'unique'
        ]);
        $account =Account::Create([
            'account_name' => $request->supplier_name,
            //'account_type' =>'1'
        ]);


        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        //$supplier = supplier::create($request->all());
        $supplier = supplier::create([
            'supplier_name'=>$request->supplier_name,
            'acc_supplier_id'=>$account->id
        ]);
        if ($supplier) {
            return $this->apiresponse($supplier,'This suppliers is Save ',201);
        }

        if ($account) {
            return $this->apiresponse($account,'This customers is Save ',201);
        }
        return $this->apiresponse(null,'This suppliers Not Save ',400);
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
        $validator=Validator::make ($request->all(),[
            'supplier_name'=>'required',
            'supplier_email'=>'unique'
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        $supplier = supplier::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if (!$supplier) {
            return $this->apiresponse(null,'This supplier Not found to updated ',401);
         }

        $supplier->update($request->all());

        if ($supplier) {
            return $this->apiresponse($supplier,'This supplier is update ',201);

        }

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
