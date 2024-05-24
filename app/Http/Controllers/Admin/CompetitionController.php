<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{
    Competition,
};

class CompetitionController extends Controller
{
    public function live()
    {
        // Removing a value from the session
        Session::forget('previousURL');
        $CompetitionLiveData = Competition::where('status','live')->get();
        $titel = "Live";

        return view('admin.competition.index',compact('CompetitionLiveData','titel'));
    }


    public function completed()
    {
        // Removing a value from the session
        Session::forget('previousURL');
        $CompetitionLiveData = Competition::where('status','result')->get();
        $titel = "Completed";
        return view('admin.competition.index',compact('CompetitionLiveData','titel'));
    }


    public function upcoming()
    {
        // Removing a value from the session
        Session::forget('previousURL');
        $CompetitionLiveData = Competition::where('status','upcoming')->get();
        $titel = "Upcoming";
        return view('admin.competition.index',compact('CompetitionLiveData','titel'));
    }
}
