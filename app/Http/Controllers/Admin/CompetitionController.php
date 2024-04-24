<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function index()
    {
        return view('admin.competition.index');
    }


    public function getData(Request $request)
    {
        try {
            $status = $request->status;
            $page = $request->page;
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
            $compdata = $competitionresponsedata['response']['items'];

            return response()->json($compdata);
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }
}
