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
    Transaction,
    Winning

};

class AdminUserController extends Controller
{
    //========================= User Member Funcations ========================//

    public function index() {
        try {
            // Removing a value from the session
            Session::forget('previousURL');
            $users = User::where('role', 'user')->orderBy('id', 'desc')->get();
            return view('admin.users.index',compact('users'));
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
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
        try {
            $user = Auth::user();
            return view('web.auth.profile',compact('user'));
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }

    public function show($id) {
        try {
            // Retrieve user and handle the case where user is not found
            $user = User::findOrFail($id);

            // Retrieve predictions with eager loading and pagination
            $datamatches = Prediction::with('competitionMatch')
                ->where('user_id', $id)
                ->groupBy('match_id')
                ->paginate(10);

            // Retrieve transactions and calculate totals using SQL
            $transactions = Transaction::select('id', 'user_id', 'amount', 'payment_id', 'transaction_type', 'created_at')
                ->where('user_id', $id)
                ->orderBy('id', 'desc')
                ->get();

            $transactionscount = $transactions->count();

            // Calculate transaction types totals
            $transactionTypes = Transaction::where('user_id', $id)
                ->selectRaw('transaction_type, SUM(amount) as total')
                ->groupBy('transaction_type')
                ->pluck('total', 'transaction_type')
                ->toArray();

            // Ensure all keys are present in the $transactionTypes array
            $transactionTypes = array_merge([
                'add-wallet' => 0,
                'pay' => 0,
                'winning-amount' => 0,
                'withdrawal-amount' => 0
            ], $transactionTypes);

            // Retrieve winnings with eager loading
            $datawinning = Winning::with(['competitionMatch', 'inningsOvers'])
                ->where('user_id', $id)
                ->get();

            // Return the view with the compacted data
            return view('admin.users.show', compact('user', 'datamatches', 'transactions', 'transactionTypes', 'transactionscount', 'datawinning'));

        } catch (Exception $e) {
            // Log the exception message for debugging purposes
            Log::error('Error fetching user data: ' . $e->getMessage());
            return back()->withError('An error occurred while fetching user data.');
        }
    }

    public function matchPrediction($userid,$matchid){
        try {
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
            return view('admin.users.pridection_info', compact('transformedMatch','userid','matchid'));
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }

    public function predictionResult($userid,$matchid,$overid){
        try {
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

            return view('admin.users.pridection_question', compact('predictedData','userid','matchid'));
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }

}
