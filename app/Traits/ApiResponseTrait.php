<?php

namespace App\Traits;

trait  ApiResponseTrait
{

  public function apiresponse($data=null, $message=null,$status=200)
   {
      return response()->json($data);
   }
}
