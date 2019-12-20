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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/friend', 'FriendController@index')->name('friend');

Route::get('/message', 'MessageController@index')->name('message');

Route::get('/threads', 'MessageController@threadCenter')->name('threads');

Route::get('/profile', 'ProfileController@index')->name('profile');

Route::get('/community', 'CommunityController@index')->name('community');

Route::get('/community-join', 'CommunityController@reqres')->name('joincomu');

Route::get('/community-history', 'CommunityController@history')->name('comuhis');

Route::get('/mapview', 'MessageController@mapview')->name('mapview');

// RESTFUL api

// Community

Route::get('/aprvother', 'CommunityController@aprvother')->name('aprvother');

Route::get('/quitblock', 'CommunityController@quitblock')->name('quitblock');

// Friend & Neighbor
Route::get('/addneighbor', 'FriendController@addneighbor')->name('addneighbor');

Route::get('/addfriend', 'FriendController@addfriend')->name('addfriend');

Route::get('/apvfriend', 'FriendController@apvfriend')->name('apvfriend');

Route::get('/rejfriend', 'FriendController@rejfriend')->name('rejfriend');

Route::get('/getfrinei', 'FriendController@getfrinei')->name('getfrinei');

// Fetch friend chat message
Route::get('/quickFetch', 'MessageController@quickMessageFetch')->name('quickFetch');

Route::get('/getFriendChat', 'MessageController@getFriendChat')->name('getFriendChat');

Route::get('/sendFriendChat', 'MessageController@sendFriendChat')->name('sendFriendChat');

// Group Thread Message
Route::get('/initGroupMessage', 'MessageController@initGroupMessage')->name('initGroupMessage');

Route::get('/getThreads', 'MessageController@getThreadList')->name('getThreads');

Route::get('/getThreadMessages', 'MessageController@getThreadMessages')->name('getThreadMessages');

Route::get('/normalGroupMessage', 'MessageController@normalGroupMessage')->name('normalGroupMessage');

// Local Broadcast Message
Route::get('/getLocalMessages', 'MessageController@getLocalMessages')->name('getLocalMessages');

Route::get('/broadcastMessage', 'MessageController@broadcastMessage')->name('broadcastMessage');

// Search in home page
Route::get('/search', 'MessageController@search')->name('search');
 
// Get or update my information 
Route::get('/getmyinfo', 'ProfileController@getmyinfo')->name('getmyinfo');

Route::get('/updatemyinfo', 'ProfileController@updatemyinfo')->name('updatemyinfo');

// For TEST PASSWORD
Route::get('/test', 'HomeController@test')->name('test');
