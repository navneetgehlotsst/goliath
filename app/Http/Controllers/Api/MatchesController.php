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
    OverQuestions,
    Prediction
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
            $transformedMatches = [];

            foreach ($datamatches as $key => $match) {
                $transformedMatch = [
                        "id"=> $match->id,
                        "competiton_id" => $match->competiton_id,
                        "match_id" => $match->match_id,
                        "match" => $match->match,
                        "short_title" => $match->teama_short_name . " vs " . $match->teamb_short_name,
                        "status" => $match->status,
                        "note" => $match->note,
                        "match_start_date" => $match->match_start_date,
                        "match_start_time" => $match->match_start_time,
                        "formate" => $match->formate,
                        "teama" => [
                            "team_id" => $match->teamaid, // Set team ID if available, otherwise null.
                            "name" => $match->teama_name,
                            "short_name" => $match->teama_short_name,
                            "logo_url" => $match->teama_img,
                            "thumb_url" => $match->teama_img,
                            "scores_full" => $match->teamascorefull, // Set scores if available.
                            "scores" => $match->teamascore, // Set scores if available.
                            "overs" => $match->teamaover, // Set overs if available.
                        ],
                        "teamb" => [
                            "team_id" => $match->teambid, // Set team ID if available, otherwise null.
                            "name" => $match->teamb_name,
                            "short_name" => $match->teamb_short_name,
                            "logo_url" => $match->teamb_img,
                            "thumb_url" => $match->teamb_img,
                            "scores_full" => $match->teambscorefull, // Set scores if available.
                            "scores" => $match->teambscore, // Set scores if available.
                            "overs" => $match->teambover, // Set overs if available.
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
    }

    public function matchesDetail(Request $request)
    {
        $input = $request->validate([
            'match_id' => 'required',
        ]);

        $datamatches = CompetitionMatches::where('match_id', $input['match_id'])->first();
        // Fetch match data and live score data concurrently
        $livePromise = $this->makeCurlRequest("https://rest.entitysport.com/v2/matches/{$input['match_id']}/live/?token={$this->token}");
        $matchdatalive = $livePromise['response'];

        $currentover = $matchdatalive['live_score']['overs'] ?? 0;

        // Calculate current over final
        $current_over = ceil($currentover);
        $nextover = $current_over + 3;

        // Fetch innings data
        $matchInnings = MatchInnings::where('match_innings.match_id', $input['match_id'])->get();

        $matchdetail = [];
        $inningsone = [];

        foreach ($matchInnings as $match_inning) {
            $innings_status = '';
            $over = [];

            $matchInningsOversData = InningsOver::where('match_innings_id', $match_inning->id)->get();

            foreach ($matchInningsOversData as $matchInningsOversvalue) {
                $over_status = '';
                $prediction = Prediction::where('over_id', $matchInningsOversvalue->id)->first();

                if ($match_inning->innings == $matchdatascorecard['latest_inning_number']) {
                    if ($prediction) {
                        $over_status = "Predicted";
                    } else {
                        if ($matchInningsOversvalue->overs < $current_over) {
                            $over_status = "Completed";
                        } elseif ($matchInningsOversvalue->overs == $current_over) {
                            $over_status = "Ongoing";
                        } elseif ($matchInningsOversvalue->overs >= $nextover) {
                            $over_status = "Not Available";
                        } else {
                            $over_status = "Available";
                        }
                    }
                    $innings_status = "Ongoing";
                }

                if ($match_inning->innings < $matchdatascorecard['latest_inning_number']) {
                    $over_status = "Completed";
                    $innings_status = "Completed";
                }

                if ($match_inning->innings > $matchdatascorecard['latest_inning_number']) {
                    $over_status = "Upcoming";
                    $innings_status = "Upcoming";
                }

                $over[] = [
                    "over_id" => $matchInningsOversvalue->id,
                    "over_number" => $matchInningsOversvalue->overs,
                    "over_status" => $over_status
                ];
            }

            $inningsone[] = [
                "inning_name" => $match_inning->innings . " Inning",
                "inning_status" => $innings_status,
                "overs" => $over,
            ];
        }

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
            "innings" => $inningsone // Assuming no detailed innings information is available initially.
        ];

        return ApiResponse::successResponse($transformedMatch ?? null, $message ?? "Matches Data Not Found");

    }

    public function questionListForOver(Request $request)
    {
        $input = $request->validate([
            'over_id' => 'required',
            'match_id' => 'required',
        ]);

        $dataquestion = OverQuestions::select('over_questions.id','over_questions.question_id','questions.question','innings_overs.overs')
            ->where('over_questions.innings_over_id', $input['over_id'])
            ->join('questions', 'over_questions.question_id', '=', 'questions.id')
            ->join('innings_overs', 'over_questions.innings_over_id', '=', 'innings_overs.id')
            ->get();
        $datamatches = CompetitionMatches::where('match_id', $input['match_id'])->first();

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
            "overnumber" => $dataquestion->isEmpty() ? "0" : $dataquestion->first()->overs,
            "question" => $dataquestion,
        ];
        if($transformedMatch){
            return ApiResponse::successResponse($transformedMatch,"Question Found");
        }else{
            return ApiResponse::errorResponse("Question Not Found");
        }

    }
}
