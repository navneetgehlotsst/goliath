<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\{
    CompetitionMatches,
    MatchInnings,
    InningsOver,
    OverQuestions
};
use App\Http\Response\ApiResponse;

class MatchesController extends Controller
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

    public function matchesList(Request $request)
    {
        $input = $request->validate([
            'status' => 'required|in:Live,Scheduled,Completed',
        ]);

        $datamatches = CompetitionMatches::where('status', $input['status'])
            ->orderBy('match_start_date', 'ASC')
            ->orderBy('match_start_time', 'ASC')
            ->paginate(10);

        return $datamatches->count()
            ? ApiResponse::successResponse($datamatches, "Matches Data Found")
            : ApiResponse::errorResponse("Matches Data Not Found");
    }

    public function matchesDetail(Request $request)
    {
        $input = $request->validate([
            'match_id' => 'required',
        ]);

        $matchdata = $this->makeCurlRequest("https://rest.entitysport.com/v2/matches/{$input['match_id']}/scorecard/?token={$this->token}")['response'];
        $matchdatalive = $this->makeCurlRequest("https://rest.entitysport.com/v2/matches/{$input['match_id']}/live/?token={$this->token}")['response'];

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

        return ApiResponse::successResponse($matchdetail ?? null, $message ?? "Matches Data Not Found");
    }

    public function questionListForOver(Request $request)
    {
        $input = $request->validate([
            'over_id' => 'required',
            'match_id' => 'required',
        ]);

        $matchdata = $this->makeCurlRequest("https://rest.entitysport.com/v2/matches/{$input['match_id']}/scorecard/?token={$this->token}")['response'];
        $datamatches = OverQuestions::select('over_questions.id','over_questions.question_id','questions.question','innings_overs.overs')
            ->where('over_questions.innings_over_id', $input['over_id'])
            ->join('questions', 'over_questions.question_id', '=', 'questions.id')
            ->join('innings_overs', 'over_questions.innings_over_id', '=', 'innings_overs.id')
            ->get();

        return ApiResponse::successResponse([
            "match" => $matchdata['title'],
            "short_title" => $matchdata['short_title'],
            "status"  => $matchdata['status_str'],
            "note"  => $matchdata['status_note'],
            "datetime"  => $matchdata['date_start'],
            "teama"  => $matchdata['teama'],
            "teamb"  => $matchdata['teamb'],
            "overnumber" => $datamatches->isEmpty() ? null : $datamatches->first()->overs,
            "question" => $datamatches,
        ], $datamatches->isEmpty() ? "Question Not Found" : "Question Data Found");
    }
}
