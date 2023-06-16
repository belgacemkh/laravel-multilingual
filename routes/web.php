<?php

use Illuminate\Support\Facades\Route;

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
Route::get('langue/{lang}', function ($lang) {
    app()->setLocale($lang);
    session()->put('locale', $lang);

    return redirect()->back();
});

Route::get('/', function () {
    return view('welcome');
});

