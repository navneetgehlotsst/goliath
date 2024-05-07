<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\{
    AuthController,
    ContactController,
    UserController,
    HowToPlayController,
    CompetitionController,
    MatchesController,
    UserPredictionController
};



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/splash-screen', [AuthController::class, 'splashScreens']);
Route::post('/contact', [ContactController::class, 'submitContact']);
Route::post('/how-to-play', [HowToPlayController::class, 'howToPlay']);

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/send-otp', 'sendOtp');
    Route::post('/verify-otp', 'verifyOtp');
    Route::post('/login', 'login');
});

Route::middleware('jwt.verify')->group(function() {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
    Route::post('/add-wallet', [AuthController::class, 'addWallet']);
    Route::post('/competition-list', [CompetitionController::class, 'competitionList']);
    Route::post('/matches-list', [MatchesController::class, 'matchesList']);
    Route::post('/matches-detail', [MatchesController::class, 'matchesDetail']);
    Route::post('/question-list-for-over', [MatchesController::class, 'questionListForOver']);
    Route::post('/save-user-prediction', [UserPredictionController::class, 'saveUserPrediction']);
    Route::post('/user-prediction', [UserPredictionController::class, 'getUserPrediction']);
    Route::post('/confirm-prediction', [UserPredictionController::class, 'confirmPrediction']);
    Route::post('/get-predictions', [UserPredictionController::class, 'getPredictions']);
    Route::post('/mypredictions', [UserPredictionController::class, 'listPredictions']);
});
