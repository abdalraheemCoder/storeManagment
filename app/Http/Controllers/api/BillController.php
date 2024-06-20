<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Bill;
use App\Models\Bill_details;
use App\Models\buyBill;
use App\Models\customer;
use App\Models\supplier;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;
use App\Models\driver;

class BillController extends RoutingController
{
    use ApiResponseTrait;


    public function index()
    {
        $Bill = Bill::get();
        return $this->apiresponse($Bill,'This all Bill ',200);
    }


    public function show(string $id)
    {
        // if ($id=null) {
        //     return $this->apiresponse(null,'This id Not found ',401);
        // }

        $Bill = Bill::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }
        if ($Bill) {
          return $this->apiresponse($Bill,'This your Bills ',200);
        }
        return $this->apiresponse(null,'This Bill Not found ',401);
    }



    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'price' => 'nullable|numeric',
            'quantity' => 'nullable|numeric',
            'date' => 'nullable|date',
            'discount' => 'nullable|numeric',
            'discount%' => 'nullable|numeric',
            'typeOfbill' => 'required|in:buy,sale,re_sale,re_buy',
            'typeOfpay' => 'required|in:def,cash',
            'note' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        $bill = new Bill([
            'price' => $request->price,
            'quantity' => $request->quantity,
            'date' => $request->date ?? now(),
            'discount' => $request->discount,
            'discount%' => $request->input('discount%'),
            'typeOfbill' => $request->typeOfbill,
            'typeOfpay' => $request->typeOfpay,
            'driver_id'=>$request->driver,
            'note' => $request->note
        ]);

        if ($bill->typeOfbill == 'sale' || $bill->typeOfbill == 're_sale') {
            if ($request->has('customer_id')) {
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    $bill->customer_id = $request->customer_id;
                } else {
                    return $this->apiresponse(null, ['customer_id' => ['Customer not found']], 400);
                }
            }
        } elseif ($bill->typeOfbill == 'buy' || $bill->typeOfbill == 're_buy') {
            if ($request->has('supplier_id')) {
                $supplier = Supplier::find($request->supplier_id);
                if ($supplier) {
                    $bill->supplier_id = $request->supplier_id;
                } else {
                    return $this->apiresponse(null, ['supplier_id' => ['Supplier not found']], 400);
                }
            }
        }

        if (($bill->typeOfpay == 'def' && $bill->typeOfbill == 'sale') ||
            ($bill->typeOfpay == 'def' && $bill->typeOfbill == 're_sale')) {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return $this->apiresponse(null, $validator->errors(), 400);
            } else {
                $bill->customer_id = $request->customer_id;
            }
        }

        if (($bill->typeOfpay == 'def' && $bill->typeOfbill == 'buy') ||
            ($bill->typeOfpay == 'def' && $bill->typeOfbill == 're_buy')) {
            $validator = Validator::make($request->all(), [
                'supplier_id' => 'required|exists:suppliers,id',
            ]);
            if ($validator->fails()) {
                return $this->apiresponse(null, $validator->errors(), 400);
            } else {
                $bill->supplier_id = $request->supplier_id;
            }
        }
        $bill->save();

        if ($bill) {
            return $this->apiresponse($bill, 'This Bill is saved', 201);
        }

        return $this->apiresponse(null, 'This Bill is not saved', 400);
    }




    public function update(Request $request, string $id)
    {
      $validator = Validator::make($request->all(), [
          'date' => 'nullable|date',
          'discount' => 'nullable|numeric',
          'typeOfbill' => 'sometimes|required|in:buy,sale,re_sale,re_buy',
          'typeOfpay' => 'sometimes|required|in:def,cash',
          'note' => 'nullable|string',
          'customer_id' => 'nullable|exists:customers,id',
          'driver_id' => 'nullable|exists:drivers,id',
          'supplier_id' => 'nullable|exists:suppliers,id',
      ]);

      if ($validator->fails()) {
          return $this->apiresponse(null, $validator->errors(), 400);
      }

      $Bill = Bill::find($id);

      if (!$Bill) {
          return $this->apiresponse(null, 'This Bill Not found to update', 404);
      }

      if ($request->has('note')) {
          $Bill->note = $request->note;
      }
      if ($request->has('discount')) {
          $Bill->discount = $request->discount;
      }
      if ($request->has('price')) {
          return $this->apiresponse(null, 'you cant update price', 404);
      }
      if ($request->has('quantity')) {
          return $this->apiresponse(null, 'This Bill Not found to store', 404);;
      }
      if ($request->has('date')) {
          $Bill->date = $request->date;
      }
      if ($request->has('typeOfbill')) {
          $Bill->typeOfbill = $request->typeOfbill;
      }
      if ($request->has('typeOfpay')) {
          $Bill->typeOfpay = $request->typeOfpay;
      }

      if ($request->has('customer_id')) {
          $customer = Customer::find($request->customer_id);
          if ($customer) {
              $Bill->customer_id = $request->customer_id;
          } else {
              return $this->apiresponse(null, 'Customer not found', 401);
          }
      }

      if ($request->has('supplier_id')) {
          $supplier = Supplier::find($request->supplier_id);
          if ($supplier) {
              $Bill->supplier_id = $request->supplier_id;
          } else {
              return $this->apiresponse(null, 'Supplier not found', 401);
          }
      }

      if ($request->has('driver_id')) {
          $driver = Driver::find($request->driver_id);
          if ($driver) {
              $Bill->driver_id = $request->driver_id;
          } else {
              return $this->apiresponse(null, 'Driver not found', 401);
          }
      }

      $Bill->save();

      return $this->apiresponse($Bill, 'This Bill is updated', 200);
    }




    public function destroy(string $id)
    {
        $Bill = Bill::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$Bill) {
            return $this->apiresponse(null,'This Bill Not found to deleted ',401);
        }

        $Bill->delete($id);

        if ($Bill) {
            return $this->apiresponse($Bill,'This Bill is deleted ',200);
        }
    }


}
