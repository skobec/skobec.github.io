<?php

/**
 * Ответ веб-службы
 */
class Prodom_Api_Response {

    // код выполнения (если не «0», значит функция вернула ошибку)
    public $jsonrpc = '2.0';
    // время выполнения метода
    public $exec_time = null;
    public $full_time = null;

    public function __construct($code, $result, $requestID, $exec_time = 0) {
        $code = (int)$code;
        $this->id = $requestID;
        if($code == 0) {
            $this->result = $result;
        }
        else {
            $this->error = array('code' => $code, 'message' => $result);
        }
        $this->exec_time = $exec_time;
        $this->full_time = round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 5);
    }

    public function __toString() {
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }

}