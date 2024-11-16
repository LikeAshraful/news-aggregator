<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route ::get('/call-api', function () {

    //use NewsFetcherService to fetch and save news
    try {
        $newsFetcherService = new \App\Services\NewsFetcherService();
        $newsFetcherService->fetchAndSaveNews();
    } catch (\Exception $e) {
        return $e->getMessage();
    }
    
    return 'News fetched and saved successfully';
});




