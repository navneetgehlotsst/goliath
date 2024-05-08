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

use Mail,Hash,File,DB,Helper,Auth;

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

            $compdata = Competition::where('status', 'like', 'live')->get();

            $token = 'dbe24b73486a731d9fa8aab6c4be02ef';

            foreach ($compdata as $value) {
                $cId = $value->competiton_id;

                // Fetch live matches outside the loop
                $matchesData = CompetitionMatches::where('status', 'like', 'live')->get();

                foreach ($matchesData as $key => $matche) {
                    $matchid = $matche->match_id;
                    $apimatchlive = "https://rest.entitysport.com/v2/matches/$matchid/live?token=$token";

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

                    $matchresponsedata = json_decode($matchresponse, true);
                    $matchesData = $matchresponsedata['response'];
                    $matchcommentaries = $matchesData['commentaries'];
                    $liveinningnumber = $matchesData['live_inning_number'];
                    $liveinningrun = $matchesData['live_score']['runs'];
                    $liveinningover = $matchesData['live_score']['overs'];
                    $liveinningnwicket = $matchesData['live_score']['wickets'];

                    // Update live innings in CompetitionMatches table
                    CompetitionMatches::where('match_id', $matchid)->update(['live_innings' => $liveinningnumber]);

                    // Update match innings in MatchInnings table
                    $matchData = [
                        'current_score' => $liveinningnumber,
                        'current_wicket' => $liveinningnwicket,
                        'current_overs' => $liveinningover
                    ];
                    MatchInnings::where('match_id', $matchid)->where('innings', $liveinningnumber)->update($matchData);

                    // Prepare ball events for batch insert
                    $ballEvents = [];
                    foreach ($matchcommentaries as $commentarieskey => $commentariesvalue) {

                        // over by ball data insert
                        if ($commentariesvalue['event'] == 'ball') {
                            $eventId = $commentariesvalue['event_id'];
                            $overBallCheck = Overballes::where('eventid', $eventId)->first();
                            if (empty($overBallCheck)) {
                                $ballevent = [
                                    "eventid" => $commentariesvalue['event_id'],
                                    "match_id" => $matchid,
                                    "innings" => $liveinningnumber,
                                    "over_no" => $commentariesvalue['over'],
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

                        // Get Perdition data
                        $predictiondata = Prediction::select('predictions.id', 'predictions.user_id', 'predictions.match_id', 'predictions.question_id', 'predictions.over_id', 'predictions.answere', 'predictions.status' ,'predictions.result' ,'questions.question_constant','innings_overs.overs')
                        ->where('predictions.result', '=', 'ND')
                        ->where('predictions.status', '=', 'pending')
                        ->where('predictions.match_id', '=', $matchid)
                        ->join('questions', 'predictions.question_id', '=', 'questions.id')
                        ->join('innings_overs', 'predictions.over_id', '=', 'innings_overs.id')
                        ->get();
                        foreach ($predictiondata as $predictionkey => $predictionvalue) {
                            if($predictionvalue->overs < $liveinningover){
                                $type = $predictionvalue->question_constant;
                                $over = $predictionvalue->overs;
                                $answer = $predictionvalue->answere;
                                $predictionid = $predictionvalue->id;
                                $returnresult = Helper::QuestionType($type,$matchid,$liveinningnumber,$over);

                                if($answer == $returnresult){
                                    $result = "W";
                                }else{
                                    $result = "L";
                                }

                                Prediction::where('id', $predictionid)->update(['result' => $result]);
                            }
                        }
                    }
                    // Batch insert ball events
                    if (!empty($ballEvents)) {
                        Overballes::insert($ballEvents);
                    }
                }
            }
            \Log::error("score updated");
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }
    }
}
