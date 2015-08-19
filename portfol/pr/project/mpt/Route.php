<?php

    if (php_sapi_name() != 'cli') {

		// настройка фронт контроллера
		$frontController = Zend_Controller_Front::getInstance();
		$frontController->throwExceptions(false);
		$frontController->addModuleDirectory(dirname(__FILE__) . '/application');
        $router = $frontController->getRouter();

        // Вызов метода из API
        $router->addRoute('api', new Zend_Controller_Router_Route('/api/:service/:method', array('module' => 'default', 'controller' => 'api', 'action' => 'run')));

        if(isset($_POST['_mikron'])) {
	        // Mikron controllers
			$router->addRoute(
				'mikron_edit',
				new Zend_Controller_Router_Route_Regex(
					'(.+)', array(
						'module' => 'default',
						'controller' => 'mikron',
						'action' => 'index'
					),
					array(1 => 'table'),
					'%s'
				)
			);
		}

	}