<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail,Hash,File,Auth,DB,Helper,Exception,Session,Redirect;
use App\Models\{
    Competition,
};

class CompetitionController extends Controller
{
    public function live()
    {
        try {
            // Removing a value from the session
            Session::forget('previousURL');
            $CompetitionLiveData = Competition::where('status','live')->get();
            $titel = "Live";

            return view('admin.competition.index',compact('CompetitionLiveData','titel'));
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }


    public function completed()
    {
        try {
            // Removing a value from the session
            Session::forget('previousURL');
            $CompetitionLiveData = Competition::where('status','result')->get();
            $titel = "Completed";
            return view('admin.competition.index',compact('CompetitionLiveData','titel'));
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }


    public function upcoming()
    {
        try {
            // Removing a value from the session
            Session::forget('previousURL');
            $CompetitionLiveData = Competition::where('status','upcoming')->get();
            $titel = "Upcoming";
            return view('admin.competition.index',compact('CompetitionLiveData','titel'));
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }
}
