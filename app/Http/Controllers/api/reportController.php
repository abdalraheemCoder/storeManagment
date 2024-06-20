<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Account;
//use App\Models\Bill;
use Illuminate\Http\Request;
use App\Models\customer;
use App\Models\driver;
use App\Models\Bill;
use App\Models\Bill_details;
use App\Models\material;
use App\Models\supplier;
use Illuminate\Routing\Controller as RoutingController;
use SebastianBergmann\CodeCoverage\Driver\Driver as DriverDriver;

class reportController extends RoutingController
{
    public function getBillsByCustomer(Request $request, $customerId)
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $bills = Bill::where('customer_id', $customerId)
                     ->whereBetween('date', [$request->fromDate, $request->toDate])
                     ->get();

        return response()->json(['bills' => $bills], 200);
    }

    public function getBillsByMaterial(Request $request, $materialId)
    {
        $material = Material::find($materialId);
        if (!$material) {
            return response()->json(['message' => 'Material not found'], 404);
        }

        $billDetails = Bill_details::where('material_id', $materialId)
                                   ->whereHas('bill', function ($query) use ($request) {
                                       $query->whereBetween('date', [$request->fromDate, $request->toDate]);
                                   })
                                   ->get();

        return response()->json(['billDetails' => $billDetails], 200);
    }

    public function getBillsByDriver(Request $request, string $DriverId)
    {
        $driver = Driver::find($DriverId);
        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        $bills = Bill::where('driver_id', $DriverId)
                     ->whereBetween('date', [$request->fromDate, $request->toDate])
                     ->get();

        return response()->json(['bills' => $bills], 200);
    }

    public function getBillsBySupplier(Request $request, $supplierId)
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $bills = Bill::where('supplier_id', $supplierId)
                     ->whereBetween('date', [$request->fromDate, $request->toDate])
                     ->get();

        return response()->json(['bills' => $bills], 200);
    }

    public function getDeferredSalesBills(Request $request)
    {
        $bills = Bill::where('typeOfbill', 'sale')
                     ->where('typeOfpay', 'def')
                     ->whereBetween('date', [$request->fromDate, $request->toDate])
                     ->get();

        return response()->json(['bills' => $bills], 200);
    }

    public function getDeferredBuyBills(Request $request)
    {
        $bills = Bill::where('typeOfbill', 'buy')
                     ->where('typeOfpay', 'def')
                     ->whereBetween('date', [$request->fromDate, $request->toDate])
                     ->get();

        return response()->json(['bills' => $bills], 200);
    }

    public function getCashSalesBills(Request $request)
    {
        $bills = Bill::where('typeOfbill', 'sale')
                     ->where('typeOfpay', 'cash')
                     ->whereBetween('date', [$request->fromDate, $request->toDate])
                     ->get();

        return response()->json(['bills' => $bills], 200);
    }

    public function getCashBuyBills(Request $request)
    {
        $bills = Bill::where('typeOfbill', 'buy')
                     ->where('typeOfpay', 'cash')
                     ->whereBetween('date', [$request->fromDate, $request->toDate])
                     ->get();

        return response()->json(['bills' => $bills], 200);
    }

    public function accountDetail($ID)
    {
        $account = Account::find($ID);
        if ($account) {
            return response()->json(['account' => $account], 200);
        } else {
            return response()->json(['message' => 'Account not found'], 404);
        }
    }
}
