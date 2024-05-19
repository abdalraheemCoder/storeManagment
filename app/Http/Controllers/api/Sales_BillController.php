<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Bill_details;
use App\Models\buyBill;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\salseBill;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;
class Sales_BillController extends RoutingController
{
    use ApiResponseTrait;


    public function index()
    {
        $salseBill = salseBill::get();
        return $this->apiresponse($salseBill,'This all salseBill ',200);
    }


    public function show(string $id)
    {
        // if ($id=null) {
        //     return $this->apiresponse(null,'This id Not found ',401);
        // }

        $salseBill = salseBill::find($id);


        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }
        if ($salseBill) {
          return $this->apiresponse($salseBill,'This your salseBills ',200);
        }
        return $this->apiresponse(null,'This salseBill Not found ',401);
    }



    public function store(Request $request)
    {

        $validator=Validator::make ($request->all(),[
            'quantity'=>'required',

        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }


        $salseBill = salseBill::create($request->all());

        if ($salseBill) {
            return $this->apiresponse($salseBill,'This salseBill is Save ',201);

        }
        return $this->apiresponse(null,'This salseBill Not Save ',400);
    }




    public function update(Request $request, string $id)
    {
        $validator=Validator::make ($request->all(),[
            'quantity'=>'required',


        ]);
        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        $salseBill = salseBill::find($id);
        if($salseBill->type ==salseBill::Type_SALE )
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$salseBill) {
            return $this->apiresponse(null,'This salseBill Not found to updated ',401);
         }

        $salseBill->update($request->all());

        if ($salseBill) {
            return $this->apiresponse($salseBill,'This salseBill is update ',201);

        }

    }




    public function destroy(string $id)
    {
        $salseBill = salseBill::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$salseBill) {
            return $this->apiresponse(null,'This salseBill Not found to deleted ',401);
        }

        $salseBill->delete($id);

        if ($salseBill) {
            return $this->apiresponse($salseBill,'This salseBill is deleted ',200);
        }
    }


}
