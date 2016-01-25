<?php

Route::any('/users', 'Packages\Users\Controllers\UsersController@indexAction');
Route::any('/users/files', 'Packages\Users\Controllers\FilesController@indexAction');
Route::any('/users/files/save', 'Packages\Users\Controllers\FilesController@saveFileAction');
Route::any('/users/files/delete', 'Packages\Users\Controllers\FilesController@deleteFileAction');
Route::any('/users/verify', 'Packages\Users\Controllers\VerifyController@verifyAction');