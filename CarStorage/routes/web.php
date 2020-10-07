<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Crawl;
use App\Http\Controllers\main;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Startsite
//list out cars 
//Get for Seacrch impement with category/reg/brand...
Route::get('/', function () {
    return view('index', ['cars' => main::init()]);
});
//Crawler site
//add admin to function
//Delete data for renew
//Set add option to add License plate
//storedata to database
Route::get('/crawler', function () {
    return view('crawl', ['crawl' => Crawl::GetData()]);
});
Route::post('/search', function (Request $req) {
    return view('index', ['cars' => main::search($req)]);
});