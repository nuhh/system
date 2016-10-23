<?php

	namespace KobimInternette\System;

	use Illuminate\Support\Facades\Facade;

	class RouteManage extends Facade
	{
		protected static function getFacadeAccessor()
		{
			return 'systemroute';
		}
	}
