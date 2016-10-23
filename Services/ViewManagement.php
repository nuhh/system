<?php

	namespace KobimInternette\System\Services;


	/**
	 * Tema yönetimi
	 */
	class ViewManagement {

		var $app = null;

		var $statics = [];

		public function __construct($app)
		{
			$this->app = $app;
		}

		public function themeExtend(Callable $function)
		{
			\Blade::extend($function);
		}

		public function themeDirective(string $name, Callable $function)
		{
			\Blade::directive($name, $function);
		}

		public function shareToAllView(string $name, $value)
		{
			$this->app['view']->share($name, $value);
		}

		public function composer(string $view, $callback)
		{
			$this->app['view']->composer($view, $callback);
		}

		public function addStatic(string $key, string $callback)
		{
			if(!array_key_exists($key, $this->statics)) $this->statics[$key] = $callback;
			else throw new Exception('692');
		}

		public function getStatic(string $key)
		{
			return $this->statics[$key];
		}

		public function addViewStatic(string $key)
		{
			if(preg_match('/(.*)\@(.*)/si', $this->getStatic($key))) {
				$explode = explode('@', $this->getStatic($key));
				$class = $explode[0];
				$method = $explode[1];
			} else {
				$class = $this->getStatic($key);
				$method = 'compose';
			}

			$callable = [$this->app->make($class), $method];

			$variables = func_get_args();
			unset($variables[0]);

			call_user_func_array($callable, $variables);
		}

	}
