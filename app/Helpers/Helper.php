<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Page;
use DB, Auth, File, Mail;
use Carbon\Carbon;

use App\Models\{
    Overballes,
    Prediction,
    Notification,
    NotificationUser,
    Competition
};


class Helper
{

    public static function admin(){
        $admin = User::where('id',1)->first();
        return $admin;
    }

    public static function pages(){
        $pages = Page::get();
        return $pages;
    }

    public static function slug($table, $name)
    {
        $slug = str_replace(' ', '-', $name);
        $slug = strtolower($slug);
        $i = 1;
        while ($i > 0) {
            $check_slug = DB::table($table)->where('slug', $slug)->first();
            if($check_slug) {
                $slug = str_replace(' ', '-', $name) . '-' . $i;
                $slug = strtolower($slug);
                $i++;
                continue;
            }else{
                break;
            }
        }

        return $slug;
    }

    public static function slugUpdate($table, $name,$id)
    {
        $slug = str_replace(' ', '-', $name);
        $slug = strtolower($slug);
        $i = 1;
        while ($i > 0) {
            $check_slug = DB::table($table)->where('slug', $slug)->where('id','!=',$id)->first();
            if($check_slug) {
                $slug = str_replace(' ', '-', $name) . '-' . $i;
                $slug = strtolower($slug);
                $i++;
                continue;
            }else{
                break;
            }
        }

        return $slug;
    }

    public static function getUserNotifications(){
        $user = Auth::user();
        $notifications = array();
        if($user){
            $user_notifications = NotificationUser::where('user_id',$user->id)->where('read_at',null)->pluck('notification_id')->toArray();
            $notifications = Notification::whereIn('id',$user_notifications)->orderBy('created_at', 'desc')->take(5)->get();
        }
        return $notifications;
    }

    public static function cleanImage($string)
    {
        $string = str_replace(' ', '-', $string);
        return preg_replace('/[^A-Za-z0-9.\-]/', '', $string);
    }

    public static function userDetail($user_id)
    {
        $user_detail = User::find($user_id);
        return $user_detail;
    }

    public static function urlValidation(){
        $regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
        return $regex;
    }

    public static function getMatchByStatus(){
        $matchbystatus = [
            '1' => 'Schedule',
            '3' => 'Live'
        ];

        return $matchbystatus;
    }

    public static function getCompetitionsStatus(){
        //$matchbystatus = array('live','fixture');
        $matchbystatus =[
            'live' => 'Live',
            'fixture' => 'Upcoming',
        ];
        return $matchbystatus;
    }



    //parent Function
    public static function QuestionType($type,$matchid,$liveinningnumber,$over){

        if($type == 'even_runs_in_over'){
            $evenrunresult =  self::CalculateForEvenRun($matchid,$liveinningnumber,$over);
            return $evenrunresult;
        }

        if($type == 'first_ball_scoring'){
            $firstbalresult = self::CalculateForFirstBallScore($matchid,$liveinningnumber,$over);
            return $firstbalresult;
        }

        if($type == 'boundary_in_over'){
            $boundaryresult = self::CalculateForBoundaryInOver($matchid,$liveinningnumber,$over);
            return $boundaryresult;
        }

        if($type == 'six_in_over'){
            $sixresult = self::CalculateForSixInOver($matchid,$liveinningnumber,$over);
            return $sixresult;
        }

        if($type == 'more_than_three_dot_balls'){
            $threedotballresult = self::CalculateForMoreThanThreeDotBall($matchid,$liveinningnumber,$over);
            return $threedotballresult;
        }


        if($type == 'one_wicket_in_over'){
            $onewicketresult = self::CalculateForOneWicketInOver($matchid,$liveinningnumber,$over);
            return $onewicketresult;
        }

        if($type == 'more_than_two_double_runs'){
            $twodoubleresult = self::CalculateForMoreThanTwoDoubleRun($matchid,$liveinningnumber,$over);
            return $twodoubleresult;
        }

        if($type == 'more_than_two_boundaries'){
            $twoboundaryresult = self::CalculateForMoreThanTwoBoundary($matchid,$liveinningnumber,$over);
            return $twoboundaryresult;
        }

        if($type == 'total_in_over_more_than_seven_runs'){
            $morethansevenresult = self::CalculateForMoreThanSevenRun($matchid,$liveinningnumber,$over);
            return $morethansevenresult;
        }

        if($type == 'no_ball_in_over'){
            $noballresult = self::CalculateForNoBallInOver($matchid,$liveinningnumber,$over);
            return $noballresult;
        }

        if($type == 'wide_in_over'){
            $wideresult = self::CalculateForWideInOver($matchid,$liveinningnumber,$over);
            return $wideresult;
        }

        if($type == 'lbw_in_over'){
            $result = self::CalculateForLbwInOver($matchid,$liveinningnumber,$over);
        }

        if($type == 'maiden_over'){
            $madineresult = self::CalculateForMadineOver($matchid,$liveinningnumber,$over);
            return $madineresult;
        }

        if($type == 'out_for_duck'){
            $result = self::CalculateForOutForDuck($matchid,$liveinningnumber,$over);
        }

    }


