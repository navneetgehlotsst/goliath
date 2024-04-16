<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HowToPlay;
use Mail,Hash,File,Auth,DB,Helper,Exception,Session,Redirect;
use Carbon\Carbon;
use App\Mail\ContactMail;

class HowToPlayController extends Controller
{
    public function index()
    {
        return view('admin.howtoplay.index');
    }

    public function getallhowtoplay(Request $request){
        $howtoplay = HowToPlay::orderBy('id','desc')->get();
        return response()->json(['data' => $howtoplay]);
    }

    public function destroy($id)
    {

        try{
            HowToPlay::where('id',$id)->delete();
            return response()->json([
                'success' => 'success',
                'message' => 'deleted successfully',
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

    }


}
