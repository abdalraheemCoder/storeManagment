<?php

namespace App\Http\Controllers\api;

use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\buyBill;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;

class Buy_BillController extends RoutingController
{
    use ApiResponseTrait;

    public function index()
    {
        $buyBill = buyBill::get();
        return $this->apiresponse($buyBill,'This all buyBill ',200);
    }


    public function show(string $id)
    {
        // if ($id=null) {
        //     return $this->apiresponse(null,'This id Not found ',401);
        // }

        $buyBill = buyBill::find($id);

        if ($buyBill) {
          return $this->apiresponse($buyBill,'This your buyBills ',200);
        }
        return $this->apiresponse(null,'This buyBill Not found ',401);
    }



    public function store(Request $request)
    {

        $validator=Validator::make ($request->all(),[
            //'buyBill_details'=>'required',
            'quantity'=>'required',


        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }


        $buyBill = buyBill::create($request->all());

        if ($buyBill) {
            return $this->apiresponse($buyBill,'This buyBill is Save ',201);

        }
        return $this->apiresponse(null,'This buyBill Not Save ',400);
    }




    public function update(Request $request, string $id)
    {
        $validator=Validator::make ($request->all(),[
            'buyBill_details'=>'required',
            'quantity'=>'required'


        ]);
        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        $buyBill = buyBill::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$buyBill) {
            return $this->apiresponse(null,'This buyBill Not found to updated ',401);
         }

        $buyBill->update($request->all());

        if ($buyBill) {
            return $this->apiresponse($buyBill,'This buyBill is update ',201);

        }

    }


    public function destroy(string $id)
    {

        $buyBill = buyBill::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$buyBill) {
            return $this->apiresponse(null,'This buyBill Not found to deleted ',401);
        }

        $buyBill->delete($id);

        if ($buyBill) {
            return $this->apiresponse($buyBill,'This buyBill is deleted ',200);
        }
    }

}
