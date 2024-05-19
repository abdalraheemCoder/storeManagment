<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\customer;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;
use App\Models\Account;

class CustmerController extends RoutingController
{

 use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer = customer::get();
        return $this->apiresponse($customer,'This all Custmer ',200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator=Validator::make ($request->all(),[
            'customer_name'=>'required',
        ]);
        $account =Account::Create([
            'account_name' => $request->customer_name,
            //'account_type' =>'0',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }
        $customer = customer::create([
            'customer_name'=>$request->customer_name,
            'customer_area'=>$request->customer_area,
            'acc_client_id'=>$account->id
        ]);

        if ($customer) {
            return $this->apiresponse($customer,'This customers is Save ',201);
        }

        if ($account) {
            return $this->apiresponse($account,'This customers is Save ',201);
        }
        return $this->apiresponse(null,'This customers Not Save ',400);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

      $customer = customer::find($id);
      if (!$id) {
        return $this->apiresponse(null,'This id Not found ',401);
    }
      if ($customer) {
        return $this->apiresponse($customer,'This your Custmer ',200);
      }
      return $this->apiresponse(null,'This Custmer Not found ',401);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator=Validator::make ($request->all(),[
            'customer_name'=>'required',
            //'customer_email'=>'unique'
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        $customer = customer::find($id);
        if (!$id) {
          return $this->apiresponse(null,'This id Not found  ',401);
        }

        if ( !$customer) {
            return $this->apiresponse(null,' This customer Not found to updated ',401);
         }


         $customer->update($request->all());

        if ($customer) {
            return $this->apiresponse($customer,'This customer is Update',201);

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = customer::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$customer) {
            return $this->apiresponse(null,'This customers Not found to deleted ',401);
        }

        $customer->delete($id);

        if ($customer) {
            return $this->apiresponse($customer,'This customer is deleted ',200);

        }
    }

}
