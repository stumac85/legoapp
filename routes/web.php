<?php

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



Route::get('/', 'test');

Route::get('/about', function () {
    return view('about');
});

Route::get('/samples/legoapp', 'legoapp@index');
Route::post('/samples/legoapp/addItem', 'legoapp@addItem');
Route::get('/samples/legoapp/sessionhell', 'legoapp@sessionHell');
Route::post('/samples/legoapp/removeItem', 'legoapp@removeItem');
Route::post('/samples/legoapp/getSets', 'legoapp@getSets');
Route::get('/samples/legoapp/print/{id}', 'legoapp@pdf');