    //child Function
    public static function CalculateForEvenRun($matchid,$liveinningnumber,$over){
        //========== calculation here======//
        $evenrun = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->sum('run');

        // Check if the number is odd or even
        if ($evenrun % 2 == 0) {
            return "true";
        } else {
            return "false";
        }

    }

    public static function CalculateForFirstBallScore($matchid,$liveinningnumber,$over){
        //============== calculate here ========///
        $firstBallScore = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->where('ball_no' , '1')->value('run');
        if($firstBallScore != 0){
            return "true";
        }else{
            return "false";
        }

    }

    public static function CalculateForBoundaryInOver($matchid,$liveinningnumber,$over){
        //============== calculate here====///
        $boundary = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->where('four' , '1')->count();
        if($boundary != 0){
            return "true";
        }else{
            return "false";
        }
    }


    public static function CalculateForSixInOver($matchid,$liveinningnumber,$over){
        //============== calculate here====///
        $six = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->where('six' , '1')->count();
        if($six != 0){
            return "true";
        }else{
            return "false";
        }
    }

    public static function CalculateForMoreThanThreeDotBall($matchid,$liveinningnumber,$over){
        //============== calculate here
        $threeDotBall = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->where('score' , '0')->count();
        if($threeDotBall > 3){
            return "true";
        }else{
            return "false";
        }
    }

    public static function CalculateForOneWicketInOver($matchid,$liveinningnumber,$over){
        //============== calculate here
        $wicketinover = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->where('score' , 'w')->count();
        if($wicketinover > 1){
            return "true";
        }else{
            return "false";
        }
    }

    public static function CalculateForMoreThanTwoDoubleRun($matchid,$liveinningnumber,$over){
        //============== calculate here
        $twodouble = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->where('bat_run' , '2')->count();
        if($twodouble > 2){
            return "true";
        }else{
            return "false";
        }
    }

    public static function CalculateForMoreThanTwoBoundary($matchid,$liveinningnumber,$over){
        //============== calculate here
        $twoboundary = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->where('four' , '1')->count();
        if($twoboundary < 2){
            return "true";
        }else{
            return "false";
        }
    }

    public static function CalculateForMoreThanSevenRun($matchid,$liveinningnumber,$over){
        //========== calculation here======//
        $sevenrun = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->sum('run');

        if ($sevenrun > 7) {
            return "true";
        } else {
            return "false";
        };
    }

    public static function CalculateForNoBallInOver($matchid,$liveinningnumber,$over){
        //========== calculation here======//
        $noball = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->where('noball' , '1')->count();

        if ($noball != 0) {
            return "true";
        } else {
            return "false";
        };
    }

    public static function CalculateForWideInOver($matchid,$liveinningnumber,$over){
       //========== calculation here======//
       $wideball = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->where('wideball' , '1')->count();

       if ($wideball != 0) {
           return "true";
       } else {
           return "false";
       };
    }

    public static function CalculateForLbwInOver($matchid,$liveinningnumber,$over){
        //============== calculate here
        return true;
    }

    public static function CalculateForMadineOver($matchid,$liveinningnumber,$over){
        //========== calculation here======//
        $madineover = Overballes::where('match_id' , $matchid)->where('innings' , $liveinningnumber)->where('over_no' , $over)->sum('run');

        if ($madineover == 0) {
            return "true";
        } else {
            return "false";
        }
    }

    public static function CalculateForOutForDuck($matchid,$liveinningnumber,$over){
        //============== calculate here
        return true;
    }



    public static function CompetionDetail($compotitionid){
        $datamatchescomp = Competition::where('competiton_id', $compotitionid)->first();
        return $datamatchescomp;
    }

    // Get Predicted Overs
    public static function predictedOvers($matchId){
        $dataPrediction = Prediction::where('match_id', $matchId)->groupBy('over_id')->with('inningsOvers')->get();
        return $dataPrediction;
    }

    // Get Predicted Overs Count
    public static function predictedOverCount($matchId){
        $dataPrediction = Prediction::where('match_id', $matchId)->count();
        return $dataPrediction;
    }

}
