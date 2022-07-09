<?php


Route::middleware('auth:api')->group(function () {

Route::get('/media/share/{type}/{movie}', 'MovieController@share');

});
