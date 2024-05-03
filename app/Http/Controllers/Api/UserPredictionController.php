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
    CompetitionMatches

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

            $userId = auth()->id();
            $matchid = $input['match_id'];
            $overid = $input['over_id'];
            $questionans = $input['questionans'];
            $questionAns = json_decode($questionans, true);

            $matchdatalive = $this->makeCurlRequest("https://rest.entitysport.com/v2/matches/{$input['match_id']}/live/?token={$this->token}")['response'];

            $currentover = $matchdatalive['live_score']['overs'] ?? "0";

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



    public function getUserPrediction(Request $request){
        try {
            $input = $request->validate([
                'over_id' => 'required',
                'match_id' => 'required',
            ]);
            $datamatches = CompetitionMatches::where('match_id', $input['match_id'])->first();
            $matchdata = $this->makeCurlRequest("https://rest.entitysport.com/v2/matches/{$input['match_id']}/scorecard/?token={$this->token}")['response'];
            $userprediction = Prediction::select('predictions.question_id','predictions.over_id','predictions.answere','questions.question','innings_overs.overs')
                ->where('predictions.over_id', $input['over_id'])
                ->join('questions', 'predictions.question_id', '=', 'questions.id')
                ->join('innings_overs', 'predictions.over_id', '=', 'innings_overs.id')
                ->get();

                $transformedMatch['matchdetail'] = [
                    "id"=> $datamatches->id,
                    "competiton_id" => $datamatches->competiton_id,
                    "match_id" => $datamatches->match_id,
                    "match" => $datamatches->match,
                    "short_title" => $datamatches->teama_short_name . " vs " . $datamatches->teamb_short_name,
                    "status" => $datamatches->status,
                    "note" => $datamatches->note,
                    "match_start_date" => $datamatches->match_start_date,
                    "match_start_time" => $datamatches->match_start_time,
                    "formate" => $datamatches->formate,
                    "teama" => [
                        "team_id" => $datamatches->teamaid, // Set team ID if available, otherwise null.
                        "name" => $datamatches->teama_name,
                        "short_name" => $datamatches->teama_short_name,
                        "logo_url" => $datamatches->teama_img,
                        "thumb_url" => $datamatches->teama_img,
                        "scores_full" => $datamatches->teamascorefull, // Set scores if available.
                        "scores" => $datamatches->teamascore, // Set scores if available.
                        "overs" => $datamatches->teamaover, // Set overs if available.
                    ],
                    "teamb" => [
                        "team_id" => $datamatches->teambid, // Set team ID if available, otherwise null.
                        "name" => $datamatches->teamb_name,
                        "short_name" => $datamatches->teamb_short_name,
                        "logo_url" => $datamatches->teamb_img,
                        "thumb_url" => $datamatches->teamb_img,
                        "scores_full" => $datamatches->teambscorefull, // Set scores if available.
                        "scores" => $datamatches->teambscore, // Set scores if available.
                        "overs" => $datamatches->teambover, // Set overs if available.
                    ],
                    "overnumber" => $userprediction->isEmpty() ? '0' : $userprediction->first()->overs,
                    "question" => $userprediction,
                ];

                if($transformedMatch){
                    return ApiResponse::successResponse($transformedMatch,"Question Found");
                }else{
                    return ApiResponse::errorResponse("Question Not Found");
                }

        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }

    }
}
