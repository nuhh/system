<?php

	namespace KobimInternette\System\Services;

	use Exception;
	use Illuminate\Support\Facedes\File;

	/**
	 * eklentiler arası etkileşimi kontrol etmek için kullanılacak olan trait
	 */
	class PluginManagement {

		var $app = null;

		var $pluginName  = '';

		var $addedPlugins = [];

		var $init = false;

		function __construct($app)
		{
			$this->app = $app;
			//$this->pluginName = $name;
		}

		function gettt()
		{
			return $this->addedPlugins;
		}

		/**
		 * eklnetinin service provider üzerinden kurulumunu yapacak olan mehto
		 */
		public function setUp($name)
		{
			if($this->init==false) {
				$this->init = true;
			}

	        $config = $this->app['config']->get($name, []);

	        $this->app['config']->set($name, array_merge(require __DIR__ . '/../../'.$name.'/Config.php', $config));

			if(!array_key_exists($name, $this->addedPlugins)) {
				if($this->app['config']->get($name.'.pluginName', null)!==null) $this->pluginDir = __DIR__;
				
				if($this->app['config']->get($name.'.pluginName', null)==null) throw new Exception('Her eklentinin bir adı olmak zorundadır. HataKodu#1');

				if($this->app['config']->get($name.'.requireFiles', []) != []) $this->requireFiles($this->requireFiles);

				if(isset($this->pluginRequires)) $this->pluginRequires($this->pluginRequires);

				$this->addedPlugins[$name] = true;

			}

			
		}

		/**
		 * eklenti için kullanılacak olan dosyaları sisteme dahil edecek bir nevi helpers
		 */
		public function requireFiles(array $files)
		{
			foreach($files as $file) $this->requireFile($file);
		}

		public function requireFile(string $file)
		{
			if(!file_exists($file)) throw new Exception('Eklemek istediğiniz dosya bulunamadı.');
			 else require $file;
		}

		/**
		 * eklenti içerisinde başka bir eklenti gerekiyorsa onun kontrolunu yapacak olan method
		 */
		public function pluginRequire(string $plugin)
		{
			if(!class_exists($plugin))
				throw new Exception('Bu eklenti için gerekli olan bir eklentiyi barındırmıyor. ' . $this->pluginName . ' HataKodu#3');
		}

		public function pluginRequires(array $plugins)
		{
			foreach($plugins as $plugin) $this->pluginRequire($plugin);
		}

		public function getPluginInfos()
		{
			$n = file_get_contents($this->pluginDir . '/info');

			$mm = preg_match_all('/(.*)=(.*)(\n|)/', $n, $m);

			$result = [];

			for($i = 0; count($m[0])-1>=$i; $i++) {
				$result[$m[1][$i]] = $m[2][$i];
			}

			return $result;
		}

		public function addNewInfo(string $key, string $value)
		{
			$infos = $this->getPluginInfos();
			$infos[$key] = $value;

			$string = '';

			foreach($infos as $k => $v) {
				$string .= $k . '=' . $v . "\n";
			}

			file_put_contents($file, '');
			file_put_contents($file, $string);

			return $infos;
		}

		public function removeInfo(string $key)
		{
			$infos = $this->getPluginInfos($file);
			unset($infos[$key]);
			$string = '';

			foreach($infos as $k => $v) {
				$string .= $k . '=' . $v . "\n";
			}

			file_put_contents($file, '');
			file_put_contents($file, $string);

			return $infos;
		}
		
	}
