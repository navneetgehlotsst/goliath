<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Question,
    MatchInnings,
    InningsOver,
    OverQuestions

};

class MatchController extends Controller
{
    public function index($cId , $page)
    {
            $token = 'dbe24b73486a731d9fa8aab6c4be02ef';
            $pagedatacount = 10;
        //Scheduled data
            $apiurlScheduled = "https://rest.entitysport.com/v2/competitions/$cId/matches/?token=$token&per_page=$pagedatacount&paged=$page&status=1";

            $curlScheduled = curl_init($apiurlScheduled);

            curl_setopt_array($curlScheduled, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);

            $matchScheduledresponse = curl_exec($curlScheduled);
            curl_close($curlScheduled);

            $matchscheduledresponsedata = json_decode($matchScheduledresponse, true);
            $matchscheduleddata = $matchscheduledresponsedata['response']['items'] ?? [];
            $scheduledpagecount = $matchscheduledresponsedata['response']['total_pages'] ?? 0;
        //Live
            $apiurlLive = "https://rest.entitysport.com/v2/competitions/$cId/matches/?token=$token&per_page=$pagedatacount&paged=$page&status=3";

            $curlLive = curl_init($apiurlLive);

            curl_setopt_array($curlLive, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);

            $matchliveresponse = curl_exec($curlLive);
            curl_close($curlLive);

            $matchliveresponsedata = json_decode($matchliveresponse, true);
            $matchliveddata = $matchliveresponsedata['response']['items'] ?? [];
        return view('admin.matches.index', compact('matchscheduleddata', 'page', 'scheduledpagecount', 'cId', 'matchliveddata'));
    }

    public function matchInfo($id)
    {
        try {
            $token = 'dbe24b73486a731d9fa8aab6c4be02ef';
            $apiurl = "https://rest.entitysport.com/v2/matches/$id/scorecard/?token=$token";

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiurl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $matchresponse = curl_exec($curl);
            curl_close($curl);

            $matchresponsedata = json_decode($matchresponse, true);
            $matchdata = $matchresponsedata['response'];


            $format = $matchdata['format'];

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

            $GetMatchdata = MatchInnings::select('match_innings.id as match_innings_id', 'match_innings.match_id', 'match_innings.innings', 'innings_overs.overs', 'innings_overs.id as innings_overs_id')->where('match_innings.match_id', $matchdata['match_id'])->join('innings_overs', 'match_innings.id', '=', 'innings_overs.match_innings_id')->get();

            return view('admin.matches.info', compact('matchdata','GetMatchdata'));
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
