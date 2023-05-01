<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function userID()
    {
        try {
            return Auth::user()->id;
        } catch (\Exception $e) {
            return 0;
        }
    }
    protected function respondWithToken($token,$data = [])
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
//            'expires_in' => Auth::factory()->getTTL() * 60, //for testing purpose, later change to 60 =>1hr
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'res'=>$data
        ], 200);
    }

    protected function respondWithStatus($status,$message,$data = [],$header_status = 200){

        try {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'res' =>  $data//)->makeHidden($hiddenAttributes)
            ], $header_status);

        } catch (\Exception $e) {
//            later log error
//            dd($e->getMessage());
            return response()->json([
                "status" => -99,
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    protected function validateRequests($request, $rules = []){

        $validator = \Validator::make($request->except(['_token']), $rules);

        if($validator->fails()){
            return ['passed'=>false,'message'=>implode(",", $validator->messages()->all())];
        }
        else{
            return ['passed'=>true];
        }
    }
}
