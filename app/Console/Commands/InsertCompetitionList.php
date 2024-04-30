<?php

namespace App\Console\Commands;
use App\Models\{
    CompetitionList,
    CompetitionMatchesList
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
            $currentYear = Carbon::now()->year;
            $token = 'dbe24b73486a731d9fa8aab6c4be02ef';
            $perPage = 500;
            $apiurl = "https://rest.entitysport.com/v2/seasons/$currentYear/competitions?token=$token&per_page=$perPage";
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
            $compdata = $competitionresponsedata['response']['items'];

            // Collect competition IDs
            $competitionIds = array_column($compdata, 'cid');

            // Fetch existing competitions
            $existingCompetitions = CompetitionList::whereIn('competiton_id', $competitionIds)->get()->keyBy('competiton_id');

            foreach ($compdata as $value) {
                $competitiondata = [
                    'competiton_id' => $value['cid'],
                    'title' => $value['title'],
                    'type' => $value['category'],
                    'competition_type' => $value['match_format'],
                    'date_start' => $value['datestart'],
                    'date_end' => $value['dateend'],
                    'status' => $value['status'],
                ];

                if ($existingCompetitions->has($value['cid'])) {
                    // Update existing competition
                    $existingCompetitions[$value['cid']]->update($competitiondata);
                } else {
                    // Create new competition
                    CompetitionList::create($competitiondata);
                }
                $cId = $value['cid'];
                $pagedatacount = 100;
                $apiurlmatch = "https://rest.entitysport.com/v2/competitions/$cId/matches/?token=$token&per_page=$pagedatacount";

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
                $matchesData = $matchresponsedata['response']['items'];
                // Collect Matches IDs
                $matchesIds = array_column($matchesData, 'match_id');

                // Fetch existing matches
                $existingMatches = CompetitionMatchesList::whereIn('match_id', $matchesIds)->get()->keyBy('match_id');

                foreach ($matchesData as $matchvalue) {
                    $dateTime = $matchvalue['date_start'];
                    $datearray = explode(" ", $dateTime);
                    $matchesdata = [
                        'competiton_id' => $cId,
                        'match_id' => $matchvalue['match_id'],
                        'match' => $matchvalue['title'],
                        'teama_name' => $matchvalue['teama']['name'],
                        'teama_img' => $matchvalue['teama']['logo_url'],
                        'teamb_name' => $matchvalue['teamb']['name'],
                        'teamb_img' => $matchvalue['teamb']['logo_url'],
                        'formate' => $matchvalue['format_str'],
                        'match_start_date' => $datearray['0'],
                        'match_start_time' => $datearray['1'],
                        'status' => $matchvalue['status_str'],
                    ];

                    if ($existingMatches->has($matchvalue['match_id'])) {
                        // Update existing matches
                        $existingCompetitions[$matchvalue['match_id']]->update($matchesdata);
                    } else {
                        // Create new matches
                        CompetitionMatchesList::create($matchesdata);
                    }
                }
            }
            \Log::error("Match And Compition Are Added");
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }

    }
}
