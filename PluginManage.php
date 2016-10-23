<?php

	namespace KobimInternette\System;

	use Illuminate\Support\Facades\Facade;

	class PluginManage extends Facade
	{
		protected static function getFacadeAccessor()
		{
			return 'systemplugin';
		}
	}
