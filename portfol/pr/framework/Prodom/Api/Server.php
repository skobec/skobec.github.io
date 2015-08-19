<?php

class Prodom_Api_Server {

    const ERR_PARSE_ERROR = -32700;
    const ERR_INVALID_REQUEST = -32600;
    const ERR_METHOD_NOT_FOUND = -32601;
    const ERR_INVALID_PARAMS = -32602;
    const ERR_INTERNAL_ERROR = -32603;
    const ERR_KEY_MANAGER_INTERNAL_ERROR = -32000;
    const ERR_KEY_MANAGER_NOT_LOADED = -32006;
    const ERR_INVALID_KEY = -32005;
    const ERR_KEY_NOT_PERMITTED = -32004;
    const ERR_KEY_RESTICTS_NOT_FOUND = -32003;
    const ERR_SERVICE_NOT_PERMITTED = -32002;
    const ERR_METHOD_NOT_PERMITTED = -32001;
    const ERR_PARAM_REQUIRE = -32020;
    const ERR_PARAM_TYPE = -32021;

    private $gatewayDirectory = null;
    private $_serviceName = null;
    private $_methodName = null;
    private $_params = null;
    private $_apiKey = null;
    private $_apiKeyManager = null;
    private $_requestID = null;

    public function setServicesDirectory($filePath) {
        $this->gatewayDirectory = $filePath;
    }

    public function setKeysManager($object) {
        $this->_apiKeyManager = $object;
    }

    public function __construct() {
        try {
            if($_SERVER['REQUEST_METHOD'] != 'POST') {
                return true;
            }
            // параметры вызова метода
            $request = file_get_contents('php://input');
            if(substr($request, 0, 8) == 'request=') {
                $request = urldecode(substr($request, 8));
            }
            if(!$request && isset($_POST['request'])) {
                $request = $_POST['request'];
            }
            $request = json_decode($request);
            if(!is_object($request)) {
                throw new Exception('Некорретный вызов метода', self::ERR_INVALID_REQUEST);
            }
            $this->_method = $request->method;
            // Вычленяем имя сервиса
            $this->_serviceName = ltrim($_SERVER['REQUEST_URI'], '/');
            $this->_params = isset($request->arguments) ? $request->arguments : $request->params;
            $this->_requestID = isset($request->id) ? $request->id : null;
            // первый аргумент функции всегда ApiKey
            $this->_apiKey = isset($request->key) ? $request->key : array_shift($this->_params);
            unset($request);
        }
        catch(Exception $ex) {
            die(new Prodom_Api_Response(100, __CLASS__.': Некорретный вызов метода', $this->_requestID)); 
        }
    }

    /**
    * Обработка вызова метода из сервиса
    */
    public function request() {
        try {
            header('Content-Type: application/json');
            // определение запрашиваемого сервиса и подгрузка соответствующего класса
            // делаем api-ключ глобально доступным
            define('API_KEY', $this->_apiKey);
            define('SITE_API_KEY', $this->_apiKey);
            // определение имени класса
            $serviceClass = 'Service_'.$this->_serviceName;
            if($this->_apiKeyManager) {
                define('API_KEY_NAME', $this->_apiKeyManager->getKeyName($this->_apiKey));
                try {
                    // проверяем наличие разрешения для данного ключа вызывать указанный метод из указанного шлюза
                    $allow_run = $this->_apiKeyManager->check($this->_apiKey, $this->_serviceName, $this->_method);    
                } catch(Exception $ex) {
                    die(new Prodom_Api_Response($ex->getCode(), "{$ex->getMessage()}, {$this->_serviceName}.{$this->_method}", $this->_requestID));
                }
            } else {
                define('API_KEY_NAME', null);
            }

            // определение пути к файлу с классом сервиса
            $serviceFile = $this->gatewayDirectory . '/' . str_replace('_', '/', $serviceClass) . '.php';
            // проверка существования файла с классом сервиса
            if (!file_exists($serviceFile)) {
                die(new Prodom_Api_Response('100', __CLASS__.': Файл сервиса не найден '.$serviceFile, $this->_requestID));
            }
            // загрузка класса сервиса
            include($serviceFile);
            $this->_params = Prodom_ToolBox::object_to_array($this->_params);
            // Если количество аргументов у вызываемой функции больше, тогда дополняем передаваемые ей аргументы null-ами
            $this->_params = Prodom_Reflection::validateMethodArguments($this->_params, $serviceClass, $this->_method);
            $service = new $serviceClass();
            // проверка на наличие метода в классе
            if (!is_callable(array($service, $this->_method), false)) {
                throw new Exception('Method doesn\'t exists', self::ERR_METHOD_NOT_FOUND);
            }
            if ($service instanceof Prodom_Api_Service) {
                $service->__prepare($serviceClass, $this->_method, $this->_params);    
            }
            // Prodom_Api_Service::checkAcl($serviceClass, $this->_method, $this->_params);
            $t = microtime(true);
            $resp = call_user_func_array(array($service, $this->_method), array_values($this->_params));
            $t2 = round(microtime(true) - $t, 5);
            // если пришла ошибка, тогда генерируем исключение
            if ($resp instanceof Prodom_Json_Exception) {
                throw $resp;
            }
            // если ответом является объект
            elseif(is_object($resp)) {
                // проверяем, есть ли у объекта реализация собственной сериализации
                $respMethods = get_class_methods(get_class($resp));
                if (in_array('__toString', $respMethods)) {
                    // Сериализуем, вызвая магический метод __toString()
                    $resp = trim($resp);
                }
            }
            die(new Prodom_Api_Response(0, $resp, $this->_requestID, $t2));
        } catch (Prodom_Json_Exception $sf) {
            if (!isset($t2)) {
                $t2 = 0;
                if (isset($t)) {
                    $t2 = round(microtime(true) - $t, 5);
                }
            }
            die(new Prodom_Api_Response($sf->getCode(), $sf->getMessage(), $this->_requestID));
        } catch (Exception $ex) {
            if (!isset($t2)) {
                $t2 = 0;
                if (isset($t)) {
                    $t2 = round(microtime(true) - $t, 5);
                }
            }
            $code = (string)$ex->getCode();
            if ($code == '0') {
                $code = '100';
            }
            die(new Prodom_Api_Response($code, $ex->getMessage(), $this->_requestID, $t2));
        }
    }

    /**
    * Справочник по шлюзам
    */
    public function printHelp() {
        $help_cache = $this->gatewayDirectory.'/help_cache.tmp';
        if(file_exists($help_cache)) {
            if(time() - filectime($help_cache) < 3600) {
                include $help_cache;
            }
        }
        if(!isset($help)) {
            $help = array();
            $files = scandir($this->gatewayDirectory . '/Service');
            foreach ($files as $file) {
                if (substr($file, -4) == '.php') {
                    require_once($this->gatewayDirectory . '/Service/' . $file);
                    $serviceClass = explode('.php', $file);
                    $serviceClass = 'Service_'.$serviceClass[0];
                    $help[$serviceClass] = array('methods' => Prodom_Reflection::getMethods($serviceClass));
                }
            }
            file_put_contents($help_cache, '<?php $help = '.var_export($help, true).';');
        }
        include dirname(__FILE__) . '/gateway_help.htm';
    }

}
