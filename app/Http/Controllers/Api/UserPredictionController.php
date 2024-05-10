<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Mail,Hash,File,DB,Helper,Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use App\Models\{
    Prediction,
    OverQuestions,
    InningsOver,
    CompetitionMatches,
    MatchInnings,
    User,
    Transaction,
    Competition

};

use App\Http\Response\ApiResponse;

class UserPredictionController extends Controller
{
    private $token = 'dbe24b73486a731d9fa8aab6c4be02ef';

    private function makeCurlRequest($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }
    //User Pridition Function
    public function saveUserPrediction(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'match_id' => 'required',
                'over_id' => 'required',
                'questionans' => 'required',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors()->first();
                return ApiResponse::errorResponse($message);
            }

            $user = auth()->user();
            $userId = $user->id;
            $matchId = $input['match_id'];
            $overId = $input['over_id'];
            $questionAns = $input['questionans'];
            $userWallet = $user->wallet;

            // Check if match exists
            $match = CompetitionMatches::where('match_id', $matchId)->first();
            if (!$match) {
                return ApiResponse::errorResponse("Match not found");
            }

            // Check match status
            if ($match->status === "Completed") {
                return ApiResponse::errorResponse("Match Completed, You can't predict here");
            }

            // Check wallet balance (if needed)
            // $payAmount = env('BETING_AMOUNT');
            // if ($userWallet < $payAmount) {
            //     return ApiResponse::errorResponse("Your Wallet balance is insufficient");
            // }

            // Get innings data
            $innings = MatchInnings::where('match_id', $matchId)->where('innings', $match->live_innings)->first();
            $currentOver = $innings ? $innings->current_overs : 0;

            // Check if prediction time is complete for this over
            $over = InningsOver::find($overId);
            if (!$over || $currentOver >= $over->overs) {
                return ApiResponse::errorResponse("Prediction time is complete for this over");
            }

            // Check if user has already predicted for this question
            if (Prediction::where('user_id', $userId)->where('match_id', $matchId)->where('over_id', $overId)->exists()) {
                return ApiResponse::errorResponse("You have already given an answer for this question");
            }

            // Iterate over question answers
            foreach ($questionAns as $question) {
                $existingQuestion = OverQuestions::where('innings_over_id', $overId)
                                                ->where('question_id', $question['question_id'])
                                                ->exists();

                if ($existingQuestion) {
                    Prediction::create([
                        'user_id' => $userId,
                        'match_id' => $matchId,
                        'over_id' => $overId,
                        'question_id' => $question['question_id'],
                        'answer' => $question['answere'],
                    ]);
                } else {
                    return ApiResponse::errorResponse("Question not found");
                }
            }

