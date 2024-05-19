<?php

namespace App\Traits;

trait  ApiResponseTrait
{

  public function apiresponse($data=null, $message=null,$status=200)
   {
    $array=[
        'data'=>$data,
        'message' => $message,
        'status'=>$status,
      ];
      return response()->json($array,$status);
   }
}
