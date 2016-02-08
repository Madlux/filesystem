<?php

Route::group(['middleware' => 'auth'], function () {
	Route::any('/files', 'Madlux\Filesystem\Controllers\FilesController@indexAction');
	Route::any('/files/save', 'Madlux\Filesystem\Controllers\FilesController@saveFileAction');
	Route::any('/files/delete', 'Madlux\Filesystem\Controllers\FilesController@deleteFileAction');
	Route::any('/files/update', 'Madlux\Filesystem\Controllers\FilesController@updateAction');
	Route::any('/files/folder/create', 'Madlux\Filesystem\Controllers\FilesController@createFolderAction');
});