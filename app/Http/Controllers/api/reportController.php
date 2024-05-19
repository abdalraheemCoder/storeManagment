<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\buyBill;
use Illuminate\Http\Request;
use App\Models\customer;
use App\Models\driver;
use App\Models\salseBill;
use App\Models\supplier;
use Illuminate\Routing\Controller as RoutingController;

class reportController extends RoutingController
{
    public function salesReportForClientStatement(string $customer_id)
    {

        $customer = Customer::where('id', $customer_id)->first();

    if (!$customer) {
        return response()->json(['message' => 'العميل غير موجود']);
    }

    $salesBills = SalseBill::where('customer_id', $customer->id)->get();

    return response()->json(['sales_bills' => $salesBills]);
    }

    public function salesReportForDrivertStatement(string $driver_name)
    {

        $driver = driver::where('driver_name', $driver_name)->first();

    if (!$driver) {
        return response()->json(['message' => 'العميل غير موجود']);
    }


    $salesBills = SalseBill::where('driver_id', $driver->id)->get();

    return response()->json(['sales_bills' => $salesBills]);
    }

    public function BuyReportForsupplierStatement(string $supplier_name)
    {

        $supplier = supplier::where('supplier_name', $supplier_name)->first();

    if (!$supplier) {
        return response()->json(['message' => 'العميل غير موجود']);
    }


    $buy_bills = buyBill::where('supplier_id', $supplier->id)->get();

    return response()->json(['buy_bills' => $buy_bills]);
    }


    public function saleReportForTypeOfBill(bool $type)
    {

    $salesBills = SalseBill::where('type', $type)->get();

    if ($salesBills->isEmpty()) {
        return response()->json(['message' => 'لا توجد فواتير ']);
    }

    return response()->json(['salesBills' => $salesBills]);
    }

    public function BuyReportForTypeOfBill(bool $type)
    {

    $buyBills = buyBill::where('type', $type)->get();

    if ($buyBills->isEmpty()) {
        return response()->json(['message' => 'لا توجد فواتير ']);
    }

    return response()->json(['buyBills' => $buyBills]);
    }
}
