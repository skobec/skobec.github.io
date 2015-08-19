<?php

date_default_timezone_set('Etc/GMT-4');
header('Content-Type: text/html; charset=utf-8');

define('SERVERNAME', $_SERVER['SERVER_NAME']);
define('DOCUMENTROOT', dirname(__FILE__).'/');
// define('IS_DEVELOPER_HOST', false);
define('IS_DEVELOPER_HOST', in_array(SERVERNAME, array('general.api.omsk', 'api.omsk.loc')));
define('IS_DEMO_HOST', in_array(SERVERNAME, array('api.new.demo.itgkh.ru', 'api.demo.etton.ru')));

$paths = array(
    realpath(dirname(__FILE__) . '/../../library/'),
    realpath(dirname(__FILE__) . '/../../framework/'),
    realpath(dirname(__FILE__) . '/../../config'), // папка, где будут храниться настройки
    realpath(dirname(__FILE__) . '/'),
    get_include_path());
set_include_path(implode(PATH_SEPARATOR, $paths));

// подключение Zend автозагрузчика
require_once('Zend/Loader/Autoloader.php');
$loader = Zend_Loader_Autoloader::getInstance();
$loader->setFallbackAutoloader(true);

//!IS_DEVELOPER_HOST ? Config::read(dirname(__FILE__).'/../../config/db.json'): Config::read(dirname(__FILE__).'/../../config/db_local.json');
define('CONFIG_DIR', __DIR__.'/../../library/config');
Config::read(CONFIG_DIR.'/db.json');

class db_general {        
    public static $db; 
}