            $message = "Predicted successfully";
            return ApiResponse::successResponseNoData($message);
        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }
    }

    public function listPredictions(Request $request){
        try {
            $userId = auth()->id();

            // Fetch predictions with eager loading
            $datamatches = Prediction::with(['competitionMatch'])
                ->where('user_id', $userId)
                ->groupBy('match_id')
                ->paginate(10);


                foreach ($datamatches as $key => $match) {
                    $datamatchescomp = Competition::where('competiton_id', $match['competitionMatch']->competiton_id)->first();
                    $transformedMatch = [
                            "id"=> $match->id,
                            "competiton_id" => $match['competitionMatch']->competiton_id,
                            "competiton_name" => $datamatchescomp->title,
                            "match_id" => $match['competitionMatch']->match_id,
                            "match" => $match['competitionMatch']->match,
                            "match_no" => $match['competitionMatch']->subtitle,
                            "short_title" => $match['competitionMatch']->teama_short_name . " vs " . $match['competitionMatch']->teamb_short_name,
                            "status" => $match['competitionMatch']->status,
                            "note" => $match['competitionMatch']->note,
                            "match_start_date" => $match['competitionMatch']->match_start_date,
                            "match_start_time" => $match['competitionMatch']->match_start_time,
                            "formate" => $match['competitionMatch']->formate,
                            "teama" => [
                                "team_id" => $match['competitionMatch']->teamaid, // Set team ID if available, otherwise null.
                                "name" => $match['competitionMatch']->teama_name,
                                "short_name" => $match['competitionMatch']->teama_short_name,
                                "logo_url" => $match['competitionMatch']->teama_img,
                                "thumb_url" => $match['competitionMatch']->teama_img,
                                "scores_full" => $match['competitionMatch']->teamascorefull, // Set scores if available.
                                "scores" => $match['competitionMatch']->teamascore, // Set scores if available.
                                "overs" => $match['competitionMatch']->teamaover, // Set overs if available.
                            ],
                            "teamb" => [
                                "team_id" => $match['competitionMatch']->teambid, // Set team ID if available, otherwise null.
                                "name" => $match['competitionMatch']->teamb_name,
                                "short_name" => $match['competitionMatch']->teamb_short_name,
                                "logo_url" => $match['competitionMatch']->teamb_img,
                                "thumb_url" => $match['competitionMatch']->teamb_img,
                                "scores_full" => $match['competitionMatch']->teambscorefull, // Set scores if available.
                                "scores" => $match['competitionMatch']->teambscore, // Set scores if available.
                                "overs" => $match['competitionMatch']->teambover, // Set overs if available.
                            ],
                            "innings" => [] // Assuming no detailed innings information is available initially.
                    ];

                    $datamatches[$key] = $transformedMatch;
                }
                $matchesdata['matchlist'] = $datamatches;
                // Now $transformedMatches contains the transformed data in the desired format.



            return $datamatches->count()
                ? ApiResponse::successResponse($matchesdata, "Matches Data Found")
                : ApiResponse::errorResponse("Matches Data Not Found");

        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }
    }

    public function predicted_over(Request $request){
        try {

            $input = $request->all();

            // Validate input
            $validator = Validator::make($input, ['match_id' => 'required']);
            if ($validator->fails()) {
                return ApiResponse::errorResponse($validator->errors()->first());
            }

            $user = Auth::user();
            $userId = $user->id;

            // Fetch match data, current innings data, and innings data for the match in a single query
            $matchData = CompetitionMatches::where('match_id', $input['match_id'])
                ->with(['matchInnings', 'matchInnings.inningsOvers'])
                ->first();

            if (!$matchData) {
                return ApiResponse::errorResponse(null, "Match Data Not Found");
            }

            // Fetch prediction data with eager loading
            $predictionData = Prediction::with(['competitionMatch'])
                ->where('user_id', $userId)
                ->where('match_id', $input['match_id'])
                ->first();

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

            foreach ($matchData->matchInnings as $match_inning) {
                $innings_status = ($match_inning->innings == $matchData->live_innings) ? "Ongoing" : (($match_inning->innings < $matchData->live_innings) ? "Completed" : "Upcoming");
                $overs = [];

                foreach ($match_inning->inningsOvers as $matchInningsOversvalue) {
                    $over_status = Prediction::where('over_id', $matchInningsOversvalue->id)
                        ->where('user_id', $userId)
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
            return ApiResponse::successResponse($transformedMatch, "Matches Data Found"); // Simplified response message

        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }

    }

    public function predicted_result(Request $request){
        try {

            // Extracting necessary input fields from the request
            $input = $request->only(['match_id', 'over_id']);

            // Validate input
            $validator = Validator::make($input, [
                'match_id' => 'required',
                'over_id' => 'required'
            ]);
            if ($validator->fails()) {
                // Return error response if validation fails
                return ApiResponse::errorResponse($validator->errors()->first());
            }

            // Fetch current user
            $user = Auth::user();
            $userId = $user->id;

            // Retrieve predictions for the specified match and over for the current user
            $userPredictions = Prediction::select('predictions.question_id', 'predictions.over_id', 'predictions.answere as your_answer', 'predictions.result as your_result','questions.question')
                ->where('predictions.over_id', $input['over_id'])
                ->where('user_id', $userId)
                ->where('match_id', $input['match_id'])
                ->join('questions', 'predictions.question_id', '=', 'questions.id')
                ->get();

            // Modify your_answer field to be 1 or 0 based on the string value
            $userPredictions->transform(function ($prediction) {
                $prediction->your_answer = $prediction->your_answer === "true" ? 1 : 0;
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

            // Message for result
            $message = $countResult >= 5 ? "You are a winner" : "You have lost this time";

            // Return success response with prediction data
            return ApiResponse::successResponse($predictedData, $message);
        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }

    }
}
