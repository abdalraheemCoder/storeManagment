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
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255|unique:customers',
            'customer_phone' => 'nullable|integer|max:10',
            'customer_area' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        $account = Account::create([
            'account_name' => $request->customer_name,
        ]);

        if (!$account) {
            return $this->apiresponse(null, 'Failed to create account', 400);
        }

        $customer = Customer::create([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_area' => $request->customer_area,
            'acc_client_id' => $account->id,
            'note' => $request->note,
        ]);

        if ($customer) {
            return $this->apiresponse($customer, 'Customer saved successfully', 201);
        } else {
            $account->delete();
            return $this->apiresponse(null, 'Failed to save customer', 400);
        }


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
        $customer = Customer::find($id);

        if (!$customer) {
            return $this->apiresponse(null, 'Customer not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'customer_name' => 'sometimes|required|string|max:255|unique:customers,customer_name,' . $id,
            'customer_phone' => 'sometimes|nullable|integer',
            'customer_area' => 'sometimes|nullable|string',
            'note' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        if ($request->has('customer_name')) {
            $customer->customer_name = $request->customer_name;
            $account = Account::find($customer->acc_client_id);
            if ($account) {
                $account->account_name = $request->customer_name;
                $account->save();
            }
        }
        $customer->fill($request->only([
            'customer_phone',
            'customer_area',
            'note',
        ]));

        $customer->save();

        return $this->apiresponse($customer, 'Customer updated successfully', 200);

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
