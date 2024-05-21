<?php

namespace App\Console\Commands;

use App\Models\{
    MatchInnings,
    CompetitionMatches,
    Competition,
    Overballes,
    Prediction
};
use Carbon\Carbon;

use Mail, Hash, File, DB, Helper, Auth;

use Illuminate\Console\Command;

class LiveDataForMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:live-data-for-matches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            //$apicount = DB::table('api_count')->where('name', 'match_live')->value('count');
            //if ($apicount < 30000) {
                // API Token
                $token = 'dbe24b73486a731d9fa8aab6c4be02ef';

                $currentUtcTime = Carbon::now('UTC');
                $oneHourBefore = $currentUtcTime->copy()->subHour();
                $matchesData = CompetitionMatches::where('status', 'Live')
                    ->orWhere(function ($query) use ($currentUtcTime, $oneHourBefore) {
                        $query->where('status', 'Scheduled')
                            ->where(DB::raw('TIMESTAMP(CONCAT(match_start_date, " ", match_start_time))'), '>', $oneHourBefore)
                            ->where(DB::raw('TIMESTAMP(CONCAT(match_start_date, " ", match_start_time))'), '<=', $currentUtcTime);
                    })->get();

                // If there are live and Completed matches
                if (!empty($matchesData)) {
                    foreach ($matchesData as $key => $match) {
                        $matchid = $match->match_id;

                        // API URL for live match data
                        $apimatchlive = "https://rest.entitysport.com/v2/matches/$matchid/live?token=$token";

                        //$data = DB::table('api_count')->where('name', 'match_live')->increment("count");
                        // Curl Request
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => $apimatchlive,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                        ));
                        $matchresponse = curl_exec($curl);
                        curl_close($curl);

                        // Decode API response
                        $matchresponsedata = json_decode($matchresponse, true);
                        $matchesData = $matchresponsedata['response'];

                        //\Log::info($matchresponse);

                        // If API response is successful
                        if ($matchresponsedata['status'] == 'ok') {
                            if ($matchresponsedata['response'] != "Data unavailable") {
                                // Update live Data in CompetitionMatches table
                                if ($match->teama_name == $matchesData['team_batting']) {
                                    $competitionmatchDatainsert = [
                                        'teamascorefull' => $matchesData['live_score']['runs'] . '/' . $matchesData['live_score']['wickets'],
                                        'teamascore' => $matchesData['live_score']['wickets'],
                                        'teamaover' => $matchesData['live_score']['overs'],
                                        'live_innings' => $matchesData['live_inning_number'],
                                        'status' => $matchesData['status_str'],
                                        'max_over' => ($matchesData['live_inning']['max_over'] ?? 0)
                                    ];
                                } else {
                                    $competitionmatchDatainsert = [
                                        'teambscorefull' => $matchesData['live_score']['runs'] . '/' . $matchesData['live_score']['wickets'],
                                        'teambscore' => $matchesData['live_score']['wickets'],
                                        'teambover' => $matchesData['live_score']['overs'],
                                        'live_innings' => $matchesData['live_inning_number'],
                                        'status' => $matchesData['status_str'],
                                        'max_over' => ($matchesData['live_inning']['max_over'] ?? 0)
                                    ];
                                }

                                // Update CompetitionMatches table
                                CompetitionMatches::where('match_id', $matchid)->update($competitionmatchDatainsert);

                                // Update match innings in MatchInnings table
                                $matchDatainsert = [
                                    'current_score' => $matchesData['live_score']['runs'],
                                    'current_wicket' => $matchesData['live_score']['wickets'],
                                    'current_overs' => $matchesData['live_score']['overs']
                                ];
                                MatchInnings::where('match_id', $matchid)->where('innings', $matchesData['live_inning_number'])->update($matchDatainsert);

                                // Prepare ball events for batch insert
                                $ballEvents = [];
                                if (isset($matchesData['commentaries'])) {
                                    foreach ($matchesData['commentaries'] as $commentarieskey => $commentariesvalue) {
                                        // Over by ball data insert
                                        if ($commentariesvalue['event'] == 'ball') {
                                            $eventId = $commentariesvalue['event_id'];
                                            $overBallCheck = Overballes::where('eventid', $eventId)->first();
                                            if (empty($overBallCheck)) {
                                                $ballevent = [
                                                    "eventid" => $commentariesvalue['event_id'],
                                                    "match_id" => $matchid,
                                                    "innings" => $matchesData['live_inning_number'],
                                                    "over_no" => ($commentariesvalue['over']+1),
                                                    "ball_no" => $commentariesvalue['ball'],
                                                    "score" => $commentariesvalue['score'],
                                                    "noball_dismissal" => ($commentariesvalue['noball_dismissal'] == 1) ? "1" : "0",
                                                    "run" => $commentariesvalue['run'],
                                                    "noball_run" => $commentariesvalue['noball_run'],
                                                    "wide_run" => $commentariesvalue['wide_run'],
                                                    "bye_run" => $commentariesvalue['bye_run'],
                                                    "legbye_run" => $commentariesvalue['legbye_run'],
                                                    "bat_run" => $commentariesvalue['bat_run'],
                                                    "noball" => ($commentariesvalue['noball'] == 1) ? "1" : "0",
                                                    "wideball" => ($commentariesvalue['wideball'] == 1) ? "1" : "0",
                                                    "six" => ($commentariesvalue['six'] == 1) ? "1" : "0",
                                                    "four" => ($commentariesvalue['four'] == 1) ? "1" : "0",
                                                ];
                                                $ballEvents[] = $ballevent;
                                            }
                                        }
                                    }

                                    // Batch insert ball events
                                    Overballes::insert($ballEvents);
                                }

                                // Get Prediction data
                                $predictiondata = Prediction::select('predictions.id', 'predictions.user_id', 'predictions.match_id', 'predictions.question_id', 'predictions.over_id', 'predictions.answere', 'predictions.status', 'predictions.result', 'questions.question_constant', 'innings_overs.overs')
                                    ->where('predictions.result', '=', 'ND')
                                    ->where('predictions.status', '=', 'pending')
                                    ->where('predictions.match_id', '=', $matchid)
                                    ->join('questions', 'predictions.question_id', '=', 'questions.id')
                                    ->join('innings_overs', 'predictions.over_id', '=', 'innings_overs.id')
                                    ->get();
                                if ($predictiondata) {
                                    foreach ($predictiondata as $predictionkey => $predictionvalue) {
                                        if ($predictionvalue->overs < $matchesData['live_score']['overs']) {
                                            $type = $predictionvalue->question_constant;
                                            $over = $predictionvalue->overs;
                                            $answer = $predictionvalue->answere;
                                            $predictionid = $predictionvalue->id;

                                            // Call Helper function to get prediction result
                                            $returnresult = Helper::QuestionType($type, $matchid, $matchesData['live_inning_number'], $over);
                                            if ($answer == $returnresult) {
                                                $result = "W";
                                            } else {
                                                $result = "L";
                                            }

                                            // Update prediction result
                                            Prediction::where('id', $predictionid)->update(['result' => $result , 'status' => 'complete']);
                                        }
                                    }
                                }
                            } else {
                                $apiMatchInfo = "https://rest.entitysport.com/v2/matches/$matchid/info?token=$token";

                                // Curl Request
                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => $apiMatchInfo,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'GET',
                                ));
                                $matchResponse = curl_exec($curl);
                                curl_close($curl);

                                // Decode API response
                                $matchResponseData = json_decode($matchResponse, true);
                                $matchesData = $matchResponseData['response'];

                                // If API response is successful
                                if ($matchResponseData['status'] == 'ok') {
                                    if ($matchResponseData['response'] != "Data unavailable") {
                                        // Update live Data in CompetitionMatches table
                                        $competitionmatchDatainsert = [
                                            'note' => $matchesData['status_note'],
                                            'live_innings' => $matchesData['latest_inning_number'],
                                            'status' => $matchesData['status_str']
                                        ];
                                        // Update CompetitionMatches table
                                        CompetitionMatches::where('match_id', $matchid)->update($competitionmatchDatainsert);
                                    }
                                }
        
                                \Log::error("LiveDataForMatches: Match Not Found! MATCH ID: ".$matchid);
                            }
                        } else {
                            // Log error if API not working
                            \Log::error("LiveDataForMatches: Api Not Working");
                            break;
                        }
                    }
                }
                // Log success message after updating scores
                //\Log::error("LiveDataForMatches: score updated");
            // } else {
            //     \Log::info("LiveDataForMatches: api count limt end Match live api");
            // }
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }
    }
}
