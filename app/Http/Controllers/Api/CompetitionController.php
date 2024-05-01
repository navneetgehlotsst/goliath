<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Mail,Hash,File,DB,Helper,Auth;
use App\Models\User;
use App\Models\CheckOtp;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use App\Models\Competition;


// Api Responce
use App\Http\Response\ApiResponse;

class CompetitionController extends Controller
{
    public function competitionList(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'status' => 'required|in:live,upcoming',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors()->first();
                return ApiResponse::errorResponse($message);
            }
            $status = $request->status;
            $datacomp = Competition::where('status', $status)->paginate(10);

            if($datacomp){
                $message = "Competition Data Found";
                return ApiResponse::successResponse($datacomp, $message);
            }else{
                return ApiResponse::errorResponse("Competition Data Not Found");
            }



        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }

    }
}
