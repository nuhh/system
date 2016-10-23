<?php

	namespace KobimInternette\System;

	use Illuminate\Support\ServiceProvider;

	class SystemServiceProvider extends ServiceProvider
	{

		var $pluginName = 'system';

		var $pluginDescription = 'Sistemin kendisi';

		var $pluginDir = __DIR__;

		var $author = 'Nuh Orun';

		public function boot()
		{
			$this->registerRouteManagement();
			$this->registerViewManagement();
			$this->registerPluginManagement();

			PluginManage::setUp($this->pluginName);



			# burada CertainRoutes.php eklenecek

			$this->loadViewsFrom(__DIR__ . '/Views/Frontend', 'system');
			$this->loadViewsFrom(__DIR__ . '/Views/Admin', 'admin');

			RouteManage::get('asd')->to('asd@asd');
			RouteManage::get('asdcas')->to('asd@asd');
			RouteManage::addMiddlewareToSystem('new', 'Auth');

			RouteManage::routeDone();

			ViewManage::addStatic('blog', 'KobimInternette\System\Com');
			ViewManage::addStatic('blog2', 'KobimInternette\System\Compossse2');

			ViewManage::addViewStatic('blog', 'asd', 5);

			//dd(PluginManage::gettt());
			
			//dd(preg_match('/(.*)\@(.*)/si', 'KobimInternette\System\Compossse2@compose'));
		}

		public function registerRouteManagement()
		{
			$this->app->singleton('systemroute', function ($app) {
				return new Services\RouteManagement($app);
			});
		}

		public function registerViewManagement()
		{
			$this->app->singleton('systemview', function ($app) {
				return new Services\ViewManagement($app);
			});
		}

		public function registerPluginManagement()
		{
			$this->app->singleton('systemplugin', function ($app) {
				return new Services\PluginManagement($app);
			});
		}
	}