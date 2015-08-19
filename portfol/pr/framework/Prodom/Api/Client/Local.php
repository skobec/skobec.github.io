<?php

/**
* Клиент для локальных API-шлюзов
*/
class Prodom_Api_Client_Local {

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

    private $serviceDirectory = null; // папка со шлюзами
    private $__lastRequest = null; // последний запрос
    private $__lastResponse = null; // последний ответ
    private $_serviceName = null; // имя шлюза
    private $_methodName = null;
    private $_params = null;
    private $_service = null;

    /**
     * @author Lenar Zakirov
     *
     * @param string $serviceDirectory
     * @param string $serviceName
     */
    public function __construct($serviceDirectory, $serviceName) {
        $this->serviceDirectory = $serviceDirectory;
        $this->_serviceName = $serviceName;
        $this->_serviceClass = 'Service_'.$this->_serviceName;
        // определение пути к файлу с классом сервиса
        $serviceFile = rtrim($this->serviceDirectory, '/') . '/' . str_replace('_', '/', $this->_serviceClass) . '.php';
        // проверка существования файла с классом сервиса
        if (!file_exists($serviceFile)) {
            throw new Exception(__CLASS__.': Файл сервиса не найден '.$serviceFile, 100);
        }
        // загрузка класса сервиса
        include_once $serviceFile;
        $this->_service = new $this->_serviceClass();
    }

    /**
    * Автоматическое кеширование вызова функции
    * @param int $seconds
    */
    function _cache($seconds) {
        return new Prodom_Api_Cache($this, $seconds);
    }

    public function __getLastRequest() {
        return $this->__lastRequest;
    }

    public function __getLastResponse() {
        return $this->__lastResponse;
    }

    /**
    * "Магический метод", перехват вызова несуществующих методов класса
    * 
    * @param string $m Имя вызываемого метода
    * @param array $a Входные аргументы метода
    * 
    * @return Prodom_Json_Exception
    */
    public function __call($m, $a) {
        try {
            $t = microtime(true);
            // подготовка вызова функции
            $this->__lastRequest = array('method' => $m, 'params' => $a);
            $this->_method = $m;
            $this->_params = $a;
            // $this->_params = Prodom_ToolBox::object_to_array($this->_params);
            // Если количество аргументов у вызываемой функции больше, тогда дополняем передаваемые ей аргументы null-ами
            try {
                $this->_params = Prodom_Reflection::validateMethodArguments($this->_params, $this->_serviceClass, $this->_method);
            } catch(Exception $ex) {
                throw new Exception("Ошибка в аргументах API функции {$this->_serviceClass}::{$this->_method}(): ".$ex->getMessage(), 950);
            }
            // проверка на наличие метода в классе
            if (!is_callable(array($this->_service, $this->_method), false)) {
                throw new Exception('Method doesn\'t exists', self::ERR_METHOD_NOT_FOUND);
            }
            if ($this->_service instanceof Prodom_Api_Service) {
                $this->_service->__prepare($this->_serviceClass, $this->_method, $this->_params);    
            }
            $t = microtime(true);
            $this->__lastResponse = call_user_func_array(array($this->_service, $this->_method), array_values($this->_params));
            $resp_size = 0;
            $t = round(microtime(true) - $t, 5);
            // сохраняем ответ в историю запросов
            Prodom_Api_Client::$history[] = new Prodom_Api_History(array('elapsed' => $t, 'method' => "{$this->_serviceName}.{$m}", 'code' => 0, 'result' => null));
            // Костыль от Notfoolen
            // return $this->__lastResponse;
            return json_decode(json_encode($this->__lastResponse));
        }
        catch(Exception $ex) {
            throw $ex;
        }
    }
    
    function get_caller_info() {
        $c = '';
        $file = '';
        $func = '';
        $class = '';
        $trace = debug_backtrace();
        if (isset($trace[2])) {
            $file = $trace[1]['file'];
            $func = $trace[2]['function'];
            if ((substr($func, 0, 7) == 'include') || (substr($func, 0, 7) == 'require')) {
                $func = '';
            }
        } else if (isset($trace[1])) {
            $file = $trace[1]['file'];
            $func = '';
        }
        if (isset($trace[3]['class'])) {
            $class = $trace[3]['class'];
            $func = $trace[3]['function'];
            $file = $trace[2]['file'];
        } else if (isset($trace[2]['class'])) {
            $class = $trace[2]['class'];
            $func = $trace[2]['function'];
            $file = $trace[1]['file'];
        }
        if ($file != '') $file = basename($file);
        $c = $file . ": ";
        $c .= ($class != '') ? ":" . $class . "->" : "";
        $c .= ($func != '') ? $func . "(): " : "";
        return($c);
    }

}