<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostCommentRepliesController;
use App\Http\Controllers\PostCommentsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;

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



// only routes that are not protected by sanctum
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

// routes that are protected by sanctum
Route::middleware('auth:sanctum')->group(function () {
    //auth
    Route::post('logout', [AuthController::class, 'logout']);
    //posts
    Route::post('posts', [PostController::class, 'createPost']);
    Route::get('posts', [PostController::class, 'getPosts']);
    Route::post('/posts/{post}/like', LikeController::class);

    //comments
    Route::get('/posts/{post}/comments', [PostCommentsController::class, 'getCommentsWithReplies']);
    Route::post('/posts/{post}/comments', [PostCommentsController::class, 'storeComments']);

    //replies
    Route::post('/comments/{comment}/replies', [PostCommentRepliesController::class, 'store']);


    // settings
    Route::get('/settings', [SettingsController::class, 'show']);
    Route::post('/settings', [SettingsController::class, 'update']);
    Route::post('/settings/avatar', [SettingsController::class, 'updateAvatar']);


    //profile
    Route::get('/users/{user}', [ProfileController::class, 'showUserData']);

    //profiles posts
    Route::get('/users/{user}/posts', [ProfileController::class, 'showUserPosts']);
});
