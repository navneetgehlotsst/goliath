<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{
    Competition,
};

class CompetitionController extends Controller
{
    public function index()
    {
        $CompetitionLiveData = Competition::where('status','live')->get();

        return view('admin.competition.index',compact('CompetitionLiveData'));
    }
}
