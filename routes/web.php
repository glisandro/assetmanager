<?php

use App\Models\Attachment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/attachment/{attachment}', function (Attachment $attachment) {
    return redirect(Storage::url($attachment->filename));
})->name('attachment.open');
