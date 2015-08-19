<?php

/**
* Клиент для API-шлюзов
*/
class Prodom_Api_Client {

    private $host = null; // хост шлюза
    private $port = null; // порт шлюза
    private $serviceName = null; // имя шлюза
    private $__lastRequest = null; // последний запрос
    private $__lastResponse = null; // последний ответ
    private $apiKey = null;
    private $alwaysThrowExceptions = false;

    /**
    * Автоматическое кеширование вызова функции
    * 
    * @param mixed $time
    */
    function _cache($time) {
        return new Prodom_Api_Cache($this, $time);
    }

    /**
    * Устанавливает или отменяет принудительную генерацию исключений при вызове методов
    * 
    * @param bool $value
    * 
    * @return void
    */
    public function setAlwaysThrowExceptions($value) {
        $this->alwaysThrowExceptions = (int)$value != 0;
    }
    
    /**
    * Возвращает состояние принудительной генерации исключения при вызове методов
    * 
    * @return bool
    */
    public function getAlwaysThrowExceptions() {
        return $this->alwaysThrowExceptions;
    }

    /**
    * Записи истории вызова для API-шлюзов
    * @author sciner
    * @since 16-07-2012 12:39
    * 
    * @var Prodom_Api_History
    */
    public static $history = array();

    /**
     * @author Lenar Zakirov
     *
     * @param string $host
     * @param string $serviceName
     * @param bool $alwaysThrowExceptions
     */
    public function __construct($host, $serviceName, $alwaysThrowExceptions = true) {
        $host = explode(':', $host);
        $this->host = $host[0];
        $this->port = (count($host) == 2) ? (int)$host[1] : 80;
        $this->alwaysThrowExceptions = (int)$alwaysThrowExceptions != 0;
        $this->serviceName = $serviceName;
        if(defined('SITE_API_KEY')) {
            $this->setApiKey(SITE_API_KEY);
        }
    }

    public function getApiKey() {
        return $this->apiKey;
    }

    /**
    * Установка секретного ключа, для использования API
    * 
    * @param string $key
    */
    public function setApiKey($key) {
        $this->apiKey = $key;
    }

    public function __getLastRequest() {
        return $this->__lastRequest;
    }

    public function __getLastResponse() {
        return $this->__lastResponse;
    }

    public static function __getHistory() {
        return self::$history;
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
            // генерация запроса
            $req = array('jsonrpc' => '2.0', 'method' => $m, 'params' => $a, 'id' => md5(uniqid(microtime(true), true)), 'key' => $this->getApiKey());
            $this->__lastRequest = $req;
            $data = json_encode($req, JSON_UNESCAPED_UNICODE);
            unset($req);
            $t = microtime(true);
            // отправка POST-запроса
			$response_string = Prodom_ToolBox::__post_request($this->host, $this->port, "/{$this->serviceName}", $data);
			$resp_size = strlen($response_string);
            $t = round(microtime(true) - $t, 5);
            // запоминаем последний ответ с сервера
            $this->__lastResponse = $response_string;
            unset($response_string);
            $response = json_decode($this->__lastResponse);
            if(!is_object($response)) {
                // если пришел мусор:
                $error_code = 112233;
                $error_text = "Ответ метода {$this->serviceName}.{$m} не соответствует формату JsonRpc 2.0:\r\n{$this->__lastResponse}";
                // сохраняем ответ в историю запросов
                self::$history[] = new Prodom_Api_History(array('elapsed' => $t, 'method' => "{$this->serviceName}.{$m}", 'code' => $error_code, 'result' => $error_text, 'response_size' => $resp_size));
                // генерим исключение с текстом в теле которого будет этот самый "пришедший мусор"
                throw new Exception($error_text, $error_code);
            }
            if($response !== false) {$this->__lastResponse = $response;}
            if(isset($response->error)) {
                // если пришла ошибка
                $error_code = (int)$response->error->code;
                // сохраняем ответ в историю запросов
                self::$history[] = new Prodom_Api_History(array('elapsed' => $t, 'method' => "{$this->serviceName}.{$m}", 'code' => $error_code, 'result' => $response->error->message, 'response_size' => $resp_size));
                // генерим исключение
                throw new Exception($response->error->message, $error_code);
            }
            // сохраняем ответ в историю запросов
            self::$history[] = new Prodom_Api_History(array('elapsed' => $t, 'method' => "{$this->serviceName}.{$m}", 'code' => 0, 'result' => null, 'response_size' => $resp_size));
            Prodom_Api_History::$history[] = new Prodom_Api_History(array('elapsed' => $t, 'method' => "{$this->serviceName}.{$m}", 'code' => 0, 'result' => null, 'response_size' => $resp_size));
            return $response->result;
        }
        catch(Prodom_Json_Exception $sf) {
            if($this->alwaysThrowExceptions) {
                throw $sf;
            } else {
                die(new Prodom_Api_Response($sf->faultcode, $sf->faultstring));
            }
        }
        catch(Exception $ex) {
            if($this->alwaysThrowExceptions) {
                throw $ex;
            } else {
                return new Prodom_Json_Exception($ex->getMessage(), $ex->getCode());
            }
        }
    }

}