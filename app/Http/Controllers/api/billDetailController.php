<?php

namespace App\Http\Controllers\api;

use App\Models\Account;
use App\Models\Bill_details;
use App\Models\buyBill;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\Bill;
use App\Models\customer;
use App\Models\material;
use App\Models\supplier;
use App\Models\unit;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class billDetailController extends RoutingController
{
    use ApiResponseTrait;


    public function index()
    {
        $bill_detil = Bill_details::get();
        return $this->apiresponse($bill_detil,'This all Bill ',200);
    }


    public function show(string $id)
    {
        $bill_detil = Bill_details::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }
        if ($bill_detil) {
          return $this->apiresponse($bill_detil,'This your bill_detil ',200);
        }
        return $this->apiresponse(null,'This bill_detil Not found ',401);
    }

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'price' => 'nullable|integer|min:0',
        'totalPrice' => 'nullable|integer|min:0',
        'quantity' => 'required|integer|min:1',
        'discount' => 'nullable|numeric|min:0',
        'discount%' => 'nullable|numeric|min:0',
        'note' => 'nullable|string',
        'unit_id' => 'required|exists:units,id',
        'material_id' => 'required|exists:materials,id',
    ]);

    if ($validator->fails()) {
        return $this->apiresponse(null, $validator->errors(), 400);
    }

    return DB::transaction(function () use ($request) {
        $Bill = Bill::latest()->first();

        $unitId = $request->unit_id;
        $materialId = $request->material_id;

        $unit = Unit::where('unit_mat_id', $materialId)->where('id', $unitId)->first();
        $material = Material::find($materialId);

        if (!$material) {
            return response()->json(['error' => 'Material not found'], 404);
        }
        if (!$unit) {
            return response()->json(['error' => 'Unit not found for the given material'], 404);
        }
        if ($Bill->typeOfbill == Bill::typeOfbill_SALE || $Bill->typeOfbill == Bill::typeOfbill_RE_SALE )  {
            $defaultPrice = $unit->unitSalse_price;
        } elseif ($Bill->typeOfbill == Bill::typeOfbill_BUY || $Bill->typeOfbill == Bill::typeOfbill_RE_BUY) {
            $defaultPrice = $unit->unitbuy_price;
        } else {
            $defaultPrice = 0;
        }

        $price = $request->price ?? $defaultPrice;
        $quantity = $request->quantity;
        $discount = $request->discount ?? 0;
        $discountPercentage = $request->{'discount%'} ?? 0;

        $totalPriceBeforeDiscount = $price * $quantity;
        $totalPriceAfterDiscount = $totalPriceBeforeDiscount - $discount;
        $totalPriceAfterDiscount -= $totalPriceAfterDiscount * ($discountPercentage / 100);

        $bill_detail = Bill_details::create([
            'price' => $price,
            'quantity' => $quantity,
            'totalPrice' => $totalPriceAfterDiscount,
            'discount' => $discount,
            'discount %' => $discountPercentage,
            'note' => $request->note,
            'material_id' => $materialId,
            'unit_id' => $unit->id,
            'bill_id' => $Bill->id,
            'type' => $Bill->typeOfbill,
        ]);
        $units = Unit::where('unit_mat_id', $materialId)->get();
        $quantityToAdjust = $quantity;

        foreach ($units as $otherUnit) {
            if ($Bill->typeOfbill == Bill::typeOfbill_SALE || $Bill->typeOfbill == Bill::typeOfbill_RE_BUY) {
                if ($unit->unit_equal > $otherUnit->unit_equal) {
                    $otherUnit->Quantity -= $quantityToAdjust * ($unit->unit_equal / $otherUnit->unit_equal);
                } else {
                    $otherUnit->Quantity -= intval($quantityToAdjust / ($otherUnit->unit_equal / $unit->unit_equal));
                }
            } elseif ($Bill->typeOfbill == Bill::typeOfbill_BUY || $Bill->typeOfbill == Bill::typeOfbill_RE_SALE) {
                if ($unit->unit_equal > $otherUnit->unit_equal) {
                    $otherUnit->Quantity += $quantityToAdjust * ($unit->unit_equal / $otherUnit->unit_equal);
                } else {
                    $otherUnit->Quantity += intval($quantityToAdjust / ($otherUnit->unit_equal / $unit->unit_equal));
                }
            }
            $otherUnit->save();
        }

        $billDetails = Bill_details::where('bill_id', $Bill->id)->get();
        $totalPrice = $billDetails->sum('totalPrice');
        $totalQuantity = $billDetails->sum('quantity');
        $Bill->price = $totalPrice;
        $Bill->quantity = $totalQuantity;
        $Bill->save();

        $this->StoreAccounts($Bill);

        return $this->apiresponse($Bill, 'This bill_detail is successful', 200);
    });

}
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'nullable|integer|min:0',
            'totalPrice' => 'nullable|integer|min:0',
            'quantity' => 'nullable|integer|min:1|required_with:material_id',
            'discount' => 'nullable|numeric|min:0',
            'discount %' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'unit_id' => 'nullable|exists:units,id|required_with:material_id',
            'material_id' => 'nullable|exists:materials,id',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        return DB::transaction(function () use ($request, $id) {
            $bill_detail = Bill_details::findOrFail($id);
            $Bill = Bill::findOrFail($bill_detail->bill_id);

            $oldunitId = $bill_detail->unit_id;
            $oldmaterialId = $bill_detail->material_id;
            $unitId = $request->unit_id ?? $oldunitId;
            $materialId = $request->material_id ?? $oldmaterialId;

            $oldPrice = $bill_detail->price;
            $oldQuantity = $bill_detail->quantity;

            $Ounit = Unit::where('unit_mat_id', $oldmaterialId)->where('id', $oldunitId)->first();
            $Omaterial = Material::find($oldmaterialId);
            $unit = Unit::where('unit_mat_id', $materialId)->where('id', $unitId)->first();
            $material = Material::find($materialId);

            if ($materialId && (!$material || !$unit || !$Omaterial || !$Ounit)) {
                return response()->json(['error' => 'Invalid material or unit'], 404);
            }

            if ($materialId) {
                $oldUnits = Unit::where('unit_mat_id', $oldmaterialId)->get();

                foreach ($oldUnits as $old) {
                    if ($Bill->typeOfbill == Bill::typeOfbill_SALE || $Bill->typeOfbill == Bill::typeOfbill_RE_BUY) {
                        if ($Ounit->unit_equal > $old->unit_equal) {
                            $old->Quantity += $oldQuantity * ($Ounit->unit_equal / $old->unit_equal);
                        } else {
                            $old->Quantity += $oldQuantity / ($old->unit_equal / $Ounit->unit_equal);
                        }
                    } elseif ($Bill->typeOfbill == Bill::typeOfbill_BUY || $Bill->typeOfbill == Bill::typeOfbill_RE_SALE) {
                        if ($Ounit->unit_equal > $old->unit_equal) {
                            $old->Quantity -= $oldQuantity * ($Ounit->unit_equal / $old->unit_equal);
                        } else {
                            $old->Quantity -= $oldQuantity / ($old->unit_equal / $Ounit->unit_equal);
                        }
                    }
                    $old->save();
                }

                $units = Unit::where('unit_mat_id', $materialId)->get();
                $newQuantity = $request->quantity ?? $oldQuantity;

                foreach ($units as $otherUnit) {
                    if ($Bill->typeOfbill == Bill::typeOfbill_SALE || $Bill->typeOfbill == Bill::typeOfbill_RE_BUY) {
                        if ($unit->unit_equal > $otherUnit->unit_equal) {
                            $otherUnit->Quantity -= $newQuantity * ($unit->unit_equal / $otherUnit->unit_equal);
                        } else {
                            $otherUnit->Quantity -= $newQuantity / ($otherUnit->unit_equal / $unit->unit_equal);
                        }
                    } elseif ($Bill->typeOfbill == Bill::typeOfbill_BUY || $Bill->typeOfbill == Bill::typeOfbill_RE_SALE) {
                        if ($unit->unit_equal > $otherUnit->unit_equal) {
                            $otherUnit->Quantity += intval($newQuantity * ($unit->unit_equal / $otherUnit->unit_equal));
                        } else {
                            $otherUnit->Quantity += intval($newQuantity / ($otherUnit->unit_equal / $unit->unit_equal));
                        }
                    }
                    $otherUnit->save();
                }
            }

            $price = $request->price ?? $bill_detail->price;
            $quantity = $request->quantity ?? $bill_detail->quantity;
            $discount = $request->discount ?? $bill_detail->discount;
            $discountPercentage = $request->{'discount %'} ?? $bill_detail->{'discount %'};

            $totalPriceBeforeDiscount = $price * $quantity;
            $totalPriceAfterDiscount = $totalPriceBeforeDiscount - $discount;
            $totalPriceAfterDiscount -= $totalPriceAfterDiscount * ($discountPercentage / 100);

            $bill_detail->update([
                'price' => $price,
                'quantity' => $quantity,
                'totalPrice' => $totalPriceAfterDiscount,
                'discount' => $discount,
                'discount %' => $discountPercentage,
                'note' => $request->note,
                'unit_id' => $unitId,
                'material_id' => $materialId,
            ]);

            $billDetails = Bill_details::where('bill_id', $Bill->id)->get();
            $totalPrice = $billDetails->sum('totalPrice');
            $totalQuantity = $billDetails->sum('quantity');
            $Bill->price = $totalPrice;
            $Bill->quantity = $totalQuantity;
            $Bill->save();

            if ($request->has('price') && $request->price != $oldPrice) {
                $this->updateAccounts($Bill, $oldPrice, $request->price);
            }

            return $this->apiresponse($bill_detail, 'Bill detail updated successfully', 200);
        });
    }

    private function updateAccounts($Bill, $oldPrice, $newPrice)
    {
        if (($Bill->typeOfbill == Bill::typeOfbill_SALE && $Bill->typeOfpay == Bill::typeOfpay_CASH) ||
            ($Bill->typeOfbill == Bill::typeOfbill_RE_BUY && $Bill->typeOfpay == Bill::typeOfpay_CASH)) {
            $account = Account::find(1);
            if ($account) {
                $account->account_UP += ($newPrice - $oldPrice);
                $account->save();
            } else {
                return $this->apiresponse(null, 'Account not found', 404);
            }
        }

        if (($Bill->typeOfbill == Bill::typeOfbill_SALE && $Bill->typeOfpay == Bill::typeOfpay_DEF) ||
            ($Bill->typeOfbill == Bill::typeOfbill_RE_BUY && $Bill->typeOfpay == Bill::typeOfpay_DEF)) {
            $acc_customer = Customer::where('id', $Bill->customer_id)->select('acc_client_id')->first();
            if ($acc_customer) {
                $account = Account::find($acc_customer->acc_client_id);
                if ($account) {
                    $account->account_UP += ($newPrice - $oldPrice);
                    $account->save();
                } else {
                    return $this->apiresponse(null, 'Account not found', 404);
                }
            } else {
                return $this->apiresponse(null, 'Account not found', 404);
            }
        }

        if (($Bill->typeOfbill == Bill::typeOfbill_BUY && $Bill->typeOfpay == Bill::typeOfpay_CASH) ||
            ($Bill->typeOfbill == Bill::typeOfbill_RE_SALE && $Bill->typeOfpay == Bill::typeOfpay_CASH)) {
            $account = Account::find(1);
            if ($account) {
                $account->account_DOWN += ($newPrice - $oldPrice);
                $account->save();
            } else {
                return $this->apiresponse(null, 'Account not found', 404);
            }
        }

        if (($Bill->typeOfbill == Bill::typeOfbill_BUY && $Bill->typeOfpay == Bill::typeOfpay_DEF) ||
            ($Bill->typeOfbill == Bill::typeOfbill_RE_SALE && $Bill->typeOfpay == Bill::typeOfpay_DEF)) {
                $acc_supplier = Supplier::where('id', $Bill->supplier_id)->select('acc_supplier_id')->first();
            if ($acc_supplier) {
                $account = Account::find($acc_supplier->acc_supplier_id);
                if ($account) {
                    $account->account_DOWN += ($newPrice - $oldPrice);
                    $account->save();
                } else {
                    return $this->apiresponse(null, 'Account not found', 404);
                }
            }
        }
    }
    private function StoreAccounts($Bill)
{
        if (($Bill->typeOfbill == Bill::typeOfbill_SALE && $Bill->typeOfpay == Bill::typeOfpay_CASH)){
        $sPriceDetail = Bill_details::where('bill_id', $Bill->id)->where('type', 'sale')->first();
        $account = Account::find(1);
        if ($account) {
            $totalPrice = $sPriceDetail->totalPrice;
            $account->account_UP += $totalPrice;
            $account->save();
        } else {
            return $this->apiresponse(null, 'Account not found', 404);
        }
    }
    if (($Bill->typeOfbill == Bill::typeOfbill_SALE && $Bill->typeOfpay == Bill::typeOfpay_DEF)) {
            $acc_customer = Customer::where('id', $Bill->customer_id)->select('acc_client_id')->first();
        $sPriceDetail = Bill_details::where('bill_id', $Bill->id)->where('type', 'sale')->first();
        if ($acc_customer) {
            $account = Account::find($acc_customer->acc_client_id);
            if ($account) {
                $totalPrice = $sPriceDetail->totalPrice;
                $account->account_UP += $totalPrice;
                $account->save();
            } else {
                return $this->apiresponse(null, 'Account not found', 404);
            }
        } else {
            return $this->apiresponse(null, 'Account not found', 404);
        }
    }
    if (($Bill->typeOfbill == Bill::typeOfbill_RE_BUY && $Bill->typeOfpay == Bill::typeOfpay_CASH)) {
        $sPriceDetail = Bill_details::where('bill_id', $Bill->id)->where('type', 're_buy')->first();
        $account = Account::find(1);
        if ($account) {
            $totalPrice = $sPriceDetail->totalPrice;

            $account->account_UP += $totalPrice;
            $account->save();
        } else {
            return $this->apiresponse(null, 'Account not found', 404);
        }
    }
    if (($Bill->typeOfbill == Bill::typeOfbill_RE_BUY && $Bill->typeOfpay == Bill::typeOfpay_DEF)) {
        $acc_supplier = Supplier::where('id', $Bill->supplier_id)->select('acc_supplier_id')->first();
        $sPriceDetail = Bill_details::where('bill_id', $Bill->id)->where('type', 're_buy')->first();
        if ($acc_supplier) {
            $account = Account::find($acc_supplier->acc_supplier_id);
            if ($account) {
                $totalPrice = $sPriceDetail->totalPrice;
                $account->account_UP += $totalPrice;
                $account->save();
            } else {
                return $this->apiresponse(null, 'Account not found', 404);
            }
        }
        $sPriceDetail = Bill_details::where('bill_id', $Bill->id)->where('type', 're_buy')->first();
        $account = Account::find(1);
        if ($account) {
            $totalPrice = $sPriceDetail->totalPrice;

            $account->account_UP += $totalPrice;
            $account->save();
        } else {
            return $this->apiresponse(null, 'Account not found', 404);
        }
    }

    if (($Bill->typeOfbill == Bill::typeOfbill_BUY && $Bill->typeOfpay == Bill::typeOfpay_CASH)) {
            $bPriceDetail = Bill_details::where('bill_id', $Bill->id)->where('type', 'buy')->first();
            $account = Account::find(1);
            if ($account) {
                $totalPrice = $bPriceDetail->totalPrice;
                $account->account_DOWN += $totalPrice;
                $account->save();
            } else {
            return $this->apiresponse(null, 'Account not found', 404);
        }
    }

    if (($Bill->typeOfbill == Bill::typeOfbill_RE_SALE && $Bill->typeOfpay == Bill::typeOfpay_CASH)) {
        $bPriceDetail = Bill_details::where('bill_id', $Bill->id)->where('type', 're_sale')->first();
        $account = Account::find(1);
        if ($account) {
            $totalPrice = $bPriceDetail->totalPrice;
            $account->account_DOWN += $totalPrice;
            $account->save();
            //return $this->apiresponse($totalPrice, 'Account not found', 404);
        } else {
            return $this->apiresponse(null, 'Account not found', 404);
        }
    }
    if ($Bill->typeOfbill == Bill::typeOfbill_RE_SALE && $Bill->typeOfpay == Bill::typeOfpay_DEF ) {
        $acc_customer = Customer::where('id', $Bill->customer_id)->select('acc_client_id')->first();
    $sPriceDetail = Bill_details::where('bill_id', $Bill->id)->where('type', 're_sale')->first();
    if ($acc_customer) {
        $account = Account::find($acc_customer->acc_client_id);
        if ($account) {
            $totalPrice = $sPriceDetail->totalPrice;
            $account->account_UP += $totalPrice;
            $account->save();
        } else {
            return $this->apiresponse(null, 'Account not found', 404);
        }
        $bPriceDetail = Bill_details::where('bill_id', $Bill->id)->where('type', 're_sale')->first();
        $account = Account::find(1);
        if ($account) {
            $totalPrice = $bPriceDetail->totalPrice;
            $account->account_DOWN += $totalPrice;
            $account->save();
        } else {
            return $this->apiresponse(null, 'Account not found', 404);
        }
    } else {
        return $this->apiresponse(null, 'Account not found', 404);
    }
}
    if (($Bill->typeOfbill == Bill::typeOfbill_BUY && $Bill->typeOfpay == Bill::typeOfpay_DEF)) {
        $acc_supplier = Supplier::where('id', $Bill->supplier_id)->select('acc_supplier_id')->first();
        $sPriceDetail = Bill_details::where('bill_id', $Bill->id)->where('type', 'buy')->first();
        if ($acc_supplier) {
            $account = Account::find($acc_supplier->acc_supplier_id);
            if ($account) {
                $totalPrice = $sPriceDetail->totalPrice;
                $account->account_DOWN += $totalPrice;
                $account->save();
            } else {
                return $this->apiresponse(null, 'Account not found', 404);
            }
        }
    }

}

}
