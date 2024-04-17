<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use Mail,Hash,File,Auth,DB,Helper,Exception,Session,Redirect;
use Carbon\Carbon;
use App\Mail\ContactMail;

class QuestionsController extends Controller
{
    public function index()
    {
        return view('admin.questions.index');
    }

    public function getallquestions(Request $request){
        $questions = Question::orderBy('id','asc')->get();
        return response()->json(['data' => $questions]);
    }
}
