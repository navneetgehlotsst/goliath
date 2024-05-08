<?php

namespace App\Helpers;

use DB, Auth, File, Mail;
use Carbon\Carbon;

use App\Models\{
    User,
};


class QuestionHelper
{
    //parent Function
    public static function QuestionType($type,$over_id){
        echo $type;
        die;
        if($type == 'even_runs_in_over'){
            $result =  self::CalculateForEvenRun($over_id);
        }

        if($type == 'first_ball_scoring'){
            $result = self::CalculateForFirstBallScore($over_id);
        }

        if($type == 'boundary_in_over'){
            $result = self::CalculateForBoundaryInOver($over_id);
        }

        if($type == 'six_in_over'){
            $result = self::CalculateForSixInOver($over_id);
        }

        if($type == 'more_than_three_dot_balls'){
            $result = self::CalculateForMoreThanThreeDotBall($over_id);
        }


        if($type == 'one_wicket_in_over'){
            $result = self::CalculateForOneWicketInOver($over_id);
        }

        if($type == 'more_than_two_double_runs'){
            $result = self::CalculateForMoreThanTwoDoubleRun($over_id);
        }

        if($type == 'more_than_two_boundaries'){
            $result = self::CalculateForMoreThanTwoBoundary($over_id);
        }

        if($type == 'total_in_over_more_than_seven_runs'){
            $result = self::CalculateForMoreThanSevenRun($over_id);
        }

        if($type == 'no_ball_in_over'){
            $result = self::CalculateForNoBallInOver($over_id);
        }

        if($type == 'wide_in_over'){
            $result = self::CalculateForWideInOver($over_id);
        }

        if($type == 'lbw_in_over'){
            $result = self::CalculateForLbwInOver($over_id);
        }

        if($type == 'maiden_over'){
            $result = self::CalculateForMadineOver($over_id);
        }

        if($type == 'out_for_duck'){
            $result = self::CalculateForOutForDuck($over_id);
        }
    }


    //child Function
    public static function CalculateForEvenRun($over_id){
        //========== calculation here======//
        return true;
    }

    public static function CalculateForFirstBallScore($over_id){
        //============== calculate here
        return true;
    }

    public static function CalculateForBoundaryInOver($over_id){
        //============== calculate here
        return true;
    }


    public static function CalculateForSixInOver($over_id){
        //============== calculate here
        return true;
    }

    public static function CalculateForMoreThanThreeDotBall($over_id){
        //============== calculate here
        return true;
    }

    public static function CalculateForOneWicketInOver($over_id){
        //============== calculate here
        return true;
    }

    public static function CalculateForMoreThanTwoDoubleRun($over_id){
        //============== calculate here
        return true;
    }

    public static function CalculateForMoreThanTwoBoundary($over_id){
        //============== calculate here
        return true;
    }

    public static function CalculateForMoreThanSevenRun($over_id){
        //============== calculate here
        return true;
    }

    public static function CalculateForNoBallInOver($over_id){
        //============== calculate here
        return true;
    }

    public static function CalculateForWideInOver($over_id){
        //============== calculate here
        return true;
    }

    public static function CalculateForLbwInOver($over_id){
        //============== calculate here
        return true;
    }

    public static function CalculateForMadineOver($over_id){
        //============== calculate here
        return true;
    }

    public static function CalculateForOutForDuck($over_id){
        //============== calculate here
        return true;
    }


}
