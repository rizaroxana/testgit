<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/produse', 'ProdusController@index')->name('produse');
Route::get('/produs/create', 'ProdusController@create');
Route::post('/produs', 'ProdusController@store');
Route::get('/produs/intrari/{productId}', 'ProdusController@intrari');
Route::get('/produs/intrare/{productId}', function ($productId) {
    return view('create_intrare',compact('productId'));

});
Route::post('/produs/intrare', 'ProdusController@storeIntrare');