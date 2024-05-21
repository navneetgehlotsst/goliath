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
        try {
            // API Token
            $token = 'dbe24b73486a731d9fa8aab6c4be02ef';

            // Get Prediction data
            $pendingPredictionMatchIds = Prediction::where('result','ND')->where('status','pending')->groupBy('match_id')->pluck('match_id');
            $completedMatches = CompetitionMatches::whereIn('match_id',$pendingPredictionMatchIds)->where('status','Completed')->pluck('match_id');
            $predictiondata = Prediction::select('predictions.id', 'predictions.user_id', 'predictions.match_id', 'predictions.question_id', 'predictions.over_id', 'predictions.answere', 'predictions.status', 'predictions.result', 'questions.question_constant', 'innings_overs.overs')
                ->whereIn('predictions.match_id',$completedMatches)
                ->where('predictions.result', '=', 'ND')
                ->where('predictions.status', '=', 'pending')
                ->join('questions', 'predictions.question_id', '=', 'questions.id')
                ->join('innings_overs', 'predictions.over_id', '=', 'innings_overs.id')
                ->get();
            if ($predictiondata) {
                \Log::info(json_encode($predictiondata)); die;

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
                        Prediction::where('id', $predictionid)->update(['result' => $result]);
                    }
                }
            }


        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }
    }
}
