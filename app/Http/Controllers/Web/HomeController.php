<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Mail,Hash,File,DB,Helper,Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use App\Models\{
    Prediction,
    OverQuestions,
    InningsOver,
    CompetitionMatches,
    MatchInnings,
    User,
    Transaction,
    Competition,
    Overballes

};
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HomeController extends Controller
{
    public function index()
    {
        return view('web.home.index');
    }


    public function croncheck()
    {
        try {
            // API Token
            $token = 'c16eaec3abd49e3477yy567836a95ad43';
            $currentDate = Carbon::today()->   toDateString();
            $currentTime = Carbon::now()->toTimeString();
            $oneHourLater = Carbon::now()->addHour()->toTimeString();

            // Fetch live and scheduled matches
            $matchesData = CompetitionMatches::where(function ($query) use ($currentDate, $currentTime, $oneHourLater) {
                $query->where('status', 'Live')
                    ->orWhere(function ($query) use ($currentDate, $currentTime, $oneHourLater) {
                        $query->where('status', 'Scheduled')
                                ->whereDate('match_start_date', $currentDate)
                                ->whereBetween('start_time', [$currentTime, $oneHourLater]);
                    });
            })->get();

            // Process each match
            foreach ($matchesData as $match) {
                $matchId = $match->match_id;

                // API URL for live match data
                $apiMatchLive = "https://rest.entitysport.com/sandbox/cricket/matches/$matchId/live?token=$token";

                // Curl Request
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $apiMatchLive,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ]);
                $matchResponse = curl_exec($curl);
                curl_close($curl);

                // Decode API response
                $matchResponseData = json_decode($matchResponse, true);

                // Check API response status
                if ($matchResponseData['status'] == 'ok' && $matchResponseData['response'] != "Data unavailable") {
                    $matchData = $matchResponseData['response'];

                    // Update live Data in CompetitionMatches table
                    $teamKey = ($match->teama_name == $matchData['team_batting']) ? 'teama' : 'teamb';
                    $competitionMatchDataInsert = [
                        $teamKey . 'scorefull' => $matchData['live_score']['runs'] . '/' . $matchData['live_score']['wickets'],
                        $teamKey . 'score' => $matchData['live_score']['wickets'],
                        $teamKey . 'over' => $matchData['live_score']['overs'],
                        'live_innings' => $matchData['live_inning_number'],
                        'status' => $matchData['status_str']
                    ];

                    // Update CompetitionMatches table
                    CompetitionMatches::where('match_id', $matchId)->update($competitionMatchDataInsert);

                    // Update match innings in MatchInnings table
                    MatchInnings::where('match_id', $matchId)
                        ->where('innings', $matchData['live_inning_number'])
                        ->update([
                            'current_score' => $matchData['live_score']['runs'],
                            'current_wicket' => $matchData['live_score']['wickets'],
                            'current_overs' => $matchData['live_score']['overs']
                        ]);

                    // Prepare ball events for batch insert
                    $ballEvents = [];
                    if (isset($matchData['commentaries'])) {
                        foreach ($matchData['commentaries'] as $commentary) {
                            if ($commentary['event'] == 'ball') {
                                $eventId = $commentary['event_id'];
                                if (!Overballes::where('eventid', $eventId)->exists()) {
                                    $ballEvents[] = [
                                        "eventid" => $commentary['event_id'],
                                        "match_id" => $matchId,
                                        "innings" => $matchData['live_inning_number'],
                                        "over_no" => $commentary['over'],
                                        "ball_no" => $commentary['ball'],
                                        "score" => $commentary['score'],
                                        "noball_dismissal" => ($commentary['noball_dismissal'] == 1) ? "1" : "0",
                                        "run" => $commentary['run'],
                                        "noball_run" => $commentary['noball_run'],
                                        "wide_run" => $commentary['wide_run'],
                                        "bye_run" => $commentary['bye_run'],
                                        "legbye_run" => $commentary['legbye_run'],
                                        "bat_run" => $commentary['bat_run'],
                                        "noball" => ($commentary['noball'] == 1) ? "1" : "0",
                                        "wideball" => ($commentary['wideball'] == 1) ? "1" : "0",
                                        "six" => ($commentary['six'] == 1) ? "1" : "0",
                                        "four" => ($commentary['four'] == 1) ? "1" : "0",
                                    ];
                                }
                            }
                        }
                        // Batch insert ball events
                        Overballes::insert($ballEvents);
                    }

                    // Get Prediction data for the current match
                    $predictionData = Prediction::select('id', 'answere', 'overs')
                        ->where('result', '=', 'ND')
                        ->where('status', '=', 'pending')
                        ->where('match_id', '=', $matchId)
                        ->get();

                    foreach ($predictionData as $prediction) {
                        if ($prediction->overs < $matchData['live_score']['overs']) {
                            $type = $prediction->question_constant;
                            $over = $prediction->overs;
                            $answer = $prediction->answere;
                            $predictionId = $prediction->id;

                            // Call Helper function to get prediction result
                            $returnResult = Helper::QuestionType($type, $matchId, $matchData['live_inning_number'], $over);
                            $result = ($answer == $returnResult) ? "W" : "L";

                            // Update prediction result
                            Prediction::where('id', $predictionId)->update(['result' => $result]);
                        }
                    }
                } else {
                    // Log error if API not working or data unavailable
                    \Log::error("API Not Responding or Data Unavailable for match ID: $matchId");
                }
            }

            // Log success message after updating scores
            \Log::info("Scores updated successfully.");


        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }
    }


    public function cronchecktwo()
    {
        try {
            // Get current year
            $currentYear = Carbon::now()->year;

            // Initialize variables
            $page = 1;
            $token = 'dbe24b73486a731d9fa8aab6c4be02ef';
            $perPage = 500;

            // Construct API URL
            $apiurl = "https://rest.entitysport.com/sandbox/cricket/seasons/$currentYear/competitions?token=$token&per_page=$perPage";

            // Initialize cURL session
            $curl = curl_init();

            // Set cURL options
            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiurl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            // Execute cURL session
            $competitionresponse = curl_exec($curl);
            curl_close($curl);

            // Decode API response
            $competitionresponsedata = json_decode($competitionresponse, true);
            $compdata = $competitionresponsedata['response']['items'] ?? [];

            // Check if API call was successful
            if ($competitionresponsedata['status'] == 'ok') {
                // Collect competition IDs
                $competitionIds = array_column($compdata, 'cid');

                // Fetch existing competitions
                $existingCompetitions = Competition::whereIn('competiton_id', $competitionIds)->get()->keyBy('competiton_id');

                // Fetch question IDs
                $questionIds = Question::where('type', 'initial')->pluck('id')->toArray();

                foreach ($compdata as $compdatavalue) {
                    // Prepare competition data
                    $competitiondata = [
                        'competiton_id' => $compdatavalue['cid'],
                        'title' => $compdatavalue['title'],
                        'type' => $compdatavalue['category'],
                        'competition_type' => $compdatavalue['match_format'],
                        'date_start' => $compdatavalue['datestart'],
                        'date_end' => $compdatavalue['dateend'],
                        'status' => $compdatavalue['status'],
                    ];

                    // Check if competition already exists
                    if ($existingCompetitions->has($compdatavalue['cid'])) {
                        // Update existing competition
                        $existingCompetitions[$compdatavalue['cid']]->update($competitiondata);
                    } else {
                        // Create new competition
                        Competition::create($competitiondata);
                    }

                    //========================== Now get competitions matches ==================//
                    $cId = $compdatavalue['cid'];

                    // Fetch existing matches
                    $existingMatches = CompetitionMatches::whereIn('competiton_id', $cId)->count();
                    if($existingMatches == '0'){
                        $pagedatacount = 100;
                        $apiurlScheduled = "https://rest.entitysport.com/v2/competitions/$cId/matches/?token=$token&per_page=$pagedatacount&paged=$page";

                        // Initialize cURL session for matches
                        $curlScheduled = curl_init($apiurlScheduled);

                        // Set cURL options for matches
                        curl_setopt_array($curlScheduled, [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                        ]);

                        // Execute cURL session for matches
                        $matchScheduledresponse = curl_exec($curlScheduled);
                        curl_close($curlScheduled);

                        // Decode match API response
                        $matchscheduledresponsedata = json_decode($matchScheduledresponse, true);
                        $matchscheduleddata = $matchscheduledresponsedata['response']['items'] ?? [];

                        // Check if match API call was successful
                        if ($matchscheduledresponsedata['status'] == 'ok') {
                            foreach ($matchscheduleddata as $matchvalue) {
                                // Check if match already exists
                                if (!$existingMatches->has($matchvalue['match_id'])) {

                                    // Prepare match data
                                    $dateTime = $matchvalue['date_start'];
                                    $datearray = explode(" ", $dateTime);
                                    $matchesdata = [
                                        'competiton_id' => $cId,
                                        'match_id' => $matchvalue['match_id'],
                                        'match' => $matchvalue['title'],
                                        'subtitle' => $matchvalue['subtitle'],
                                        'note' => $matchvalue['status_note'],
                                        'teamaid' => $matchvalue['teama']['team_id'],
                                        'teama_name' => $matchvalue['teama']['name'],
                                        'teama_short_name' => $matchvalue['teama']['short_name'],
                                        'teama_img' => $matchvalue['teama']['logo_url'],
                                        'teamascorefull' => $matchvalue['teama']['scores_full'] ?? '0',
                                        'teamascore' => $matchvalue['teama']['scores'] ?? '0',
                                        'teamaover' => $matchvalue['teama']['overs'] ?? '0',
                                        'teambid' => $matchvalue['teamb']['team_id'],
                                        'teamb_name' => $matchvalue['teamb']['name'],
                                        'teamb_short_name' => $matchvalue['teamb']['short_name'],
                                        'teamb_img' => $matchvalue['teamb']['logo_url'],
                                        'teambscorefull' => $matchvalue['teamb']['scores_full'] ?? '0',
                                        'teambscore' => $matchvalue['teamb']['scores'] ?? '0',
                                        'teambover' => $matchvalue['teamb']['overs'] ?? '0',
                                        'formate' => $matchvalue['format_str'],
                                        'match_start_date' => $datearray['0'],
                                        'match_start_time' => $datearray['1'],
                                        'status' => $matchvalue['status_str'],
                                    ];

                                    // Create new match
                                    CompetitionMatches::create($matchesdata);

                                    // Determine over limit based on match format
                                    $format = $matchvalue['format'];
                                    $overlimit = ($format == '7' || $format == '1') ? 50 : (($format == '17') ? 10 : (($format == '3' || $format == '6' || $format == '8') ? 20 : 90));



                                    // Create innings and overs
                                    $inningsToCreate = [];
                                    foreach (range(1, 2) as $i) {
                                        $inningsToCreate[] = [
                                            'match_id' => $matchvalue['match_id'],
                                            'innings' => $i,
                                        ];
                                    }
                                    MatchInnings::insert($inningsToCreate);

                                    // Create overs and questions
                                    foreach (MatchInnings::where('match_id', $matchvalue['match_id'])->get() as $matchInning) {
                                        $inningsOverToCreate = [];
                                        foreach (range(1, $overlimit) as $j) {
                                            $inningOver = InningsOver::create([
                                                'match_innings_id' => $matchInning->id,
                                                'overs' => $j,
                                            ]);

                                            foreach ($questionIds as $questionId) {
                                                OverQuestions::create([
                                                    'innings_over_id' => $inningOver->id,
                                                    'question_id' => $questionId,
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            // Log error if match API call fails
                            echo "API for matches not working for competition ID:" .$cId;
                        }
                    }else{
                        echo "match exixt";
                    }
                }
                // Log success message
                echo 'All competitions and matches processed successfully.';
            } else {
                // Log error if competition API call fails
                echo "API for competitions not working";
            }
        } catch (\Throwable $th) {
            // Log any exceptions
            // Log::info($th);
            dd($th);
        }
    }
}
