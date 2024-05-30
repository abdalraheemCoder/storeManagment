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
use App\Models\Bill;
use App\Models\supplier;
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
            'value'=>'required'

        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }
        if (Bill::typeOfbill_SALE) {
            $bill = Bill::find($id);
            if ($bill) {
                $customerId = Bill::where('id', $id)->select('customer_id')->first()->customer_id;
                if ($customerId) {
                    $customerAccountId = customer::where('id', $customerId)->select('acc_client_id')->first();
                }

                if (isset($customerAccountId)) {
                    $BondRelation = BondRelation::firstOrNew(
                        ['bill_id' => $bill->id, 'acc_id' => $customerAccountId->acc_client_id],
                        ['value' => 0]
                    );

                    $newBondRelationValue = $BondRelation->value + $request->value;
                    $totalBondValues = BondRelation::where('bill_id', $bill->id)->sum('value') + $request->value;

                    if ($newBondRelationValue > $bill->price || $totalBondValues > $bill->price) {
                        return $this->apiresponse(null, 'This BondRel Not Save', 400);
                    }

                    $BondRelation->value = $newBondRelationValue;
                    $BondRelation->save();

                    $bonds = new Bond([
                        'account_id' => $customerAccountId->acc_client_id,
                        'value' => $request->value,
                        'type' => 'receipt',
                        'bondRel_id' => $BondRelation->id
                    ]);
                    $bonds->save();
                    $account = Account::find($bonds->account_id);
                        if ($account) {

                            $account->account_DOWN = $account->account_DOWN + $bonds->value;
                            $account->save();

                            return $this->apiresponse($account, 'Account updated successfully', 200);
                        } else {

                            return $this->apiresponse(null, 'Account not found', 404);
                        }
                        if ($BondRelation) {
                            return $this->apiresponse($BondRelation,'This Bonds is Save ',201);

                        }
                        return $this->apiresponse(null,'This Bonds Not Save ',400);
                }
            }
        }

        if (Bill::typeOfbill_BUY) {
            $bill = Bill::find($id);
            if ($bill) {
                $supplierID = Bill::where('id', $id)->select('supplier_id')->first()->supplier_id;
                if ($supplierID) {
                    $supplierAccountId = supplier::where('id', $supplierID)->select('acc_supplier_id')->first();
                }

                if (isset($supplierAccountId)) {
                    $BondRelation = BondRelation::firstOrNew(
                        ['bill_id' => $bill->id, 'acc_id' => $supplierAccountId->acc_supplier_id],
                        ['value' => 0]
                    );

                    $newBondRelationValue = $BondRelation->value + $request->value;
                    $totalBondValues = BondRelation::where('bill_id', $bill->id)->sum('value') + $request->value;

                    if ($newBondRelationValue > $bill->price || $totalBondValues > $bill->price) {
                        return $this->apiresponse(null, 'This BondRel Not Save', 400);
                    }

                    $BondRelation->value = $newBondRelationValue;
                    $BondRelation->save();

                    $bonds = new Bond([
                        'account_id' => $supplierAccountId->acc_supplier_id,
                        'value' => $request->value,
                        'type' => 'payment',
                        'bondRel_id' => $BondRelation->id
                    ]);
                    $bonds->save();
                    $account = Account::find($bonds->account_id);
                    if ($account) {

                        $account->account_UP = $account->account_UP + $bonds->value;
                        $account->save();

                        return $this->apiresponse($account, 'Account updated successfully', 200);
                    } else {

                        return $this->apiresponse(null, 'Account not found', 404);
                    }
                    if ($BondRelation) {
                        return $this->apiresponse($BondRelation,'This Bonds is Save ',201);

                    }
                    return $this->apiresponse(null,'This Bonds Not Save ',400);
                }
            }
        }

    }

    /**
     * Display the specified resource.
     */



    public function show(string $id)
    {


         //$Bonds = BondRelation::find($id);
         $Bonds = BondRelation::where('bill_id', $id)->get();

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
            'value'=>'required'

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
