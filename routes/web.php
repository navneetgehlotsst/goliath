<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;

use App\Http\Controllers\Admin\{
    AdminAuthController,
    PageController,
    ContactController,
    NotificationController,
    AdminUserController,
    TransactionController,
    QuestionsController,
    HowToPlayController,
    CompetitionController,
    MatchController,
    PredictedController,
    LeaderboardController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/test', [HomeController::class, 'croncheck']);
Route::get('/testtwo', [HomeController::class, 'cronchecktwo']);

Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('/', [AdminAuthController::class, 'index']);

    Route::get('login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('login', [AdminAuthController::class, 'postLogin'])->name('login.post');
    Route::get('forget-password', [AdminAuthController::class, 'showForgetPasswordForm'])->name('forget.password.get');
    Route::post('forget-password', [AdminAuthController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
    Route::get('reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('reset-password', [AdminAuthController::class, 'submitResetPasswordForm'])->name('reset.password.post');


    Route::middleware(['admin'])->group(function () {
        Route::get('dashboard', [AdminAuthController::class, 'adminDashboard'])->name('dashboard');
        Route::get('change-password', [AdminAuthController::class, 'changePassword'])->name('change.password');
        Route::post('update-password', [AdminAuthController::class, 'updatePassword'])->name('update.password');
        Route::get('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('profile', [AdminAuthController::class, 'adminProfile'])->name('profile');
        Route::post('profile', [AdminAuthController::class, 'updateAdminProfile'])->name('update.profile');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('', [AdminUserController::class, 'index'])->name('index');
            Route::post('status', [AdminUserController::class, 'userStatus'])->name('status');
            Route::delete('delete/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
            Route::get('{user}', [AdminUserController::class, 'show'])->name('show');
            Route::get('match-prediction/{user}/{matchId}', [AdminUserController::class, 'matchPrediction'])->name('match.prediction');
            Route::get('prediction-result/{user}/{matchId}/{overid}', [AdminUserController::class, 'predictionResult'])->name('prediction.result');
        });

        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('', [ContactController::class, 'index'])->name('index');
            Route::get('all', [ContactController::class, 'getallcontact'])->name('allcontact');
            Route::delete('delete/{contact}', [ContactController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('how-to-play')->name('how-to-play.')->group(function () {
            Route::get('list', [HowToPlayController::class, 'index'])->name('index');
            Route::get('all', [HowToPlayController::class, 'getallhowtoplay'])->name('allhowtoplay');
            Route::delete('delete/{howToPlay}', [HowToPlayController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('transaction')->name('transaction.')->group(function () {
            Route::get('', [TransactionController::class, 'index'])->name('index');
            Route::get('all', [TransactionController::class, 'getalltransaction'])->name('alltransaction');
        });

        Route::prefix('questions')->name('questions.')->group(function () {
            Route::get('', [QuestionsController::class, 'index'])->name('index');
            Route::get('all', [QuestionsController::class, 'getallquestions'])->name('allquestions');
        });

        Route::prefix('page')->name('page.')->group(function () {
            Route::get('create/{key}', [PageController::class, 'create'])->name('create');
            Route::put('update/{key}', [PageController::class, 'update'])->name('update');
        });

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('index', [NotificationController::class, 'index'])->name('index');
            Route::get('clear', [NotificationController::class, 'clear'])->name('clear');
            Route::delete('delete/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('competition')->name('competition.')->group(function () {
            Route::get('live', [CompetitionController::class, 'live'])->name('live');
            Route::get('completed', [CompetitionController::class, 'completed'])->name('completed');
            Route::get('upcoming', [CompetitionController::class, 'upcoming'])->name('upcoming');
        });

        Route::prefix('match')->name('match.')->group(function () {
            Route::get('index/{cid}', [MatchController::class, 'index'])->name('index');
            Route::get('info/{key}', [MatchController::class, 'matchInfo'])->name('info');
            Route::get('question/{overid}', [MatchController::class, 'matchQuestion'])->name('question');
            Route::post('change-question', [MatchController::class, 'changeQuestion'])->name('chnagequestion');
        });

        Route::prefix('predict')->name('predict.')->group(function () {
            Route::get('info/{key}', [PredictedController::class, 'pridictedInfo'])->name('info');
            Route::post('user', [PredictedController::class, 'pridictedUser'])->name('user');
            Route::get('list', [PredictedController::class, 'pridictedlist'])->name('list');
            Route::get('all', [PredictedController::class, 'getallpridicted'])->name('allpridicted');
        });

        Route::prefix('leaderboard')->name('leaderboard.')->group(function () {
            Route::get('list', [LeaderboardController::class, 'leaderboardlist'])->name('list');
        });
    });
});

Route::middleware(['auth'])->group(function () {

});



