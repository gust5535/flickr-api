<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//Route::get('/', function()
//{
//	return View::make('hello');
//});

//Route::get('/', 'FlickrController@index');
//dashboard
Route::match(array('GET', 'POST'), '/', array('as' => 'home', 'uses' => 'FlickrController@index'));

Route::get('/exchangeToken', array('as' => 'exchangeToken', 'uses' => 'FlickrController@exchangeToken'));

Route::get('/clearSession', 'FlickrController@destroy');

Route::get('/error', 'FlickrController@showError');

