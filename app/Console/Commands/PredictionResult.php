<?php

namespace App\Console\Commands;

use App\Models\{
    CompetitionMatches,
    Competition,
    Overballes,
    Prediction,
    MatchInnings,
    InningsOver
};
use Carbon\Carbon;

use Mail, Hash, File, DB, Helper, Auth;

use Illuminate\Console\Command;

class PredictionResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prediction-result';

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
        \Log::info("PredictionResult: INIT");

        try {
            // API Token
            $token = 'dbe24b73486a731d9fa8aab6c4be02ef';

            // Get Prediction data
            $pendingPredictionMatchIds = Prediction::where('result','ND')->where('status','pending')->groupBy('match_id')->pluck('match_id');
            $completedMatches = CompetitionMatches::whereIn('match_id',$pendingPredictionMatchIds)->where('status','Completed')->get();
            if(!empty($completedMatches)){
                foreach($completedMatches as $completedMatch){
                    $matchid = $completedMatch->match_id;

                    // API URL for live match data
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
                    $matchresponse = curl_exec($curl);
                    curl_close($curl);

                    // Decode API response
                    $matchresponsedata = json_decode($matchresponse, true);
                    $matchesData = $matchresponsedata['response'];
                    // If API response is successful
                    if ($matchresponsedata['status'] == 'ok') {
                        if ($matchresponsedata['response'] != "Data unavailable") {
                            $predictiondata = Prediction::select('predictions.id', 'predictions.user_id', 'predictions.match_id', 'predictions.question_id', 'predictions.over_id', 'predictions.answere', 'predictions.status', 'predictions.result', 'questions.question_constant', 'innings_overs.overs')
                            ->where('predictions.match_id',$matchid)
                            ->where('predictions.result', '=', 'ND')
                            ->where('predictions.status', '=', 'pending')
                            ->join('questions', 'predictions.question_id', '=', 'questions.id')
                            ->join('innings_overs', 'predictions.over_id', '=', 'innings_overs.id')
                            ->get();
                            if ($predictiondata) {
                                foreach ($predictiondata as $predictionkey => $predictionvalue) {
                                    
                                    //if ($predictionvalue->overs < $matchesData['live_score']['overs']) {
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
                                    //}
                                }
                            }

                            \Log::error("PredictionResult: Match Not Found! MATCH ID: ".$matchid);
                        }
                    } else {
                        // Log error if API not working
                        \Log::error("PredictionResult: Api Not Working");
                        break;
                    }
                }
            }

        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }
    }
}
