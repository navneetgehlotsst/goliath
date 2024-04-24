<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddQuestionToMatch;
use App\Models\Question;

class MatchController extends Controller
{
    public function index($id)
    {
        $token = 'dbe24b73486a731d9fa8aab6c4be02ef';
        $apiurl = "https://rest.entitysport.com/v2/competitions/$id/matches/?token=$token";

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
        $matchdata = $matchresponsedata['response']['items'];
        return view('admin.matches.index' , compact('matchdata'));
    }

    public function matchInfo($id)
    {
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
        $overlimt = ($format == '7' || $format == '1') ? 50 : 20;

        // Fetch existing questions for the match
        $existingQuestions = AddQuestionToMatch::where('matchid', $matchdata['match_id'])->exists();

        if (!$existingQuestions) {
            // Prepare question IDs
            $questionIds = Question::where('type', 'initial')->pluck('id')->toArray();

            // Prepare data for insertion
            $dataToCreate = [];
            foreach (range(1, $overlimt) as $i) {
                $dataToCreate[] = [
                    'matchid' => $matchdata['match_id'],
                    'questionid' => implode(',', $questionIds),
                    'over' => $i,
                ];
            }
            // Insert data into database
            AddQuestionToMatch::insert($dataToCreate);
        }

        // Fetch added questions for the match
        $addQuestionsdata = AddQuestionToMatch::where('matchid', $matchdata['match_id'])->groupBy('over')->get();


        return view('admin.matches.info' , compact('matchdata','addQuestionsdata'));
    }

}
