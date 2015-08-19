<?php

// ключ для вызова методов из шлюза
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Europe/Moscow');
define('SERVERNAME', $_SERVER['SERVER_NAME']);
define('SITE_API_KEY', 'cc9eb73f-d956-41e4-b2c1-7f4146057fa4');
define('APPLICATION_CODE', 'mpt');

if(version_compare(PHP_VERSION, '5.6.0') < 0) {
	iconv_set_encoding('internal_encoding', 'UTF-8');
	iconv_set_encoding('output_encoding', 'UTF-8');
}
ini_set('default_charset', 'utf-8');

define('IS_DEVELOPER_HOST', (isset($_SERVER['IS_DEV']) && (bool)$_SERVER['IS_DEV']) || in_array(SERVERNAME, array('localhost', 'mpt.loc')));
// расчет www-корневой папки проекта. В конце строки гарантировано отсутствует косая черта
define('DOCUMENTROOT', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));

$rootDir = realpath(__DIR__ . '/../../../');

define('CONFIG_DIR', $rootDir . '/library/config');
define('STORAGE_DIR', $rootDir . '/storage');
define('TMP_DIR', $rootDir . '/tmp');

// набор папок для автозагрузчика
set_include_path(implode(PATH_SEPARATOR, array(
    $rootDir . '/framework',
    $rootDir . '/api',
    $rootDir . '/library', // папка, где хранятся дополнительные фреймфорки и общие классы
	// realpath(dirname(__FILE__) . '/../Mikron'),
	realpath(dirname(__FILE__) . '/..'),
    CONFIG_DIR, // папка, где хранятся регионозависимые настройки
    '.',
)));

// подключение автозагрузчика
require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->setFallbackAutoloader(true);

define('ROOT_DOMAIN', IS_DEVELOPER_HOST ? 'mpt.loc' : Constant::VAR_ROOT_DOMAIN);

// настройка фронт контроллера
$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions(false);
$frontController->addModuleDirectory(dirname(__FILE__) . '/../application');
$router = $frontController->getRouter();

require_once dirname(__FILE__).'/../../../library/Mikron/Trigger.php';

Mikron_Crud::setUploadDirectory(dirname(__FILE__));

require_once 'Routines.php';
require_once 'Db.php';
// Файл с меню
require_once 'Menu.php';
require_once 'Route.php';
require_once 'App.php';

Zend_Controller_Action_HelperBroker::addHelper(new Init_Project);

/**
* Настройка Zend_Layout  
*/
Zend_Layout::startMvc(array(
	'layoutPath' => dirname(__FILE__).'/../layout', // папка, где будут храниться шаблоны системы
    'layout' => 'index' // шаблон по-умолчанию, index.phtml
    )
);

$frontController->dispatch();
