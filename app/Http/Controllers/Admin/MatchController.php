<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Question,
    MatchInnings,
    InningsOver,
    OverQuestions,
    CompetitionMatches

};

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

            Carbon::setTestNow(Carbon::now()->tz('GMT'));

            // Now you can work with GMT time using Carbon
            echo $gmtTime = Carbon::now();

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

            // Get match data
            $matchdata = makeCurlRequest("https://rest.entitysport.com/v2/matches/$id/scorecard/?token=$token")['response'];

            // Get live match data
            $matchdatalive = makeCurlRequest("https://rest.entitysport.com/v2/matches/$id/live/?token=$token")['response'];

            $currentover = $matchdatalive['live_score']['overs'] ?? "0";

            // Calculate current over final
            $currentoverfinal = ($currentover != floor($currentover)) ? ceil($currentover) : $currentover;

            $format = $matchdata['format'];
            // $maxOver = $matchdata['innings'];
            // dd($maxOver);

            // Determine over limit based on match format
            $overlimit = ($format == '7' || $format == '1') ? 50 : (($format == '3' || $format == '6' || $format == '8') ? 20 : 90);

            // Check if questions exist for the match
            $existingQuestions = MatchInnings::where('match_id', $matchdata['match_id'])->exists();

            if (!$existingQuestions) {
                // Prepare question IDs
                $questionIds = Question::where('type', 'initial')->pluck('id')->toArray();

                // Insert match innings
                $inningsToCreate = [];
                foreach (range(1, 2) as $i) {
                    $inningsToCreate[] = [
                        'match_id' => $matchdata['match_id'],
                        'innings' => $i,
                    ];
                }
                MatchInnings::insert($inningsToCreate);

                // Insert innings overs and associate questions
                foreach (MatchInnings::where('match_id', $matchdata['match_id'])->get() as $matchInning) {
                    $inningsOverToCreate = [];
                    foreach (range(1, $overlimit) as $j) {
                        $inningOver = InningsOver::create([
                            'match_innings_id' => $matchInning->id,
                            'overs' => $j,
                        ]);
                        // Associate questions
                        foreach ($questionIds as $questionId) {
                            OverQuestions::create([
                                'innings_over_id' => $inningOver->id,
                                'question_id' => $questionId,
                            ]);
                        }
                    }
                }
            }

            $GetMatchdata = MatchInnings::select('match_innings.id as match_innings_id', 'match_innings.match_id', 'match_innings.innings', 'innings_overs.overs', 'innings_overs.id as innings_overs_id')
                ->where('match_innings.match_id', $matchdata['match_id'])
                ->join('innings_overs', 'match_innings.id', '=', 'innings_overs.match_innings_id')
                ->get();

            return view('admin.matches.info', compact('matchdata', 'GetMatchdata', 'currentoverfinal'));

        } catch (\Throwable $th) {
            dd($th);
        }

    }

    public function matchQuestion($overid){

        try {
            $inningsQuestionsData = OverQuestions::select('over_questions.*','questions.question')->where('innings_over_id', $overid)->join('questions', 'over_questions.question_id', '=', 'questions.id')->get();
            $questionList = Question::where('type', 'supplementry')->get();

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
