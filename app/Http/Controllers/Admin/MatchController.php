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

class MatchController extends Controller
{
    public function index($cId)
    {
        $CompetitionMatchData = CompetitionMatches::where('competiton_id', $cId)->get();
        return view('admin.matches.index', compact('CompetitionMatchData'));
    }

    public function matchInfo($id)
    {

        try {

             // Fetching match data
            $datamatches = CompetitionMatches::where('match_id', $id)->first();
            if (!$datamatches) {
                return ApiResponse::errorResponse(null, "Match Data Not Found");
            }

            $live_innings = $datamatches->live_innings;

            // Fetching current innings data
            $datamatchinnings = MatchInnings::where('match_id', $id)
                ->where('innings', $live_innings)
                ->first();

            $currentover = $datamatchinnings ? $datamatchinnings->current_overs : 0;

            // Adding 1 to the integer part if necessary
            $current_over = ceil($currentover);
            $nextover = $current_over + 5;

            // Fetching all innings data for the match
            $matchInnings = MatchInnings::where('match_id', $id)->get();

            $transformedMatch = [
                "matchdetail" => [
                    "id" => $datamatches->id,
                    "competition_id" => $datamatches->competiton_id,
                    "competition_name" => $datamatches->title,
                    "match_id" => $datamatches->match_id,
                    "match" => $datamatches->match,
                    "short_title" => $datamatches->teama_short_name . " vs " . $datamatches->teamb_short_name,
                    "status" => $datamatches->status,
                    "note" => $datamatches->note,
                    "match_start_date" => $datamatches->match_start_date,
                    "match_start_time" => $datamatches->match_start_time,
                    "format" => $datamatches->format, // Corrected typo
                    "teama" => [
                        "team_id" => $datamatches->teamaid,
                        "name" => $datamatches->teama_name,
                        "short_name" => $datamatches->teama_short_name,
                        "logo_url" => $datamatches->teama_img,
                        "thumb_url" => $datamatches->teama_img,
                        "scores_full" => $datamatches->teamascorefull,
                        "scores" => $datamatches->teamascore,
                        "overs" => $datamatches->teamaover,
                    ],
                    "teamb" => [
                        "team_id" => $datamatches->teambid,
                        "name" => $datamatches->teamb_name,
                        "short_name" => $datamatches->teamb_short_name,
                        "logo_url" => $datamatches->teamb_img,
                        "thumb_url" => $datamatches->teamb_img,
                        "scores_full" => $datamatches->teambscorefull,
                        "scores" => $datamatches->teambscore,
                        "overs" => $datamatches->teambover,
                    ],
                    "innings" => [], // Initialize innings array
                ]
            ];


            foreach ($matchInnings as $match_inning) {
                $innings_status = '';
                $over = [];

                $matchInningsOversData = InningsOver::where('match_innings_id', $match_inning->id)->get();

                foreach ($matchInningsOversData as $matchInningsOversvalue) {
                    $over_status = '';
                    $prediction = Prediction::where('over_id', $matchInningsOversvalue->id)->first();

                    if ($match_inning->innings == $live_innings) {
                        if ($prediction) {
                            $over_status = "Predicted";
                        } else {
                            if ($matchInningsOversvalue->overs < $current_over) {
                                $over_status = "Completed";
                            } elseif ($matchInningsOversvalue->overs == $current_over) {
                                $over_status = "Ongoing";
                            } elseif ($matchInningsOversvalue->overs <= $nextover) {
                                $over_status = "Not Available";
                            } else {
                                $over_status = "Available";
                            }
                        }
                        $innings_status = "Ongoing";
                    }

                    if ($match_inning->innings < $live_innings) {
                        $over_status = "Completed";
                        $innings_status = "Completed";
                    }

                    if ($match_inning->innings > $live_innings) {
                        $over_status = "Upcoming";
                        $innings_status = "Upcoming";
                    }

                    $over[] = [
                        "over_id" => $matchInningsOversvalue->id,
                        "over_number" => $matchInningsOversvalue->overs,
                        "over_status" => $over_status
                    ];
                }

                $transformedMatch['matchdetail']['innings'][] = [
                    "inning_name" => $match_inning->innings . " Inning",
                    "inning_status" => $innings_status,
                    "overs" => $over,
                ];
            }

            return view('admin.matches.info', compact('transformedMatch'));

        } catch (\Throwable $th) {
            dd($th);
        }

    }

    public function matchQuestion($overid){

        try {
            $inningsQuestionsData = OverQuestions::select('over_questions.*','questions.question')->where('innings_over_id', $overid)->join('questions', 'over_questions.question_id', '=', 'questions.id')->get();
            $questionList = Question::where('status','active')->where('type', 'supplementry')->get();

            return view('admin.matches.questionchange', compact('inningsQuestionsData','questionList'));
        } catch (\Throwable $th) {
            dd($th);
        }
    }


    public function changeQuestion(Request $request){
        try {
            // Validate incoming request data if needed
            $validatedData = $request->validate([
                'questionid' => 'required',
            ]);

            $inningsQuestionsData = OverQuestions::where('id', $request->inningquestion)->update(['question_id' => $request->questionid]);

            return response()->json(['message' => 'Question Change successfully'], 200);

        } catch (\Throwable $th) {
            dd($th);
        }
    }

}
