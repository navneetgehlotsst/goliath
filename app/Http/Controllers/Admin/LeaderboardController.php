<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Mail,Hash,File,Auth,DB,Helper,Exception,Session,Redirect;
use Carbon\Carbon;
use App\Mail\ContactMail;
use App\Models\{
    Prediction,
    User
};

class LeaderboardController extends Controller
{
    public function leaderboardlist()
    {
        try {
            // Removing a value from the session
            Session::forget('previousURL');
            // Get the current date, month, and year
            $currentDate = Carbon::today();
            $currentMonth = $currentDate->format('m');
            $currentYear = $currentDate->format('Y');

            // Initialize arrays for storing predictions
            $dailyPrediction = [];
            $monthlyPrediction = [];
            $yearlyPrediction = [];

            // Fetch active users with the role 'user'
            $activeUsers = User::where('status', 'active')->where('role', 'user')->get();

            foreach ($activeUsers as $user) {
                // Fetch and process daily predictions
                $dailyPrediction[$user->id] = $this->processPredictions($user->id, $currentDate, 'daily');
                // Fetch and process monthly predictions
                $monthlyPrediction[$user->id] = $this->processPredictions($user->id, $currentYear, 'monthly', $currentMonth);
                // Fetch and process yearly predictions
                $yearlyPrediction[$user->id] = $this->processPredictions($user->id, $currentYear, 'yearly');
            }

            // Sort and limit the predictions to the top 10 entries
            $topDailyPredictions = $this->getTopPredictions($dailyPrediction);
            $topMonthlyPredictions = $this->getTopPredictions($monthlyPrediction);
            $topYearlyPredictions = $this->getTopPredictions($yearlyPrediction);
            return view('admin.leaderboard.index',compact('topDailyPredictions','topMonthlyPredictions','topYearlyPredictions'));
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }


    // Function to process predictions based on time frame
    public function processPredictions($userId, $year, $timeFrame, $month = null) {
        // Build the query based on the time frame
        $query = Prediction::select(
            'user_id',
            'over_id',
            DB::raw('DATE(created_at) AS Date'),
            DB::raw('SUM(IF(result = "W", 1, 0)) AS win_count')
        )
        ->where('status', 'complete')
        ->where('user_id', $userId)
        ->whereYear('created_at', $year);

        if ($timeFrame === 'daily') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($timeFrame === 'monthly') {
            $query->whereMonth('created_at', $month);
        }

        $predictions = $query->groupBy('user_id', 'over_id')->get();

        // Initialize counters
        $totalGoliathWinner = 0;
        $totalWinner = 0;
        $totalLoser = 0;

        // Calculate the counts based on win_count
        foreach ($predictions as $prediction) {
            if ($prediction->win_count == 8) {
                $totalGoliathWinner++;
            } elseif ($prediction->win_count >= 5) {
                $totalWinner++;
            } else {
                $totalLoser++;
            }
        }

        // Calculate total winnings
        $totalWinning = $totalGoliathWinner + $totalWinner;

        // Return the result array
        return [
            'name' => User::find($userId)->full_name,
            'total_winning' => $totalWinning,
        ];
    }

    // Function to sort and limit predictions to top 10
    public function getTopPredictions($predictions) {
        usort($predictions, function ($a, $b) {
            return $b['total_winning'] <=> $a['total_winning'];
        });
        return array_slice($predictions, 0, 10);
    }
}
