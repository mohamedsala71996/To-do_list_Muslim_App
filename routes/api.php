<?php

use App\Http\Controllers\Api\App\MediaPhotoController as AppMediaPhotoController;
use App\Http\Controllers\Api\App\NoteController;
use App\Http\Controllers\Api\App\TaskController as AppTaskController;
use App\Http\Controllers\Api\Auth\AdminAuthController;
use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\Dashboard\GeneralPercentageController;
use App\Http\Controllers\Api\Dashboard\MediaPhotoController;
use App\Http\Controllers\Api\Dashboard\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('dashboard/admin')->group(function () {
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware(['auth:sanctum,admin', 'type.admin']);
});

Route::group(['prefix' => 'dashboard','middleware'=>'auth:sanctum'], function () {
    Route::get('tasks/get-all', [TaskController::class,'index']);

    Route::post('tasks/create', [TaskController::class,'store']);

    Route::post('tasks/update/{id}', [TaskController::class,'update']); //daily_plan_id

    Route::post('tasks/delete/{daily_plan_id}', [TaskController::class,'destroy']);


    // general Percentage
    Route::post('general-percentage/create', [GeneralPercentageController::class,'storeOrUpdate']);

    Route::post('general-percentage/delete', [GeneralPercentageController::class,'destroy']);


    // media photos
    Route::get('media-photos', [MediaPhotoController::class, 'index']);

    Route::post('media-photos/create', [MediaPhotoController::class, 'store']);


    Route::post('media-photos/update', [MediaPhotoController::class, 'update']);

    Route::post('media-photos/delete', [MediaPhotoController::class, 'destroy']);


});
Route::prefix('user')->group(function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');
});


Route::middleware('auth:sanctum')->group(function () {

    Route::get('tasks/get-all', [AppTaskController::class,'index']);

    Route::post('tasks/create/{daily_plan_id}', [AppTaskController::class,'store']);

    Route::get('tasks/show/{daily_plan_id}', [AppTaskController::class,'show']);

    Route::get('tasks/get-percentage', [AppTaskController::class,'getPercentage']);


    Route::get('tasks/everyday-percentage', [AppTaskController::class,'everyDayPercenage']);


    Route::get('notes', [NoteController::class, 'getAllNotes']);

    Route::get('lists', [NoteController::class, 'getAllLists']);

    Route::post('notes/create', [NoteController::class, 'store']);

    Route::get('notes/show-notes', [NoteController::class, 'showNotes']);

    Route::get('notes/show-lists', [NoteController::class, 'showLists']);

    Route::post('delete-notes', [NoteController::class, 'destroyNotes']);

    Route::post('delete-lists', [NoteController::class, 'destroyLists']);

    // media photos
    Route::get('media-photos', [AppMediaPhotoController::class, 'index']);


});
