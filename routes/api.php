<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\ReplyController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Posts
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle']);
    Route::post('/posts/{post}/view', [PostController::class, 'incrementView']);

    // Comments
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy']);
    Route::post('/comments/{comment}/like', [CommentLikeController::class, 'toggle']);

    // Replies
    Route::post('/comments/{comment}/replies', [ReplyController::class, 'store']);
    Route::delete('/comments/{comment}/replies/{reply}', [ReplyController::class, 'destroy']);
    Route::post('/comments/{comment}/replies/{reply}/like', [ReplyController::class, 'toggleLike']);

    // Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', function () {
            return response()->json(\App\Models\User::all());
        });
    });
});
