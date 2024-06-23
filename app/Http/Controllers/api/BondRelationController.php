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
        $validator = Validator::make($request->all(), [
            'value' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        $bill = Bill::find($id);
        if (!$bill) {
            return $this->apiresponse(null, 'Bill not found', 404);
        }

        $account = null;

        if ($bill->typeOfbill == Bill::typeOfbill_SALE) {
            $customerAccountId = $this->getCustomerAccountId($bill->id);
            if ($customerAccountId) {
                $account = $this->handleBondRelation($bill, $customerAccountId, $request->value, 'receipt');
            }
        }
        elseif ($bill->typeOfbill == Bill::typeOfbill_BUY) {
            $supplierAccountId = $this->getSupplierAccountId($bill->id);
            if ($supplierAccountId) {
                $account = $this->handleBondRelation($bill, $supplierAccountId, $request->value, 'payment');
            }
        }
        //
        if ($bill->typeOfbill == Bill::typeOfbill_RE_SALE) {
            $customerAccountId = $this->getCustomerAccountId($bill->id);
            if ($customerAccountId) {
                $account = $this->handleBondRelation($bill, $customerAccountId, $request->value, 'payment');
            }
        }
        elseif ($bill->typeOfbill == Bill::typeOfbill_RE_BUY) {
            $supplierAccountId = $this->getSupplierAccountId($bill->id);
            if ($supplierAccountId) {
                $account = $this->handleBondRelation($bill, $supplierAccountId, $request->value, 'receipt');
            }
        }
        //

        if ($account) {
            $this->updateAccount($account, $bill, $request->value);
            $this->updateMainAccount($bill, $request->value);
        }

        return $this->apiresponse(null, 'Bond and accounts updated successfully', 200);
    }

    private function getCustomerAccountId($billId)
    {
        $customerId = Bill::where('id', $billId)->value('customer_id');
        if ($customerId) {
            return Customer::where('id', $customerId)->value('acc_client_id');
        }
        return null;
    }

    private function getSupplierAccountId($billId)
    {
        $supplierId = Bill::where('id', $billId)->value('supplier_id');
        if ($supplierId) {
            return Supplier::where('id', $supplierId)->value('acc_supplier_id');
        }
        return null;
    }

    private function handleBondRelation($bill, $accountId, $value, $type)
    {
        $bondRelation = BondRelation::firstOrNew(
            ['bill_id' => $bill->id, 'acc_id' => $accountId],
            ['value' => 0]
        );

        $newBondRelationValue = $bondRelation->value + $value;
        $totalBondValues = BondRelation::where('bill_id', $bill->id)->sum('value') + $value;

        if ($newBondRelationValue > $bill->price || $totalBondValues > $bill->price) {
            throw new \Exception('This BondRel Not Save');
        }

        $bondRelation->value = $newBondRelationValue;
        $bondRelation->save();

        $bonds = new Bond([
            'account_id' => $accountId,
            'value' => $value,
            'type' => $type,
            'bondRel_id' => $bondRelation->id
        ]);
        $bonds->save();

        return Account::find($accountId);
    }

    private function updateAccount($account, $bill, $value)
    {
        if ($bill->typeOfbill == Bill::typeOfbill_SALE) {
            if ($account->account_DOWN <= $account->account_UP) {
                $account->account_DOWN += $value;
            }
        } elseif ($bill->typeOfbill == Bill::typeOfbill_BUY) {
            if ($account->account_UP <= $account->account_DOWN) {
                $account->account_UP += $value;
            }
        }
        if ($bill->typeOfbill == Bill::typeOfbill_RE_BUY) {
            if ($account->account_DOWN <= $account->account_UP) {
                $account->account_DOWN += $value;
            }
        } elseif ($bill->typeOfbill == Bill::typeOfbill_RE_SALE) {
            if ($account->account_UP <= $account->account_DOWN) {
                $account->account_UP += $value;
            }
        }
        $account->save();
    }

    private function updateMainAccount($bill, $value)
    {
        $mainAccount = Account::find(1);
        if ($mainAccount) {
            if ($bill->typeOfbill == Bill::typeOfbill_SALE) {
                if ($mainAccount->account_UP <= $mainAccount->account_DOWN) {
                    $mainAccount->account_UP += $value;
                }
            } elseif ($bill->typeOfbill == Bill::typeOfbill_BUY) {
                if ($mainAccount->account_DOWN <= $mainAccount->account_UP) {
                    $mainAccount->account_DOWN += $value;
                }
            }
            if ($bill->typeOfbill == Bill::typeOfbill_RE_BUY) {
                if ($mainAccount->account_UP <= $mainAccount->account_DOWN) {
                    $mainAccount->account_UP += $value;
                }
            } elseif ($bill->typeOfbill == Bill::typeOfbill_RE_SALE) {
                if ($mainAccount->account_DOWN <= $mainAccount->account_UP) {
                    $mainAccount->account_DOWN += $value;
                }
            }
            $mainAccount->save();
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
