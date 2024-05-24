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

use Illuminate\Support\Facades\Session;

class MatchController extends Controller
{
    public function index($cId)
    {
        // Retrieve the previous URL
        $previousURL = url()->previous();

        // Storing a value in the session
        Session::put('previousURL', $previousURL);
        $CompetitionMatchData = CompetitionMatches::where('competiton_id', $cId)->orderByRaw("CASE
                WHEN status = 'Live' THEN 1
                WHEN status = 'Scheduled' THEN 2
                WHEN status = 'Completed' THEN 3
                WHEN status = 'Cancelled' THEN 4
                ELSE 5
            END")
            ->get();
        return view('admin.matches.index', compact('CompetitionMatchData','previousURL'));
    }

    public function matchInfo($id)
    {

        try {

            // Retrieving a value from the session
            $previousURL = Session::get('previousURL');

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

            return view('admin.matches.info', compact('transformedMatch','previousURL'));

        } catch (\Throwable $th) {
            dd($th);
        }

    }

    public function matchQuestion($overid){

        try {
            // Retrieving a value from the session
            $previousURL = Session::get('previousURL');
            // Fetch OverQuestions data
            $inningsQuestionsData = OverQuestions::where('innings_over_id', $overid)
            ->get();
            // Get InningsOver
            $InningsOverData = InningsOver::where('id', $overid)->first();

            // Get MatchInnings
            $MatchInningsData = MatchInnings::where('id', $InningsOverData->match_innings_id)->first();

            // Get CompetitionMatches
            $CompetitionMatchesData = CompetitionMatches::where('match_id', $MatchInningsData->match_id)->first();
            $competiton_id = $CompetitionMatchesData->competiton_id;
            $match_id = $MatchInningsData->match_id;
            // Extract question IDs from $inningsQuestionsData
            $questionIdArray = $inningsQuestionsData->pluck('question_id')->all();

            // Fetch questions corresponding to the extracted question IDs
            $questionList = Question::where('status', 'active')
            ->whereNotIn('id', $questionIdArray)
            ->get();

            // Load the question for each OverQuestions object
            $inningsQuestionsData->load('loadquestion');


            // Pass the data to the view
            return view('admin.matches.questionchange', compact('inningsQuestionsData', 'questionList','previousURL','competiton_id','match_id'));


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
