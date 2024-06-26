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
    Competition,
    Overballes

};

use App\Http\Response\ApiResponse;

class UserPredictionController extends Controller
{
    
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
            $payAmount = env('BETING_AMOUNT');
            if ($userWallet < $payAmount) {
                return ApiResponse::errorResponse("Your Wallet balance is insufficient",['is_wallet_recharge'=>true]);
            }

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
                
                if($question['answere'] == 1){
                    $answer = "true";
                }else{
                    $answer = "false";
                }
                
                $existingQuestion = OverQuestions::where('innings_over_id', $overId)->where('question_id', $question['question_id'])->exists();
                if ($existingQuestion) {
                    Prediction::create([
                        'user_id' => $userId,
                        'match_id' => $matchId,
                        'over_id' => $overId,
                        'question_id' => $question['question_id'],
                        'answere' => $answer,
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
            ->orderBy('created_at','DESC')
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
                        "pridiction_amount" => env('BETING_AMOUNT'),
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

            $datamatchescomp = Competition::where('competiton_id', $predictionData->competitionMatch->competiton_id)
                ->first();

            // Transform data
            $transformedMatch = [
                "matchdetail" => [
                    "id" => $predictionData->competitionMatch->id,
                    "competiton_id" => $predictionData->competitionMatch->competiton_id,
                    "competiton_name" => $datamatchescomp->title,
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
            $userPredictions = Prediction::select('predictions.question_id', 'predictions.over_id', 'predictions.answere as your_answer', 'predictions.result as your_result','questions.question', 'competition_matches.status as match_status')
                ->where('predictions.over_id', $input['over_id'])
                ->where('predictions.user_id', $userId)
                ->where('predictions.match_id', $input['match_id'])
                ->join('questions', 'predictions.question_id', '=', 'questions.id')
                ->join('competition_matches', 'predictions.match_id', '=', 'competition_matches.match_id')
                ->get();

            if($userPredictions[0]->your_result == "ND"){
                $predictedData['result_message'] = "Result Not Declared.";
                $predictedData['is_result'] = false;
            }else if($userPredictions[0]->your_result == "NR"){
                $predictedData['result_message'] = "Result Not Declared! Due to over incomplete.";
                $predictedData['cancel_message'] = "Match ended early. Full amount will be credited to your wallet.";
                $predictedData['is_result'] = true;
                $predictedData['is_cancelled'] = true;
            }else{
                $predictedData['result_message'] = "Result Declared.";
                $predictedData['is_result'] = true;
            }

            // if($userPredictions[0]->match_status == "Completed" || $userPredictions[0]->match_status == "Cancelled"){
            //     $predictedData['is_cancelled'] = true;
            //     $predictedData['result_message'] = "Result Not Declared! Due to over incomplete.";
            //     $predictedData['cancel_message'] = "Match ended early. Full amount will be credited to your wallet.";
            // }
            
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
            $winningAmount = "100";
            $predictedData['winning_amount'] = $winningAmount;
            $predictedData['winning_message'] = "";
            if($countResult >= 5){
                $predictedData['winning_message'] = env('CURRENCY_SYMBOL').$winningAmount." will be transferred to your wallet.";
            }

            // Message for result
            $predictedData['message'] = $message = $countResult >= 5 ? "You are a winner" : "You have lost this time";

            // Return success response with prediction data
            return ApiResponse::successResponse($predictedData, $message);
        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }

    }





    public function testresult()
    {
        try {
            $compdata = Competition::where('status', 'like', 'live')->get();
            $token = 'dbe24b73486a731d9fa8aab6c4be02ef';

            // Fetch live matches outside the loop
            $matchesData = CompetitionMatches::where('status', 'like', 'live')->get();

            foreach ($compdata as $value) {
                $cId = $value->competiton_id;

                foreach ($matchesData as $key => $matche) {
                    $matchid = $matche->match_id;
                    $apimatchlive = "https://rest.entitysport.com/v2/matches/$matchid/live?token=$token";

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $apimatchlive,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                    ));
                    $matchresponse = curl_exec($curl);
                    curl_close($curl);

                    $matchresponsedata = json_decode($matchresponse, true);
                    $matchesData = $matchresponsedata['response'];

                    // Update live innings in CompetitionMatches table
                    CompetitionMatches::where('match_id', $matchid)->update(['live_innings' => $matchesData['live_inning_number']]);

                    // Update match innings in MatchInnings table
                    $matchData = [
                        'current_score' => $matchesData['live_score']['runs'],
                        'current_wicket' => $matchesData['live_score']['wickets'],
                        'current_overs' => $matchesData['live_score']['overs']
                    ];
                    MatchInnings::where('match_id', $matchid)->where('innings', $matchesData['live_inning_number'])->update($matchData);

                    // Prepare ball events for batch insert
                    $ballEvents = [];

                    if (isset($matchesData['commentaries'])) {
                        foreach ($matchesData['commentaries'] as $commentarieskey => $commentariesvalue) {

                            if ($commentariesvalue['event'] == 'ball') {

                                 $eventId = $commentariesvalue['event_id'];

                                 $overBallCheck = Overballes::where('eventid', $eventId)->first();

                                if (empty($overBallCheck)) {
                                    $ballevent = [
                                        "eventid" => $commentariesvalue['event_id'],
                                        "match_id" => $matchid,
                                        "innings" => $matchesData['live_inning_number'],
                                        "over_no" => $commentariesvalue['over'],
                                        "ball_no" => $commentariesvalue['ball'],
                                        "score" => $commentariesvalue['score'],
                                        "noball_dismissal" => ($commentariesvalue['noball_dismissal'] == 1) ? "1" : "0",
                                        "run" => $commentariesvalue['run'],
                                        "noball_run" => $commentariesvalue['noball_run'],
                                        "wide_run" => $commentariesvalue['wide_run'],
                                        "bye_run" => $commentariesvalue['bye_run'],
                                        "legbye_run" => $commentariesvalue['legbye_run'],
                                        "bat_run" => $commentariesvalue['bat_run'],
                                        "noball" => ($commentariesvalue['noball'] == 1) ? "1" : "0",
                                        "wideball" => ($commentariesvalue['wideball'] == 1) ? "1" : "0",
                                        "six" => ($commentariesvalue['six'] == 1) ? "1" : "0",
                                        "four" => ($commentariesvalue['four'] == 1) ? "1" : "0",
                                    ];
                                    $ballEvents[] = $ballevent;
                                }
                            }


                        }
                        // // Batch insert ball events
                        Overballes::insert($ballEvents);
                    }

                    // // Get Prediction data outside the loop
                    $predictiondata = Prediction::select('predictions.id', 'predictions.user_id', 'predictions.match_id', 'predictions.question_id', 'predictions.over_id', 'predictions.answere', 'predictions.status', 'predictions.result', 'questions.question_constant', 'innings_overs.overs')
                        ->where('predictions.result', '=', 'ND')
                        ->where('predictions.status', '=', 'pending')
                        ->where('predictions.match_id', '=', $matchid)
                        ->join('questions', 'predictions.question_id', '=', 'questions.id')
                        ->join('innings_overs', 'predictions.over_id', '=', 'innings_overs.id')
                        ->get();

                        foreach ($predictiondata as $predictionkey => $predictionvalue) {
                            if ($predictionvalue->overs < $matchesData['live_score']['overs']) {

                                $type = $predictionvalue->question_constant;
                                $over = $predictionvalue->overs - 1;
                                $answer = $predictionvalue->answere;
                                $predictionid = $predictionvalue->id;
                                $firstBallScore = Overballes::where('match_id' , $matchid)->where('innings' , $matchesData['live_inning_number'])->where('over_no' , $over)->where('ball_no' , '1')->first();

                                $returnresult = Helper::QuestionType($type, $matchid, $matchesData['live_inning_number'], $over);
                                \Log::info($returnresult);
                                if ($answer == $returnresult) {
                                    $result = "W";
                                } else {
                                    $result = "L";
                                }

                                Prediction::where('id', $predictionid)->update(['result' => $result]);
                            }
                        }
                }
            }
            \Log::error("score updated");
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }
    }

    public function croncheck()
    {
        try {
            // API Token
            $token = 'c16eaec3abd49e3477yy567836a95ad43';
            $matchesData = CompetitionMatches::whereIn('status', ['Scheduled','Live'])->get();
            dd($matchesData);

            // If there are live and Completed matches
            if(!empty($matchesData)){
                foreach ($matchesData as $key => $match) {
                    $matchid = $match->match_id;

                    // API URL for live match data
                    $apimatchlive = "https://rest.entitysport.com/sandbox/cricket/matches/$matchid/live?token=$token";

                    // Curl Request
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $apimatchlive,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                    ));
                    $matchresponse = curl_exec($curl);
                    curl_close($curl);

                    // Decode API response
                    $matchresponsedata = json_decode($matchresponse, true);
                    $matchesData = $matchresponsedata['response'];

                    // If API response is successful
                    if ($matchresponsedata['status'] == 'ok') {
                        if($matchresponsedata['response'] != "Data unavailable"){
                            // Update live Data in CompetitionMatches table
                            if($match->teama_name == $matchesData['team_batting']){
                                $competitionmatchDatainsert = [
                                    'teamascorefull' => $matchesData['live_score']['runs'] .'/'. $matchesData['live_score']['wickets'],
                                    'teamascore' => $matchesData['live_score']['wickets'],
                                    'teamaover' => $matchesData['live_score']['overs'],
                                    'live_innings' => $matchesData['live_inning_number'],
                                    'status' => $matchesData['status_str']
                                ];
                            } else {
                                $competitionmatchDatainsert = [
                                    'teambscorefull' => $matchesData['live_score']['runs'] .'/'. $matchesData['live_score']['wickets'],
                                    'teambscore' => $matchesData['live_score']['wickets'],
                                    'teambover' => $matchesData['live_score']['overs'],
                                    'live_innings' => $matchesData['live_inning_number'],
                                    'status' => $matchesData['status_str']
                                ];
                            }

                            // Update CompetitionMatches table
                            CompetitionMatches::where('match_id', $matchid)->update($competitionmatchDatainsert);

                            // Update match innings in MatchInnings table
                            $matchDatainsert = [
                                'current_score' => $matchesData['live_score']['runs'],
                                'current_wicket' => $matchesData['live_score']['wickets'],
                                'current_overs' => $matchesData['live_score']['overs']
                            ];
                            MatchInnings::where('match_id', $matchid)->where('innings', $matchesData['live_inning_number'])->update($matchDatainsert);

                            // Prepare ball events for batch insert
                            $ballEvents = [];
                            if (isset($matchesData['commentaries'])) {
                                foreach ($matchesData['commentaries'] as $commentarieskey => $commentariesvalue) {
                                    // Over by ball data insert
                                    if ($commentariesvalue['event'] == 'ball') {
                                        $eventId = $commentariesvalue['event_id'];
                                        $overBallCheck = Overballes::where('eventid', $eventId)->first();
                                        if (empty($overBallCheck)) {
                                            $ballevent = [
                                                "eventid" => $commentariesvalue['event_id'],
                                                "match_id" => $matchid,
                                                "innings" => $matchesData['live_inning_number'],
                                                "over_no" => $commentariesvalue['over'],
                                                "ball_no" => $commentariesvalue['ball'],
                                                "score" => $commentariesvalue['score'],
                                                "noball_dismissal" => ($commentariesvalue['noball_dismissal'] == 1) ? "1" : "0",
                                                "run" => $commentariesvalue['run'],
                                                "noball_run" => $commentariesvalue['noball_run'],
                                                "wide_run" => $commentariesvalue['wide_run'],
                                                "bye_run" => $commentariesvalue['bye_run'],
                                                "legbye_run" => $commentariesvalue['legbye_run'],
                                                "bat_run" => $commentariesvalue['bat_run'],
                                                "noball" => ($commentariesvalue['noball'] == 1) ? "1" : "0",
                                                "wideball" => ($commentariesvalue['wideball'] == 1) ? "1" : "0",
                                                "six" => ($commentariesvalue['six'] == 1) ? "1" : "0",
                                                "four" => ($commentariesvalue['four'] == 1) ? "1" : "0",
                                            ];
                                            $ballEvents[] = $ballevent;
                                        }
                                    }
                                }

                                // Batch insert ball events
                                Overballes::insert($ballEvents);
                            }

                            // Get Prediction data
                            $predictiondata = Prediction::select('predictions.id', 'predictions.user_id', 'predictions.match_id', 'predictions.question_id', 'predictions.over_id', 'predictions.answere', 'predictions.status', 'predictions.result', 'questions.question_constant', 'innings_overs.overs')
                                ->where('predictions.result', '=', 'ND')
                                ->where('predictions.status', '=', 'pending')
                                ->where('predictions.match_id', '=', $matchid)
                                ->join('questions', 'predictions.question_id', '=', 'questions.id')
                                ->join('innings_overs', 'predictions.over_id', '=', 'innings_overs.id')
                                ->get();
                            if($predictiondata){
                                foreach ($predictiondata as $predictionkey => $predictionvalue) {
                                    if ($predictionvalue->overs < $matchesData['live_score']['overs']) {
                                        $type = $predictionvalue->question_constant;
                                        $over = $predictionvalue->overs;
                                        $answer = $predictionvalue->answere;
                                        $predictionid = $predictionvalue->id;

                                        // Call Helper function to get prediction result
                                        $returnresult = Helper::QuestionType($type, $matchid, $matchesData['live_inning_number'], $over);
                                        if ($answer == $returnresult) {
                                            $result = "W";
                                        } else {
                                            $result = "L";
                                        }

                                        // Update prediction result
                                        Prediction::where('id', $predictionid)->update(['result' => $result]);
                                    }
                                }
                            }
                        }else{
                            \Log::error("Api Not Respomding");
                        }
                    } else {
                        // Log error if API not working
                        \Log::error("Api Not Working");
                        break;
                    }
                }
            }
            // Log success message after updating scores
            \Log::error("score updated");

        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }
    }

}
