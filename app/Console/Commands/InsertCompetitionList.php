<?php

namespace App\Console\Commands;
use App\Models\{
    Competition,
    CompetitionMatches
};
use Carbon\Carbon;
use Illuminate\Console\Command;

class InsertCompetitionList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:insert-competition-list';

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
            $perPage = 500;

            foreach ($compdata as $value) {
                $cId = $value->competiton_id;
                $pagedatacount = 100;
                $apiurlmatch = "https://rest.entitysport.com/v2/competitions/$cId/matches/?token=$token&per_page=$perPage";

                $curlmatch = curl_init($apiurlmatch);

                curl_setopt_array($curlmatch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ]);

                $matchresponse = curl_exec($curlmatch);
                curl_close($curlmatch);

                $matchresponsedata = json_decode($matchresponse, true);
                if($matchresponsedata['status'] == 'ok'){
                    $matchesData = $matchresponsedata['response']['items'];
                    // Collect Matches IDs
                    $matchesIds = array_column($matchesData, 'match_id');


                    // Fetch existing matches
                    $existingMatches = CompetitionMatches::whereIn('match_id', $matchesIds)->get()->keyBy('match_id');
                    foreach ($matchesData as $matchvalue) {
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
                        if ($existingMatches->has($matchvalue['match_id'])) {
                            // Update existing matches
                            $existingMatches[$matchvalue['match_id']]->update($matchesdata);
                        } else {
                            // Create new matches
                            CompetitionMatches::create($matchesdata);
                        }
                    }
                }else{
                    \Log::error("Api Not Working");
                    break;
                }
            }
            \Log::error("Match And Compition Are Added");
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }

    }
}
