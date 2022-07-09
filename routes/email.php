<?php

//Route::get('/email/resend', 'Api\Auth\EmailVerificationController@resend')->name('verification.resend');

Route::get('/email/verify/{id}/{hash}', 'Api\Auth\EmailVerificationController@verify')->name('verification.verify');
