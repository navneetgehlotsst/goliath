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
                        "note" => "No additional note available",
                        "match_start_date" => $match->match_start_date,
                        "match_start_time" => $match->match_start_time,
                        "formate" => $match->formate,
                        "teama" => [
                            "team_id" => null, // Set team ID if available, otherwise null.
                            "name" => $match->teama_name,
                            "short_name" => $match->teama_short_name,
                            "logo_url" => $match->teama_img,
                            "thumb_url" => $match->teama_img,
                            "scores_full" => "", // Set scores if available.
                            "scores" => "", // Set scores if available.
                            "overs" => "", // Set overs if available.
                        ],
                        "teamb" => [
                            "team_id" => null, // Set team ID if available, otherwise null.
                            "name" => $match->teamb_name,
                            "short_name" => $match->teamb_short_name,
                            "logo_url" => $match->teamb_img,
                            "thumb_url" => $match->teamb_img,
                            "scores_full" => "", // Set scores if available.
                            "scores" => "", // Set scores if available.
                            "overs" => "", // Set overs if available.
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

        // Fetch match data and live score data concurrently
        $matchPromise = $this->makeCurlRequest("https://rest.entitysport.com/v2/matches/{$input['match_id']}/scorecard/?token={$this->token}");
        $livePromise = $this->makeCurlRequest("https://rest.entitysport.com/v2/matches/{$input['match_id']}/live/?token={$this->token}");

        $matchdata = $matchPromise['response'];
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

                if ($match_inning->innings == $matchdata['latest_inning_number']) {
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

                if ($match_inning->innings < $matchdata['latest_inning_number']) {
                    $over_status = "Completed";
                    $innings_status = "Completed";
                }

                if ($match_inning->innings > $matchdata['latest_inning_number']) {
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
