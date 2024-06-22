<?php

namespace App\Http\Controllers\api;

use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\driver;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class DriverController extends RoutingController
{
    use ApiResponseTrait;
    use HasApiTokens;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $driver = driver::get();
        return $this->apiresponse($driver,'This all drivers ',200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_name' => 'required|string|max:255|unique:drivers,driver_name',
            'driver_phone' => 'nullable|integer',
            'driver_commission' => 'nullable|integer',
            'driver_address' => 'nullable|string|max:255',
            'note' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->apiresponse(null, $validator->errors(), 400);
        }

        $driver = Driver::create($request->all());

        if ($driver) {
            return $this->apiresponse($driver, 'This Driver is saved', 201);
        }

        return $this->apiresponse(null, 'This Driver is not saved', 400);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // if ($id=null) {
        //     return $this->apiresponse(null,'This id Not found ',401);
        // }
        $driver = driver::find($id);

        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }
        if ($driver) {
           return $this->apiresponse($driver,'This your drivers ',200);
        }
        return $this->apiresponse(null,'This driver Not found ',401);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $driver = Driver::find($id);

          if (!$driver) {
              return $this->apiresponse(null, 'Driver not found', 404);
          }

          $validator = Validator::make($request->all(), [
              'driver_name' => 'sometimes|required|string|max:255|unique:drivers,driver_name,' . $driver->id,
              'driver_phone' => 'sometimes|nullable|integer',
              'driver_commission' => 'sometimes|nullable|integer',
              'driver_address' => 'sometimes|nullable|string|max:255',
              'note' => 'sometimes|nullable|string'
          ]);

          if ($validator->fails()) {
              return $this->apiresponse(null, $validator->errors(), 400);
          }

          $driver->update($request->only([
              'driver_name',
              'driver_phone',
              'driver_address',
              'driver_commission',
              'note'
          ]));

          return $this->apiresponse($driver, 'This Driver is updated', 200);
     }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $driver = driver::find($id);
        if (!$id) {
            return $this->apiresponse(null,'This id Not found ',401);
        }

        if ( !$driver) {
            return $this->apiresponse(null,'This driver Not found to deleted ',401);
        }

        $driver->delete($id);

        if ($driver) {
            return $this->apiresponse($driver,'This driver is deleted ',200);

        }
    }
}
