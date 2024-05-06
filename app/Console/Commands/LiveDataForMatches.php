<?php

namespace App\Console\Commands;

use App\Models\{
    MatchInnings,
    CompetitionMatches,
    Competition
};
use Carbon\Carbon;

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
                    $liveinningnumber = $matchesData['live_inning_number'];
                    $liveinningrun = $matchesData['live_score']['runs'];
                    $liveinningover = $matchesData['live_score']['overs'];
                    $liveinningnwicket = $matchesData['live_score']['wickets'];

                    CompetitionMatches::where('match_id', $matchid)->update(['live_innings' => $liveinningnumber]);

                    $matchData = [
                        'current_score' => $liveinningnumber,
                        'current_wicket' => $liveinningnwicket,
                        'current_overs' => $liveinningover
                    ];

                    MatchInnings::where('match_id', $matchid)->where('innings', $liveinningnumber)->update($matchData);
                }
            }
            \Log::error("Match And Compition Are Added");
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }
    }
}
