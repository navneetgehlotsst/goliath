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
    InningsOver

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


            $checkUserpredictedorNot = Prediction::where('user_id', $userId)->where('match_id', $matchid)->where('over_id', $overid)->get();

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

            $matchdata = $this->makeCurlRequest("https://rest.entitysport.com/v2/matches/{$input['match_id']}/scorecard/?token={$this->token}")['response'];
            $datamatches = Prediction::select('predictions.question_id','predictions.over_id','predictions.answere','questions.question','innings_overs.overs')
                ->where('predictions.over_id', $input['over_id'])
                ->join('questions', 'predictions.question_id', '=', 'questions.id')
                ->join('innings_overs', 'predictions.over_id', '=', 'innings_overs.id')
                ->get();

            return ApiResponse::successResponse([
                "match" => $matchdata['title'],
                "short_title" => $matchdata['short_title'],
                "status"  => $matchdata['status_str'],
                "note"  => $matchdata['status_note'],
                "datetime"  => $matchdata['date_start'],
                "teama"  => $matchdata['teama'],
                "teamb"  => $matchdata['teamb'],
                "overnumber" => $datamatches->isEmpty() ? '0' : $datamatches->first()->overs,
                "question" => $datamatches,
            ], $datamatches->isEmpty() ? "Question Not Found" : "Question Data Found");


        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }

    }
}
