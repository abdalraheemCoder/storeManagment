<?php

namespace App\Http\Controllers\api;

use App\Models\Bill_details;
use App\Models\buyBill;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\salseBill;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class billDetailController extends RoutingController
{
    use ApiResponseTrait;


    public function index()
    {
        $bill_detil = Bill_details::get();
        return $this->apiresponse($bill_detil,'This all salseBill ',200);
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

        $sasle_bill = salseBill::latest()->first();
        $buy_bill = buyBill::latest()->first();

        //$bill_detil = Bill_details::create($request->all());
        if ($sasle_bill->created_at > $buy_bill-> created_at) {
            {
                $bill_detil = Bill_details::create([
                    'price'=>$request->price,
                    'unit_id'=>$request->unit_id,
                    'material_id'=>$request->material_id,
                    'salse_bill_id'=>$sasle_bill->id
                ]);}
        }

        else{
            $bill_detil = Bill_details::create([
                'price'=>$request->price,
                'unit_id'=>$request->unit_id,
                'material_id'=>$request->material_id,
                'buy_bill_id'=>$buy_bill->id
            ]);}



        if ($bill_detil) {
            return $this->apiresponse($bill_detil,'This bill_detil is Save ',201);

        }
        return $this->apiresponse(null,'This bill_detil Not Save ',400);
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
            return $this->apiresponse(null,'This salsebill_detilBill Not found to deleted ',401);
        }

        $bill_detil->delete($id);

        if ($bill_detil) {
            return $this->apiresponse($bill_detil,'This bill_detil is deleted ',200);
        }
    }
}
