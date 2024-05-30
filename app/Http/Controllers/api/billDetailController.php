<?php

namespace App\Http\Controllers\api;

use App\Models\Bill_details;
use App\Models\buyBill;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\Bill;
use App\Models\unit;
use Illuminate\Routing\Controller as RoutingController;
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
        // if ($id=null) {
        //     return $this->apiresponse(null,'This id Not found ',401);
        // }

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

        $validator=Validator::make ($request->all(),[
            'material_id'=>'required',
            'unit_id' => 'required'

        ]);


        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        $Bill = Bill::latest()->first();
        //$buy_bill = buyBill::latest()->first();

        //$bill_detil = Bill_details::create($request->all());
        $unitId = $request->unit_id;
        $unit = Unit::where('unit_mat_id', $request->material_id)->where('id', $unitId)->first();

        if (!$unit) {
            return response()->json([
                'error' => 'Unit not found for the given material'
            ], 404);
        }

        if ($Bill->typeOfbill == Bill::typeOfbill_SALE) {
            $bill_detail= Bill_details::create([
                'price' => $request->price,
                'quantity' => $request->quantity,
                'material_id' => $request->material_id,
                'unit_id' => $unit->id,
                'bill_id' => $Bill->id,
                'type' => 'sale'
            ]);
                if ($bill_detail) {
                    return $this->apiresponse($bill_detail,'This bill_detil is Save ',201);

                }
                return $this->apiresponse(null,'This bill_detil Not Save ',400);

      }
        elseif($Bill->typeOfbill ==Bill::typeOfbill_BUY ){
            $bill_detail = Bill_details::create([
                'price'=>$request->price,
                'quantity'=>$request->quantity,
                'unit_id'=>$request->unit_id,
                'material_id'=>$request->material_id,
                'bill_id'=>$Bill->id,
                'type'=>'buy'
            ]);

            if ($bill_detail) {
                return $this->apiresponse($bill_detail,'This bill_detil is Save ',201);

            }
            return $this->apiresponse(null,'This bill_detil Not Save ',400);


    }
    $billDetails = Bill_details::where('bill_id', $Bill->id)->get();
    $totalPrice = $billDetails->sum('price');
    $totalQuantity = $billDetails->sum('quantity');


    $Bill->price = $totalPrice;
    $Bill->quantity = $totalQuantity;
    $Bill->save();

}


    public function update(Request $request, string $id)
    {
        $validator=Validator::make ($request->all(),[
            'quantity'=>'required',


        ]);
        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        $bill_detil= Bill_details::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$bill_detil) {
            return $this->apiresponse(null,'This bill_detil Not found to updated ',401);
         }

        $bill_detil->update($request->all());

        if ($bill_detil) {
            return $this->apiresponse($bill_detil,'This bill_detil is update ',201);

        }

    }




    public function destroy(string $id)
    {
        $bill_detil = Bill_details::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$bill_detil) {
            return $this->apiresponse(null,'This Bill_detilBill Not found to deleted ',401);
        }

        $bill_detil->delete($id);

        if ($bill_detil) {
            return $this->apiresponse($bill_detil,'This bill_detil is deleted ',200);
        }
    }
}
