<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Mail, Hash, File, Auth, DB, TimeHelper, Exception, Session, Redirect, Validator ,Helper;
use Carbon\Carbon;
use App\Models\HowToPlay;



class HowToPlayController extends Controller
{
    public function howToPlay(Request $request){

        try{
            $howtoplay = HowToPlay::select('id','title')->orderBy('id','desc')->get();
            return response()->json([
                'status' => true,
                'message' => 'How to Play Found',
                'data' => $howtoplay
            ],200);
        }catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ],200);
        }
    }
}
