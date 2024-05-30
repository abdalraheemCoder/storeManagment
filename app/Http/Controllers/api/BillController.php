<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Bill_details;
use App\Models\buyBill;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;
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

        $validator=Validator::make ($request->all(),[
                'price' => 'required',
                'quantity' => 'required',
                'typeOfbill' => 'required|in:buy,sale',
                'typeOfpay' => 'required|in:def,cash',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }


        $bill = New Bill([
            'price' => $request->price,
            'quantity' => $request->quantity,
            //'date' => $request->default(now()),
            'discount' => $request->discount,
            'typeOfbill' => $request->typeOfbill,
            'typeOfpay' => $request->typeOfpay,
            'note' => $request->note
        ]);


        if ($bill->typeOfbill == Bill::typeOfbill_SALE) {
            $bill->customer_id = $request->customer_id;
        } elseif ($bill->typeOfbill = Bill::typeOfbill_BUY) {
            $bill->supplier_id = $request->supplier_id;
        }


        if ($bill->typeOfpay == Bill::typeOfpay_DEF && $bill->typeOfbill == Bill::typeOfbill_SALE) {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->apiresponse(null,$validator->errors(),400);
            }else
            $bill->customer_id = $request->customer_id;
        }



        if ($bill->typeOfpay == Bill::typeOfpay_DEF && $bill->typeOfbill == Bill::typeOfbill_BUY) {
            $validator = Validator::make($request->all(), [
                'supplier_id' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->apiresponse(null,$validator->errors(),400);
            }
            else
            $bill->supplier_id = $request->supplier_id;
        }
        $bill->save();

        //$Bill = Bill::create($request->all());

        if ($bill) {
            return $this->apiresponse($bill,'This Bill is Save ',201);

        }
        return $this->apiresponse(null,'This Bill Not Save ',400);
    }




    public function update(Request $request, string $id)
    {
        $validator=Validator::make ($request->all(),[

        ]);
        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        $Bill = Bill::find($id);
        if($Bill->type ==Bill::typeOfbill_SALE )
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$Bill) {
            return $this->apiresponse(null,'This Bill Not found to updated ',401);
         }

        $Bill->update($request->all());

        if ($Bill) {
            return $this->apiresponse($Bill,'This Bill is update ',201);

        }

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
