<?php

namespace App\Console\Commands;
use App\Models\{
    Question,
    MatchInnings,
    InningsOver,
    OverQuestions

};
use Carbon\Carbon;
use Illuminate\Console\Command;

class AddQuestionToMatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-question-to-match';

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
        $status = 'live';
        $page = 1;
        $token = 'dbe24b73486a731d9fa8aab6c4be02ef';
        $perPage = 30;
        $apiurl = "https://rest.entitysport.com/v2/competitions?token=$token&per_page=$perPage&paged=$page&status=$status";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiurl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $competitionresponse = curl_exec($curl);
        curl_close($curl);

        $competitionresponsedata = json_decode($competitionresponse, true);
        $compdata = $competitionresponsedata['response']['items'] ?? [];

        $questionIds = Question::where('type', 'initial')->pluck('id')->toArray();

        foreach ($compdata as $compdatakey => $compdatavalue) {
            $cId = $compdatavalue['cid'];
            $pagedatacount = 100;
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

            foreach ($matchscheduleddata as $matchscheduleddatakey => $matchscheduleddatavalue) {
                $date = explode(" ", $matchscheduleddatavalue['date_start_ist']);
                $onlydate = $date['0'];
                $now = Carbon::now();
                $formattedDate = $now->toDateString();
                if($onlydate == $formattedDate){
                    $format = $matchscheduleddatavalue['format'];
                    $overlimit = ($format == '7' || $format == '1') ? 50 : (($format == '3' || $format == '6' || $format == '8') ? 20 : 90);
                    $existingQuestions = MatchInnings::where('match_id', $matchscheduleddatavalue['match_id'])->exists();

                    if (!$existingQuestions) {
                        $inningsToCreate = [];
                        foreach (range(1, 2) as $i) {
                            $inningsToCreate[] = [
                                'match_id' => $matchscheduleddatavalue['match_id'],
                                'innings' => $i,
                            ];
                        }
                        MatchInnings::insert($inningsToCreate);

                        foreach (MatchInnings::where('match_id', $matchscheduleddatavalue['match_id'])->get() as $matchInning) {
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
            }
        }
        echo "data added";
    }
}
