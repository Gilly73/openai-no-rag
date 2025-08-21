<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReplyDraftController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/reply-drafter', [ReplyDraftController::class, 'show'])->name('reply.show');
Route::post('/reply-drafter', [ReplyDraftController::class, 'draft'])->name('reply.draft');
