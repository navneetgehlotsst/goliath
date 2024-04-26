<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddQuestionToMatch;
use App\Models\Question;

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
