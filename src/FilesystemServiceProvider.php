<?php

namespace Madlux\Filesystem;

use Illuminate\Support\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
	
	public function register()
	{
		/*
		App::bind('MyFilter', function ($app) {
			return new App\Http\MyFilter;
		});
		*/
	}
	
	public function boot()
	{
		$this->loadViewsFrom(__DIR__.'/views', 'users');
		
		$this->publishes([
			__DIR__.'/assets' => public_path('users'),
		], 'public');
		
		$this->publishes([
			__DIR__.'/config/madlux_files_settings.php' => config_path('madlux_files_settings.php'),
		], 'config');
		
		include __DIR__.'/config/routes.php';
		/*
		$this->publishes([
			__DIR__.'config/settings.php' => config_path('settings.php'),
		]);
		*/
	}
}
