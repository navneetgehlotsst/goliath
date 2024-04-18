<?php

namespace App\Http\Response;

class ApiResponse
{

    public static  function errorResponse($message = null, $code = 200)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $code);
    }

    public static  function successResponse($data = null, $message = null, $code = 200)
    {
        if(!empty($data)){
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => $message,
            ], $code);
        }else{
            return response()->json([
                'status' => true,
                'message' => $message,
            ], $code);
        }
    }


    public static  function successResponsenoData($message = null, $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
        ], $code);

    }
}
