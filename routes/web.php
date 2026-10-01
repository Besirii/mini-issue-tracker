<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\IssueMemberController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('projects.index'));

/*
|--------------------------------------------------------------------------
| Authentication (simple, enables the project ownership policy)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Write operations (require authentication). Registered before the public
| wildcard routes so static segments like /create resolve first.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class)->except(['index', 'show']);
    Route::resource('issues', IssueController::class)->except(['index', 'show']);

    Route::post('tags', [TagController::class, 'store'])->name('tags.store');

    // Attach / detach tags on an issue (AJAX).
    Route::post('issues/{issue}/tags', [IssueTagController::class, 'store'])->name('issues.tags.store');
    Route::delete('issues/{issue}/tags/{tag}', [IssueTagController::class, 'destroy'])->name('issues.tags.destroy');

    // Assign / remove members on an issue (AJAX, bonus).
    Route::post('issues/{issue}/members', [IssueMemberController::class, 'store'])->name('issues.members.store');
    Route::delete('issues/{issue}/members/{user}', [IssueMemberController::class, 'destroy'])->name('issues.members.destroy');
});

/*
|--------------------------------------------------------------------------
| Public read routes
|--------------------------------------------------------------------------
*/
Route::resource('projects', ProjectController::class)->only(['index', 'show']);

Route::get('issues', [IssueController::class, 'index'])->name('issues.index');
Route::get('issues/{issue}', [IssueController::class, 'show'])->name('issues.show');

Route::get('tags', [TagController::class, 'index'])->name('tags.index');

// Comments (load + add via AJAX). Adding a comment is open (uses author_name).
Route::get('issues/{issue}/comments', [CommentController::class, 'index'])->name('issues.comments.index');
// Public endpoint, so it is rate-limited: max 10 comments per minute per client.
Route::post('issues/{issue}/comments', [CommentController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('issues.comments.store');
