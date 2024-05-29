<?php

namespace App\Console\Commands;

use App\Models\{
    CompetitionMatches,
    Competition,
    Overballes,
    Prediction,
    MatchInnings,
    InningsOver,
    Transaction,
    Winning,
};
use Carbon\Carbon;

use Mail, Hash, File, DB, Helper, Auth;

use Illuminate\Console\Command;

class PredictionWinners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prediction-winners';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Declear winners and reward them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info("PredictionWinners: INIT");

        try {
            // API Token
            $token = env('SPORT_API_TOKEN');
            $predictionAmount = env('BETING_AMOUNT');

            // Get Prediction data for win and no result
            $predictionMatches = Prediction::whereIn('result',['W','NR'])->where('is_payment','pending')->groupBy('match_id','user_id','over_id')->get();
            if(!empty($predictionMatches)){
                foreach($predictionMatches as $predictionMatch){
                    $matchId = $predictionMatch->match_id;
                    $userId = $predictionMatch->user_id;
                    $overId = $predictionMatch->over_id;

                    //check predictions
                    if($predictionMatch->result == "W"){
                        $overWinnings = Prediction::where('is_payment','pending')->where('match_id',$matchId)->where('user_id',$userId)->where('over_id',$overId)->where('result','W')->count();
                        if($overWinnings >= 5){
                            \Log::info("OverID:".$overId."WinType:".$overWinnings);

                            $winning               =   new Winning();
                            $winning->user_id      =   $userId;
                            $winning->match_id     =   $matchId;
                            $winning->over_id      =   $overId;
                            $winning->win_type     =   (string) $overWinnings;
                            $winning->save();
                         
                        }
                        
                        //update is_payment status in prediction table 
                        Prediction::where('is_payment','complete')->where('match_id',$matchId)->where('over_id',$overId)->update(['is_payment'=>'complete']);

                    }else if($predictionMatch->result == "NR"){
                        
                        //update is_payment status in prediction table to refund
                        Prediction::where('is_payment','pending')->where('match_id',$matchId)->where('user_id',$userId)->where('over_id',$overId)->update(['is_payment'=>'refund']);

                        //update refund in wallet and transaction here
                        $refundAmount = $predictionAmount;
                        User::where('id', $userId)->increment('wallet', $refundAmount);

                        $dataTran = [
                            'user_id' => $userId,
                            'amount' =>  $refundAmount,
                            'payment_id' =>  NULL,
                            'transaction_type' => "refund",
                            'payment_mode' => "credit",
                            'reference_id' => $overId,
                            'note' => "Prediction fees refunded due to over incomplete."
                        ];
            
                        Transaction::create($dataTran);
                        
                    }
                }

                //winning logic
                $totalPredictions = Prediction::where('is_payment','complete')->groupBy('match_id','user_id','over_id')->count();
                $overTotalRevenue = !empty($totalPredictions) ? ($totalPredictions*$predictionAmount):0;
                $poolPricePercentage = env('POOL_PRICE');
                if($overTotalRevenue > 0){
                    $poolPrice = (($overTotalRevenue*$poolPricePercentage)/100);

                    $eightWinners = Winning::where('win_type',8)->where('status'=>0)->get();
                    $sevenWinners = Winning::where('win_type',7)->where('status'=>0)->get();
                    $sixWinners = Winning::where('win_type',6)->where('status'=>0)->get();
                    $fiveWinners = Winning::where('win_type',5)->where('status'=>0)->get();

                    $totalEightWinners  =   !empty($eightWinners) ? count($eightWinners) : 0;
                    $totalSevenWinners  =   !empty($sevenWinners) ? count($sevenWinners) : 0;
                    $totalSixWinners    =   !empty($sixWinners) ? count($sixWinners) : 0;
                    $totalFiveWinners   =   !empty($fiveWinners) ? count($fiveWinners) : 0;

                    if($totalEightWinners > 0 && $totalSevenWinners > 0 && $totalSixWinners > 0 && $totalFiveWinners > 0){
                        $totalEightWinnersPool  = (($poolPrice*50)/100);
                        $remainnigPoolPrice     = ($poolPrice-$totalEightWinnersPool);

                        $totalSevenWinnersPool  = (($poolPrice*25)/100);
                        $remainnigPoolPrice     = ($remainnigPoolPrice-$totalSevenWinnersPool);

                        $totalSixWinnersPool  = (($poolPrice*15)/100);
                        $remainnigPoolPrice     = ($remainnigPoolPrice-$totalSixWinnersPool);

                        $totalFiveWinnersPool  = (($poolPrice*10)/100);
                        $remainnigPoolPrice     = ($remainnigPoolPrice-$totalFiveWinnersPool);

                    }else if($totalEightWinners > 0 && $totalSevenWinners > 0 && $totalSixWinners > 0){
                        
                    }else if($totalEightWinners > 0 && $totalSevenWinners > 0 && $totalFiveWinners > 0){
                        
                    }else if($totalEightWinners > 0 && $totalSixWinners > 0 && $totalFiveWinners > 0){
                        
                    }else if($totalSevenWinners > 0 && $totalSixWinners > 0 && $totalFiveWinners > 0){
                        
                    }else if($totalEightWinners > 0 && $totalSevenWinners > 0){
                        
                    }else if($totalEightWinners > 0 && $totalSixWinners > 0){
                        
                    }else if($totalEightWinners > 0 && $totalFiveWinners > 0){
                        
                    }else if($totalSevenWinners > 0 && $totalSixWinners > 0){
                        
                    }else if($totalSevenWinners > 0 && $totalFiveWinners > 0){
                        
                    }else if($totalSixWinners > 0 && $totalFiveWinners > 0){
                        
                    }else if($totalEightWinners > 0){
                        
                    }else if($totalSevenWinners > 0){
                        
                    }else if($totalSixWinners > 0){
                        
                    }else if($totalFiveWinners > 0){
                        
                    }

                }
            }

        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }
    }
}
