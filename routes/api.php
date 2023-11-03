<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api_key')->group(function(){
Route::get('/games/all', 'App\Http\Controllers\GameController@index');
Route::post('/games/show', 'App\Http\Controllers\GameController@show');
Route::post('/games/store', 'App\Http\Controllers\GameController@store');
Route::post('/games/combination', 'App\Http\Controllers\GameController@combination');
Route::delete('/games/destroy', 'App\Http\Controllers\GameController@destroy');
}); 

