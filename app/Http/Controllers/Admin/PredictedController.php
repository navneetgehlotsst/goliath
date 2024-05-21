<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Question,
    Overballes,
    MatchInnings,
    InningsOver,
    OverQuestions,
    CompetitionMatches,
    Prediction
};
use Helper;

use Carbon\Carbon;

class PredictedController extends Controller
{
    public function pridictedInfo($matchid)
    {
        try {

            // Fetch match data, current innings data, and innings data for the match in a single query
            $matchData = CompetitionMatches::where('match_id', $matchid)
            ->with(['matchInnings', 'matchInnings.inningsOvers'])
            ->first();

            // Fetch prediction data with eager loading
            $predictionData = Prediction::with(['competitionMatch'])
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

            return view('admin.predict.info', compact('transformedMatch'));

        } catch (\Throwable $th) {
            dd($th);
        }

    }

    public function pridictedUser($overid,$matchid)
    {
        try {

            $predictionUsers = Prediction::select('predictions.match_id', 'users.full_name')
                                ->join('users', 'predictions.user_id', '=', 'users.id')
                                ->where([
                                    ['predictions.match_id', '=', $matchid],
                                    ['predictions.over_id', '=', $overid],
                                    ['predictions.status', '=', 'complete']
                                ])
                                ->groupBy('predictions.user_id', 'predictions.match_id')
                                ->get();

            return view('admin.predict.user', compact('predictionUsers'));

        } catch (\Throwable $th) {
            dd($th);
        }

    }

}