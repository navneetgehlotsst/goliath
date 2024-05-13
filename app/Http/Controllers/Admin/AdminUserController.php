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
    Prediction,
    CompetitionMatches,
    Transaction

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
            dd($user);
            // Fetch predictions with eager loading
            $datamatches = Prediction::with(['competitionMatch'])
                ->where('user_id', $id)
                ->groupBy('match_id')
                ->paginate(10);
            $transactions = Transaction::select('transactions.id','transactions.user_id','transactions.amount','transactions.transaction_id','transactions.transaction_type')->where('transactions.user_id',$id)->orderBy('id','desc')->get();
            if($user){
                return view('admin.users.show', compact('user','datamatches','transactions','getuserwallet'));
            }else{
                return redirect()->route('admin.users.index')->withError('User not found!');
            }

        }catch(Exception $e){
            return back()->withError($e->getMessage());
        }
    }

    public function matchPrediction($userid,$matchid){

        // Fetch match data, current innings data, and innings data for the match in a single query
        $matchData = CompetitionMatches::where('match_id', $matchid)
        ->with(['matchInnings', 'matchInnings.inningsOvers'])
        ->first();

        // Fetch prediction data with eager loading
        $predictionData = Prediction::with(['competitionMatch'])
        ->where('user_id', $userid)
        ->where('match_id', $matchid)
        ->first();

        // Check if prediction data exists
        if (!$predictionData) {
        return ApiResponse::errorResponse("Prediction data not found.");
        }

        // Transform data
        $transformedMatch = [
            "matchdetail" => [
                "id" => $predictionData->competitionMatch->id,
                "competiton_id" => $predictionData->competitionMatch->competiton_id,
                "match_id" => $predictionData->competitionMatch->match_id,
                "match" => $predictionData->competitionMatch->match,
                "match_no" => $predictionData->competitionMatch->subtitle,
                "short_title" => $predictionData->competitionMatch->teama_short_name . " vs " . $predictionData->competitionMatch->teamb_short_name,
                "status" => $predictionData->competitionMatch->status,
                "note" => $predictionData->competitionMatch->note,
                "match_start_date" => $predictionData->competitionMatch->match_start_date,
                "match_start_time" => $predictionData->competitionMatch->match_start_time,
                "formate" => $predictionData->competitionMatch->formate,
                "teama" => [
                    "team_id" => $predictionData->competitionMatch->teamaid,
                    "name" => $predictionData->competitionMatch->teama_name,
                    "short_name" => $predictionData->competitionMatch->teama_short_name,
                    "logo_url" => $predictionData->competitionMatch->teama_img,
                    "thumb_url" => $predictionData->competitionMatch->teama_img,
                    "scores_full" => $predictionData->competitionMatch->teamascorefull,
                    "scores" => $predictionData->competitionMatch->teamascore,
                    "overs" => $predictionData->competitionMatch->teamaover,
                ],
                "teamb" => [
                    "team_id" => $predictionData->competitionMatch->teambid,
                    "name" => $predictionData->competitionMatch->teamb_name,
                    "short_name" => $predictionData->competitionMatch->teamb_short_name,
                    "logo_url" => $predictionData->competitionMatch->teamb_img,
                    "thumb_url" => $predictionData->competitionMatch->teamb_img,
                    "scores_full" => $predictionData->competitionMatch->teambscorefull,
                    "scores" => $predictionData->competitionMatch->teambscore,
                    "overs" => $predictionData->competitionMatch->teambover,
                ],
                "innings" => [], // Initialize innings array
            ]
        ];

        // Iterate through each match inning
        foreach ($matchData->matchInnings as $match_inning) {
            // Determine the status of the inning
            $innings_status = ($match_inning->innings == $matchData->live_innings) ? "Ongoing" : (($match_inning->innings < $matchData->live_innings) ? "Completed" : "Upcoming");
            $overs = [];

            // Iterate through each inning over
            foreach ($match_inning->inningsOvers as $matchInningsOversvalue) {
                // Check if prediction exists for the over
                $over_status = Prediction::where('over_id', $matchInningsOversvalue->id)
                    ->where('user_id', $userid)
                    ->exists() ? "Predicted" : "Available";

                // Add over details only if there is a prediction
                if ($over_status === "Predicted") {
                    $overs[] = [
                        "over_id" => $matchInningsOversvalue->id,
                        "over_number" => $matchInningsOversvalue->overs,
                        "over_status" => $over_status
                    ];
                }
            }

            // Construct the innings array
            $transformedMatch['matchdetail']['innings'][] = [
                "inning_name" => $match_inning->innings . " Inning",
                "inning_status" => $innings_status,
                "overs" => $overs,
            ];
        }
        return view('admin.users.pridection_info', compact('transformedMatch','userid'));
    }

    public function predictionResult($userid,$matchid,$overid){

        // Retrieve predictions for the specified match and over for the current user
        $userPredictions = Prediction::select('predictions.question_id', 'predictions.over_id', 'predictions.answere as your_answer', 'predictions.result as your_result','questions.question')
        ->where('predictions.over_id', $overid)
        ->where('user_id', $userid)
        ->where('match_id', $matchid)
        ->join('questions', 'predictions.question_id', '=', 'questions.id')
        ->get();

        // Modify your_answer field to be 1 or 0 based on the string value
        $userPredictions->transform(function ($prediction) {
            $prediction->your_answer = $prediction->your_answer === "true" ? "yes" : "No";
            return $prediction;
        });

        // Store user predictions
        $predictedData['user_prediction'] = $userPredictions;


        // Count correct predictions
        $countResult = $userPredictions->where('your_result', 'W')->count();

        // Store count of correct predictions
        $predictedData['correct_counts'] = $countResult;

        // Winning Amount of correct predictions
        $predictedData['winning_amount'] = "100";

        return view('admin.users.pridection_question', compact('predictedData'));
    }

}
