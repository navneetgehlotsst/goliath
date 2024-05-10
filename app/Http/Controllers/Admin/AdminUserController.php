<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Mail,Hash,File,Auth,DB,Helper,Exception,Session,Redirect,Validator;
use Carbon\Carbon;

use App\Models\{
    User,
    Notification,
    NotificationUser,
    Prediction

};

class AdminUserController extends Controller
{
    //========================= User Member Funcations ========================//

    public function index() {
        $users = User::where('role', 'user')->orderBy('id', 'desc')->get();
        return view('admin.users.index',compact('users'));
    }

    public function userStatus(Request $request) {
        try
        {
            $userid = $request->userid;
            $status = $request->status;
            $user = User::find($userid);
            $user->status = $status;
            $user->save();
            return response()->json(['success' => true]);

        }catch(Exception $e){
            return response()->json(['success' => false,'message' => $e->getMessage()]);
        }
    }


    public function profile(){
        $user = Auth::user();
        return view('web.auth.profile',compact('user'));
    }

    public function show($id) {
        try
        {
            $user = User::find($id);
            // Fetch predictions with eager loading
            $datamatches = Prediction::with(['competitionMatch'])
                ->where('user_id', $id)
                ->groupBy('match_id')
                ->paginate(10);
            if($user){
                return view('admin.users.show', compact('user','datamatches'));
            }else{
                return redirect()->route('admin.users.index')->withError('User not found!');
            }

        }catch(Exception $e){
            return back()->withError($e->getMessage());
        }
    }



    public function matchPrediction($userid,$matchid){
        echo $userid;
        echo $matchid;
    }



}
