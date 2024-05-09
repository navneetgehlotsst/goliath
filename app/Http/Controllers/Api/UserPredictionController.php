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
    Transaction

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
            $matchid = $input['match_id'];
            $overid = $input['over_id'];
            $questionAns = $input['questionans'];
            $userWallet = $user->wallet;
            // $payamount = env('BETING_AMOUNT');

            // //check wallet Blance

            // if ($userWallet < $payamount) {
            //     return ApiResponse::errorResponse("Your Wallet balance is insufficient");
            // }

            $matchid = $input['match_id'];
            $overid = $input['over_id'];

            $datamatches = CompetitionMatches::where('match_id', $matchid)->first();
            //check Match
            if (!$datamatches) {
                return ApiResponse::errorResponse("Match not found");
            }

            // check status
            if ($datamatches->status === "Completed") {
                return ApiResponse::errorResponse("Match Completed, You can't predict here");
            }

            // $questionAns = json_decode($questionans, true);

            $datamatches = CompetitionMatches::where('match_id', $input['match_id'])->first();

            $live_innings = $datamatches->live_innings;

            $datamatchinnings = MatchInnings::where('match_id', $input['match_id'])->where('innings', $live_innings)->first();

            if ($datamatchinnings->current_overs != intval($datamatchinnings->current_overs)) {
                // Add 1 to the integer part
                $currentover = intval($datamatchinnings->current_overs) + 1 ?? 0;
            }else{
                $currentover = $datamatchinnings->current_overs ?? 0;
            }

            $checkOver = InningsOver::where('id', $overid)->first();

            $userpredictionOver = $checkOver->overs;

            if($currentover >= $userpredictionOver){
                return ApiResponse::errorResponse("Pridition time is Complete For this over");
            }


            $checkUserpredictedorNot = Prediction::where('user_id', $userId)->where('match_id', $matchid)->where('over_id', $overid)->first();
            //dd($checkUserpredictedorNot);

            if($checkUserpredictedorNot){
                return ApiResponse::errorResponse("You Have already given answere for this Question");
            }

            foreach ($questionAns as $questionAnskey => $questionAnsvalue) {
                $existingOverQuestion = OverQuestions::where('innings_over_id', $overid)->where('question_id', $questionAnsvalue['question_id'])->first();

                if($existingOverQuestion){
                    $questiondata = [
                        'user_id' => $userId,
                        'match_id' => $matchid,
                        'over_id' => $overid,
                        'question_id' => $questionAnsvalue['question_id'],
                        'answere' => $questionAnsvalue['answere'],
                    ];
                    // Create new Prediction
                    Prediction::create($questiondata);
                }else{
                    return ApiResponse::errorResponse("Question Not Found");
                    break;
                }
            }

            $message = "Predicted Succesfully";
            return ApiResponse::successResponsenoData($message);


        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }
    }

    public function listPredictions(Request $request){
        try {

            $userId = auth()->id();
            $datamatches = Prediction::with('competitionMatch')
                            ->where('predictions.user_id', $userId)
                            ->groupBy('predictions.match_id')
                            ->paginate(10);

                            // my pridiction data fetach//

                            foreach ($datamatches as $key => $match) {
                                $transformedMatch = [
                                    "id"=> $match['competitionMatch']->id,
                                    "competiton_id" => $match['competitionMatch']->competiton_id,
                                    "match_id" => $match['competitionMatch']->match_id,
                                    "match" => $match['competitionMatch']->match,
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
}
