<?php

namespace App\Http\Controllers\api;

use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\Bond;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;


class BondsController extends RoutingController
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        $Bonds = Bond::get();
        return $this->apiresponse($Bonds,'This all Bonds ',200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator=Validator::make($request->all(),[
            'account_id'=>'required'

        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }


        //$Bonds = Bond::create($request->all());
        $Bonds = Bond::create([
            'account_id'=>$request->account_id,
            'value'=>$request->value,
            'bond_type'=>$request->bond_type
        ]);

        $account = Account::find($request->account_id);

        if ($Bonds->bond_type=='0') {

            $account->account_DOWN =$account->account_DOWN + $Bonds->value;
            $account->save();

        }
        else if($Bonds->bond_type=='1'){

            $account->account_UP =$account->account_UP + $Bonds->value;
            $account->save();
        }

        if ($Bonds) {
            return $this->apiresponse($Bonds,'This Bonds is Save ',201);

        }
        return $this->apiresponse(null,'This Bonds Not Save ',400);
    }



    /**
     * Display the specified resource.
     */



    public function show(string $id)
    {


         $Bonds = Bond::find($id);

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
            'account_id'=>'required'

        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        $Bonds = Bond::find($id);

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
        $Bonds = Bond::find($id);
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
