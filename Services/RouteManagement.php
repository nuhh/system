<?php

	namespace KobimInternette\System\Services;

	use Exception;

	/**
	 * eklenti içerisinde yönlendirme dosyalarını kontrol etmek gerekli
	 */
	class RouteManagement extends RouteDone
	{

		/**
		 * Geçici olarak yönlendirmelerin toplandığı dosya
		 * @var dosyanın adresi
		 */
		var $routeFile = __DIR__ . '/../Routes/TemporaryRoutes.php';

		/**
		 * Eklenecek olan adres nedir?
		 * @var string
		 */
		var $uri = null;

		/**
		 * mevcut yönlendirmeleri taşıyan değişken
		 * @var null
		 */
		var $routes = null;

		/**
		 * Adresin hangi türde http ye sahip olduğunu taşır
		 * @var null
		 */
		var $type = null;

		/**
		 * Urlnin hangi controller ve method tarafından çağıralacağını taşır
		 * @var string
		 */
		var $to = null;

		/**
		 * Url de kullanılacak olan middlewareleri taşır
		 * @var null
		 */
		var $middleware = null;

		/**
		 * Url için özel bir isim kullanmak istenirse eklenecektir
		 * @var null
		 */
		var $name = null;

		/**
		 * Url hangi kontroller tarafından kullanılacak
		 * @var null
		 */
		var $controller = null;

		/**
		 * Url için tanımlanmış özel namespace
		 * @var null
		 */
		var $namespace = null;

		/**
		 * Url hangi kontrollerdeki methodu kullanacak
		 * @var null
		 */
		var $method = null;

		/**
		 * Url yönetim adresi mi öyleyse ön ek eklenecek
		 * @var boolean
		 */
		var $isManagement = false;

		/**
		 * Yönetim hangi ön eki kullanacak
		 * @var string
		 */
		var $managementPrefix = 'admin';

		/**
		 * Bu değişken sadece loadRoutesFromFile methodu üzerinden çağırılırsa çalışacaktır
		 * @var null
		 */
		var $setAllNamespace = null;

		var $app=null;

		function __construct($app)
		{
			$this->app = $app;
		}

		/**
		 * loadRoutesFromFile methodundan çağrılan yönlendirmeler için 
		 * ön tanımlı bir namespace i tek seferde eklenmek istenirse kullanılacaktır.
		 */
		public function setAllNamespace(string $namespace)
		{
			$this->setAllNamespace = $namespace;

			return $this;
		}

		public function loadAdminRoutesFromFile()
		{
			$this->getRoutes();
			foreach(require $this->pluginDir . '/Routes/Admin.php' as $uri => $values) {
				if(array_key_exists($uri, $this->routes)) {
					throw new Exception('Bu URI daha önce tanımlanmış');
				}

				$this->{$values['type']}($uri)->name($values['name'])->to($values['to'])->management()->done();
			}
		}

		public function loadFrontendRoutesFromFile()
		{
			$this->getRoutes();
			foreach(require $this->pluginDir . '/Routes/Admin.php' as $uri => $values) {
				if(array_key_exists($uri, $this->routes)) {
					throw new Exception('Bu URI daha önce tanımlanmış');
				}

				$this->{$values['type']}($uri)->name($values['name'])->to($values['to'])->done();
			}
		}

		/**
		 * Dosyalardan yükleme yapılacaksa kullanılacaktır.
		 */
		public function loadRoutesFromFile()
		{
			if(file_exists($this->pluginDir . '/Routes/Admin.php')) {
				$this->loadAdminRoutesFromFile();
			}
			if(file_exists($this->pluginDir . '/Routes/Frontend.php')) {
				$this->loadFrontendRoutesFromFile();
			}
		}

		/**
		 * Sistemin içerisine middleware eklemeye yarayan method
		 */
		public function addMiddlewareToSystem(string $middlewareName, string $middlewareClass)
		{
			if(!class_exists($middlewareClass)) throw new Exception('#4');

			$this->app['router']->middleware($middlewareName, $middlewareClass);
		}

		/**
		 * Adresin yönetim adresi olduğunu doğrulayan method
		 */
		public function management()
		{
			$this->isManagement = true;

			return $this;
		}

		/**
		 * geçici yönlendirme dosyasını taşıyan method
		 */
		public function getRoutes()
		{
			if($this->routes === null) $this->routes = require $this->routeFile;
		}

		public function controller(string $controller)
		{
			$this->controller = $controller;

			return $this;
		}

		public function namespace(string $namespace)
		{
			$this->namespace = $namespace;

			return $this;
		}

		public function method(string $method)
		{
			$this->method = $method;

			return $this;
		}

		public function to(string $controllerWithMethod)
		{
			$this->to = $controllerWithMethod;

			return $this;
		}

		public function get(string $uri)
		{
			$this->type = 'GET';
			$this->uri = $uri;

			return $this;
		}

		public function type(string $type)
		{
			$this->type = $type;

			return $this;
		}

		public function post(string $uri)
		{
			$this->method = 'POST';
			$this->uri = $uri;

			return $this;
		}

		public function name(string $name)
		{
			$this->name = $name;

			return $this;
		}

		public function middleware($middleware)
		{
			$this->middleware = $middleware;

			return $this;
		}

		public function addMiddlewareTo(string $uri, $middleware)
		{
			$this->getRoutes();
			if(!array_key_exists($uri, $this->routes)) {
				throw new Exception('Tanımlı olmayan bir URI ye middleware tanımlamaya çalışıyorsunuz');
			} else {
				if(isset($this->routes[$uri]['middleware'])) {
					$this->routes[$uri]['middleware'][] = $middleware;
				} else {
					$this->routes[$uri]['middleware'] = [$middleware];
				}
			}
		}

		public function addMiddlewareToAll($middleware)
		{
			$this->getRoutes();
			# böyle bir middleware nin olduğunu kontrol etmek lazım
			if(!array_key_exists('middlewareToAll', $this->routes)) {
				$this->routes['middlewareToAll'] = [$middleware];
			} else {
				$this->routes['middlewareToAll'][] = $middleware;
			}
		}

		/**
		 * rotasyon satırı burada oluşturulacak
		 */
		public function done()
		{
			# request type null olamaz
			# to tanımlanmışsa controller ve method devre dışı olur
			# name şart değil
			# middleware şart değil
			$this->getRoutes();
			$this->routes[$this->uri] = [
				'to' => $this->to,
				'middleware' => $this->middleware,
				'name' => $this->name,
				'controller' => $this->controller,
				'method' => $this->method,
				'type' => $this->type
			];

			file_put_contents($this->routeFile, '<?php return ' . (string) var_export($this->routes, true) . ';');

			$this->reflesh();

			return true;
		}

		public function reflesh()
		{
			$this->to = null;
			$this->middleware = null;
			$this->name = null;
			$this->controller = null;
			$this->method = null;
			$this->type = null;
			$this->isManagement = false;
		}

		/**
		 * bütün yönlendirmeleri laravele tanıtacağız
		 */
		public function routeDone()
		{
			$this->getRoutes();
			$routes = $this->routes;

			foreach($routes as $uri => $values) {
				$this->app['router']->{$values['type']}($uri, $values['to'])->name($values['name']);
			}
		}

		
	}