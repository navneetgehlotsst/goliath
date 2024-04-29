<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MatchesController extends Controller
{
    public function matchesList(Request $request){
        try {
            $cId = $request->cid;
            $page = $request->page;
            $token = 'dbe24b73486a731d9fa8aab6c4be02ef';
            $pagedatacount = 10;
            $apiurl = "https://rest.entitysport.com/v2/competitions/$cId/matches/?token=$token&per_page=$pagedatacount&paged=$page&status=1";

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiurl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $competitionresponse = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($http_status !== 200) {
                throw new Exception("HTTP request failed with status code $http_status");
            }

            $competitionresponsedata = json_decode($competitionresponse, true);

            $compdata = $competitionresponsedata['response']['items'];
            $competiitiondata = [];
            foreach ($compdata as $value) {
                $competiitiondata[] = [
                    'cid' => $value['cid'],
                    'title' => $value['title'],
                ];
            }

            $datacomp['competition'] = $competiitiondata;
            $datacomp['total_items'] = $competitionresponsedata['response']['total_items'];
            $datacomp['total_pages'] = $competitionresponsedata['response']['total_pages'];

            $message = "Competition Data Found";
            return ApiResponse::successResponse($datacomp, $message);

        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }

    }
}
