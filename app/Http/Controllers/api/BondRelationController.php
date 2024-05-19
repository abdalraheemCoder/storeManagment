<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Traits\ApiResponseTrait;
//use App\Http\Controllers\Controller;
use App\Models\Account;
//use Illuminate\Http\Request;
use App\Models\Bond;
use App\Models\BondRelation;
use App\Models\buyBill;
use App\Models\customer;
use App\Models\salseBill;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;


class BondRelationController extends RoutingController
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        $Bonds = BondRelation::get();
        return $this->apiresponse($Bonds,'This all Bonds ',200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {

        $validator=Validator::make($request->all(),[
            //'account_id'=>'required'

        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }
        $type = $request->input('type');
        if ($type == "salse") {
            
            $salse_bill = salseBill::find($id);
            if ($salse_bill) {
                $customerId = SalseBill::where('id', $id)->select('customer_id')->first()->customer_id;
                if ($customerId) {
                    $customerAccountId = customer::where('id', $customerId)->select('acc_client_id')->first();
                }
                $bond =Bond::Create([
                    'account_id' => $customerAccountId->acc_client_id,
                    'value'=>$request->value,
                    'bond_type' =>'0',
                ]);
                $BondRelation = BondRelation::create([
                    'value'=>$request->value,
                    'bond_id'=>$bond->id,
                    'salse_bill_id'=>$salse_bill->id,
                    'customer_id'=>$customerAccountId->acc_client_id
                ]);

        }
    }

    elseif ($type === 'purchase') {
            $buy_bills = buyBill::find($id);
            $supplierId = buyBill::where('id', $id)->select('supplier_id')->first()->supplier_id;
            if ($supplierId) {
                $supplierAccountId = customer::where('id', $supplierId)->select('acc_supplier_id')->first();
            }
            $bond =Bond::Create([
                'account_id' => $supplierAccountId->acc_supplier_id,
                'value'=>$request->value,
                'bond_type' =>'1',
            ]);
            $BondRelation = BondRelation::create([
                'value'=>$request->value,
                'bond_id'=>$bond->id,
                'buy_bill_id'=>$buy_bills->id,
                'supplier_id'=>$supplierAccountId->acc_supplier_id
            ]);

        }


        //$Bonds = Bond::create($request->all());



        if ($BondRelation) {
            return $this->apiresponse($BondRelation,'This Bonds is Save ',201);

        }
        return $this->apiresponse(null,'This Bonds Not Save ',400);
    }



    /**
     * Display the specified resource.
     */



    public function show(string $id)
    {


         $Bonds = BondRelation::find($id);

         if (!$id) {
             return $this->apiresponse(null,'This id Not found ',401);
        }

         if ($Bonds) {
            return $this->apiresponse($Bonds,'This your Bonds ',200);
         }

         return $this->apiresponse(null,'This Bonds Not found ',401);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator=Validator::make ($request->all(),[
            //'account_id'=>'required'

        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }


        $Bonds = BondRelation::find($id);

        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$Bonds) {
            return $this->apiresponse(null,'This Bonds Not found to updated ',401);
         }

        $Bonds->update($request->all());

        if ($Bonds) {
            return $this->apiresponse($Bonds,'This Bonds is update ',201);

        }

    }



    /**
     * Remove the specified resource from storage.
     */






    public function destroy(string $id)
    {
        $Bonds = BondRelation::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$Bonds) {
            return $this->apiresponse(null,'This Bonds Not found to deleted ',401);
        }

        $Bonds->delete($id);

        if ($Bonds) {
            return $this->apiresponse($Bonds,'This Bonds is deleted ',200);
        }
    }
}
