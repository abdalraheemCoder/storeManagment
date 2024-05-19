<?php

namespace App\Http\Controllers\api;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;


class AccountController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Account = Account::get();
        return $this->apiresponse($Account,'This all Account ',200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'account_name'=>'required',
            //'account_type'=>'required'

        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        $Account = Account::create($request->all());

        if ($Account) {
            return $this->apiresponse($Account,'This Account is Save ',201);

        }
        return $this->apiresponse(null,'This Account Not Save ',400);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Account = Account::find($id);

        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
       }

        if ($Account) {
           return $this->apiresponse($Account,'This your Account ',200);
        }

        return $this->apiresponse(null,'This Account Not found ',401);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator=Validator::make($request->all(),[
            'account_name'=>'required',

        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null,$validator->errors(),400);
        }

        $Account = Account::find($id);

        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$Account) {
            return $this->apiresponse(null,'This Account Not found to updated ',401);
         }

        $Account->update($request->all());

        if ($Account) {
            return $this->apiresponse($Account,'This Account is update ',201);

        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $Account = Account::find($id);

        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$Account) {
            return $this->apiresponse(null,'This Account Not found to deleted ',401);
        }

        $Account->delete($id);

        if ($Account) {
            return $this->apiresponse($Account,'This Account is deleted ',200);
        }
    }
}
