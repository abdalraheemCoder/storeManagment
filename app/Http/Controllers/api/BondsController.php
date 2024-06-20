<?php

namespace App\Http\Controllers\api;

use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\Bond;
use App\Models\Bill;
use App\Models\BondRelation;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;


class BondsController extends RoutingController
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        $Bonds = Bond::get();
        return $this->apiresponse($Bonds,'This all Bonds ',200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:accounts,id',
            'value' => 'required|numeric',
            'type' => 'required|in:receipt,payment',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        $Bonds = Bond::create([
            'account_id' => $request->account_id,
            'value' => $request->value,
            'type' => $request->type,
            'note' => $request->note,
        ]);

        $account = Account::find($request->account_id);
        if ($Bonds->type == 'payment') {
            $account->account_UP += $Bonds->value;
        } elseif ($Bonds->type == 'receipt') {
            $account->account_DOWN += $Bonds->value;
        }
        $account->save();
        $account = Account::find(1);
        if ($Bonds->type == 'payment') {
            $account->account_DOWN += $Bonds->value;
        } elseif ($Bonds->type == 'receipt') {
            $account->account_UP += $Bonds->value;
        }
        $account->save();

        if ($Bonds) {
            return $this->apiresponse($Bonds, 'This Bonds is Save', 201);
        }
        return $this->apiresponse(null, 'This Bonds Not Save', 400);

    }



    /**
     * Display the specified resource.
     */



    public function show(string $id)
    {


         $Bonds = Bond::find($id);

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

        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        $bond = Bond::where('id', $id)->first();
        $bondRelation = BondRelation::find($bond->bondRel_id);
        if ($bondRelation) {
            $bill = Bill::find($bondRelation->bill_id);
            if ($bill) {

                $totalBondValue = Bond::where('bondRel_id', $bondRelation->id)
                                      ->where('id', '!=', $bond->id)
                                      ->sum('value');

                $newTotalBondValue = $totalBondValue + $request->value;

                if ($newTotalBondValue > $bill->price) {
                    return $this->apiresponse(null, 'The total bond value exceeds the bill price', 400);
                }
                else{
                    $bondRelation->value = $newTotalBondValue;
                    $bondRelation->save();
                }
            }
        }
    if ($bond) {
        $account = Account::find($bond->account_id);

        if ($account) {
            if ($bond->type == Bond::typeOfbond_pay) {
                $account->account_UP -= $bond->value;
            } elseif ($bond->type == Bond::typeOfbond_rec) {
                $account->account_DOWN -= $bond->value;
            }

            $account->save();
        }
        $account = Account::find(1);
        if ($account) {
            if ($bond->type == Bond::typeOfbond_pay) {
                $account->account_DOWN -= $bond->value;
            } elseif ($bond->type == Bond::typeOfbond_rec) {
                $account->account_UP -= $bond->value;
            }

            $account->save();
        }


        $bond->value = $request->value;
        $bond->save();

        $account = Account::find($bond->account_id);
        if ($account) {
            if ($bond->type == Bond::typeOfbond_pay) {
                $account->account_UP += $bond->value;
            } elseif ($bond->type == Bond::typeOfbond_rec) {
                $account->account_DOWN += $bond->value;
            }

            $account->save();
        }


        $account = Account::find(1);
        if ($account) {
            if ($bond->type == Bond::typeOfbond_pay) {
                $account->account_DOWN += $bond->value;
            } elseif ($bond->type == Bond::typeOfbond_rec) {
                $account->account_UP += $bond->value;
            }

            $account->save();
        }
            if ($bondRelation) {

                return $this->apiresponse(null, 'Bond and related bond relation updated successfully', 200);
            } else {
                return $this->apiresponse(null, 'Bond relation not found for this bond', 404);
            }
        } else {
            return $this->apiresponse(null, 'Bond not found', 404);
        }
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$bond) {
            return $this->apiresponse(null,'This Bonds Not found to updated ',401);
         }


        if ($bond) {
            return $this->apiresponse($bond,'This Bonds is update ',201);

        }

    }
    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        $Bonds = Bond::find($id);
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
