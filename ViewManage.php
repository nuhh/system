<?php

	namespace KobimInternette\System;

	use Illuminate\Support\Facades\Facade;

	class ViewManage extends Facade
	{
		protected static function getFacadeAccessor()
		{
			return 'systemview';
		}
	}
