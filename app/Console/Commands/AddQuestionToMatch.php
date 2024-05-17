<?php

namespace App\Console\Commands;

use App\Models\{
    Question,
    MatchInnings,
    InningsOver,
    OverQuestions,
    Competition,
    CompetitionMatches
};
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        try {
            $apicount = DB::table('api_count')->where('name', 'match_live')->first();
            if ($apicount < 30000) {
                // Get current year
                $currentYear = Carbon::now()->year;

                // Initialize variables
                $page = 1;
                $token = 'dbe24b73486a731d9fa8aab6c4be02ef';
                $perPage = 500;

                // Construct API URL
                $apiurl = "https://rest.entitysport.com/sandbox/cricket/seasons/$currentYear/competitions?token=$token&per_page=$perPage";

                $data = DB::table('api_count')->where('name', 'compitetion')->increment("count");

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
                        if ($existingMatches == '0') {
                            $pagedatacount = 100;
                            $apiurlScheduled = "https://rest.entitysport.com/v2/competitions/$cId/matches/?token=$token&per_page=$pagedatacount&paged=$page";
                            $data = DB::table('api_count')->where('name', 'compition_matches')->increment("count");
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
                                //echo "API for matches not working for competition ID:" .$cId;
                                \Log::error("API for matches not working for competition ID:" . $cId);
                            }
                        } else {
                            \Log::error("Matches Exit");
                        }
                    }
                    // Log success message
                    \Log::info("All competitions and matches processed successfully.");
                } else {
                    // Log error if competition API call fails
                    \Log::error("API for competitions not working");
                }
            } else {
                \Log::info("api count limt end compitetion Matches");
            }
        } catch (\Throwable $th) {
            // Log any exceptions
            // Log::info($th);
            dd($th);
        }
    }
}
