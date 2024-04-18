<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Mail, Hash, File, Auth, DB, TimeHelper, Exception, Session, Redirect, Validator ,Helper;
use Carbon\Carbon;
use App\Models\HowToPlay;

// Api Responce
use App\Http\Response\ApiResponse;



class HowToPlayController extends Controller
{
    public function howToPlay(Request $request){

        try{
            $howtoplay = HowToPlay::select('id','title')->orderBy('id','desc')->get();
            // return response()->json([
            //     'status' => true,
            //     'message' => 'How to Play Found',
            //     'data' => $howtoplay
            // ],200);
            $data['how_to_paly'] = $howtoplay;
            $message = 'How to Play Found';
            return ApiResponse::successResponse($data, $message);
        }catch (Exception $e) {
            $message = $e->getMessage();
            return ApiResponse::errorResponse($message);
        }
    }
}
