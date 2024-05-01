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
    Question,
    MatchInnings,
    InningsOver,
    OverQuestions,
    CompetitionMatches,
    User

};

// Api Responce
use App\Http\Response\ApiResponse;

class MatchesController extends Controller
{
    public function matchesList(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'status' => 'required|in:Live,Scheduled,Completed',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors()->first();
                return ApiResponse::errorResponse($message);
            }
            $datamatches = CompetitionMatches::where('status', $input['status'])->orderBy('match_start_date', 'ASC')->orderBy('match_start_time', 'ASC')->paginate(10);

            if($datamatches){
                $message = "Matches Data Found";
                return ApiResponse::successResponse($datamatches, $message);
            }else{
                return ApiResponse::errorResponse("Matches Data Not Found");
            }


        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }

    }



    public function matchesDetail(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'match_id' => 'required',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors()->first();
                return ApiResponse::errorResponse($message);
            }

            $oversinningsone = [];
            $oversinningstwo = [];

            $token = 'dbe24b73486a731d9fa8aab6c4be02ef';

            // Function to make curl requests
            function makeCurlRequest($url) {
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
            $id = $input['match_id'];
            // Get match data
            $matchdata = makeCurlRequest("https://rest.entitysport.com/v2/matches/$id/scorecard/?token=$token")['response'];

            // Get live match data
            $matchdatalive = makeCurlRequest("https://rest.entitysport.com/v2/matches/$id/live/?token=$token")['response'];

            $currentover = $matchdatalive['live_score']['overs'] ?? "0";

            // Calculate current over final
            $currentoverfinal = ($currentover != floor($currentover)) ? ceil($currentover) : $currentover;

            $nextover = $currentoverfinal + 3;

            $matchInningsData = MatchInnings::where('match_innings.match_id', $input['match_id'])->get();


            $inningsone = [];
            $over = [];
            foreach ($matchInningsData as $matchInningskey => $matchInningsvalue) {
                $over = [];
                if($matchdata['latest_inning_number'] = '0'){
                    $inningsstatus = "Ongoing";
                }elseif($matchInningsvalue->innings == $matchdata['latest_inning_number']){
                    $inningsstatus = "Ongoing";
                }else{
                    $inningsstatus = "";
                }
                $matchInningsOversData = InningsOver::where('match_innings_id', $matchInningsvalue->id)->get();
                foreach ($matchInningsOversData as $matchInningsOverskey => $matchInningsOversvalue) {
                    $status = "Upcoming"; // Assuming all overs are Upcoming by default
                    if ($matchdata['latest_inning_number'] == "1") {
                        if ($matchInningsvalue->innings != "1") {
                            $status = "Completed";
                        }else{
                            if($matchInningsOversvalue->overs < $currentoverfinal){
                                $status = "Completed";
                            }elseif($matchInningsOversvalue->overs == $currentoverfinal){
                                $status = "Ongoing";
                            }elseif($matchInningsOversvalue->overs >= $nextover){
                                $status = "Available";
                            }else{
                                $status = "Upcoming";
                            }
                        }
                    } elseif ($matchdata['latest_inning_number'] == "2") {
                        if ($matchInningsvalue->innings == "2") {
                            if($matchInningsOversvalue->overs < $currentoverfinal){
                                $status = "Completed";
                            }elseif($matchInningsOversvalue->overs == $currentoverfinal){
                                $status = "Ongoing";
                            }elseif($matchInningsOversvalue->overs >= $nextover){
                                $status = "Available";
                            }else{
                                $status = "Upcoming";
                            }
                        }else{
                            $status = "Completed";
                        }
                    }
                    $over[] = [
                        "over_id" => $matchInningsOversvalue->id,
                        "over_number" => $matchInningsOversvalue->overs,
                        "over_status" => $status
                    ];
                }
                $inningsone[] = [
                    "inning_name" => $matchInningsvalue->innings . " Inning",
                    "inning_status" => $inningsstatus,
                    "overs" => $over,
                ];
            }
            $matchdetail['matchdetail'] = [
                    "match" => $matchdata['title'],
                    "short_title" => $matchdata['short_title'],
                    "status"  => $matchdata['status_str'],
                    "note"  => $matchdata['status_note'],
                    "datetime"  => $matchdata['date_start'],
                    "teama"  => $matchdata['teama'],
                    "teamb"  => $matchdata['teamb'],
                    "innings" => $inningsone
             ];


            if($matchdetail){
                $message = "Over Found";
                return ApiResponse::successResponse($matchdetail, $message);
            }else{
                return ApiResponse::errorResponse("Matches Data Not Found");
            }


        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }

    }
}
