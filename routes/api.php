<?php

use App\Http\Controllers\api\Admincontoller;
use App\Http\Controllers\api\QuestionController;
use App\Http\Controllers\api\QuizController;
use App\Http\Controllers\api\QuizControllerValidation;
use App\Http\Controllers\api\QuizCrudController;
use App\Http\Controllers\api\ProfileController;
use App\Http\Controllers\api\DashboardController;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum','web'])->prefix("user")->group(function () {
    Route::get('/', function (Request $request) { return $request->user(); });

    Route::get('/show', [ProfileController::class, 'show']) ;
    Route::put('/update', [ProfileController::class, 'update']) ;
    Route::delete('/remove', [ProfileController::class, 'destroy']);
    Route::get('/dashboard', [DashboardController::class,'index']);
    Route::get('/myscore', [Admincontoller::class, 'myscore']);

});




// Move the 'notfound' route to api.php if it's used for API responses
Route::get("/notfound", function () {
    return response()->json(['message' => 'Not Found'], 404);
});

// Move other API routes from web.php to api.php
Route::middleware(['web','auth:sanctum', 'isadmin'])->prefix('admin')->group(function () {
    Route::resource('quizzes', QuizCrudController::class);
    Route::post('quizzes/{quizId}', [QuizCrudController::class,"update"]);

    Route::resource('questions', QuestionController::class);
    Route::get('/associate', [QuizCrudController::class, 'associate']);
    Route::post('/submetassociate', [QuizCrudController::class, 'submetassociate']);

    Route::get('/editanswer', [QuizCrudController::class, 'editanswer']);
    Route::put('/updateanswer/{idanswer}', [QuizCrudController::class, 'updateanswer']);
    Route::delete('/destroyanswer/{idanswer}', [QuizCrudController::class, 'destroyanswer']);



    Route::get('/', [Admincontoller::class, 'index']);
    Route::get('/allusers', [Admincontoller::class, 'allusers']);
    Route::get('/activate/{id}', [Admincontoller::class, 'activateUser']);
    Route::get('/adminateuser/{id}', [Admincontoller::class, 'adminateuser']);

    //Route::get('/removeuser/{id}', [Admincontoller::class, 'delete'])->name('admin.user.remove');
    //Route::put('/edituser/{id}', [Admincontoller::class, 'edituser'])->name('admin.user.edit');
    Route::get('/userscores', [Admincontoller::class, 'userscores']);

    //for poinment
    Route::get('/points', [Admincontoller::class, 'points']);
    Route::post('/points', [Admincontoller::class, 'submitpoints']);



});
  // for aprontisage
Route::middleware(['auth:sanctum', 'isactive','web'])->prefix('quiz')->group(function () {
     Route::get('/{identify}', [Quizcontroller::class, 'startQuiz']);
    Route::get('/next/{identify}', [Quizcontroller::class, 'index']);
    Route::post('/submit', [Quizcontroller::class, 'submitAnswers']) ;

});


// for validation


Route::middleware(['auth:sanctum', 'isactive','web'])->prefix('validation')->group(function () {
    Route::get('/{identify}', [Quizcontrollervalidation::class, 'startQuiz']);
    Route::get('/next/{identify}', [Quizcontrollervalidation::class, 'index']);
    Route::post('/submit', [Quizcontrollervalidation::class, 'submitAnswers']);

});







Route::get('/userscores', [Admincontoller::class, 'userscores'])->name('quiz.userscores');



