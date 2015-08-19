<?php

/**
 * Класс является оплеткой для сервиса https://lk.ibatele.com
 * и предоставляет методы для работы с СМС.
 */
class Sms {

    private static $_url_service = "https://lk.ibatele.com/sendsms.php";
    private static $_login;
    private static $_password;
    private static $_sender_name;
    private $_recepient_number;
    private $_text;
    private $_result_message = "";

    public function __construct ($recepient_number = "", $text = "") {
        self::$_login = defined('Constant::SMS_LOGIN') ? Constant::SMS_LOGIN : null;
        self::$_password = defined('Constant::SMS_PASSWORD') ? Constant::SMS_PASSWORD : null;
        self::$_sender_name = defined('Constant::SMS_SENDER') ? Constant::SMS_SENDER : null;
        $this->_recepient_number = htmlentities($recepient_number);
        $this->_text = htmlentities($text);
    }

    /**
     * Результат выполнения отсылки смс
     * @return string   ответ от шлюза
     */
    public function getResult() {
        return $this->_result_message;
    }

    /**
     * Формируем строку запроса
     */
    private function _sendMe() {
        $params = array(
            'user' => self::$_login,
            'pwd' => self::$_password,
            'sadr' => self::$_sender_name,
            'dadr' => $this->_recepient_number,
            'text' => $this->_text);
        $url = self::$_url_service . '?' . http_build_query($params);
        $res = self::_push($url);
        $this->_result_message = $res;
    }

    /**
     * Непосредственная отсылка строки
     * @param type $url string
     * @return string
     */
    private static function _push($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    
    /**
     * Отправка СМС.
     * @param Sms $sms
     * @return \Sms|\self
     * @throws Exception
     */
    public static function Send($sms) {       
        if((self::$_login==null)||(self::$_password==null)||(self::$_sender_name==null)){return;}
        if(gettype($sms) == "array") {
            $status_arr = array();
            foreach($sms as $s) {
                if($s instanceof self) {
                    $s->_sendMe();
                    $status_arr[] = $s;
                }else {
                    throw new Exception('Не правильный формат СМС.', 950);
                }
            }
            return $status_arr;
        }else {
            if($sms instanceof Sms) {
                $sms->_sendMe();
                return $sms;
            }else {
                throw new Exception('Не правильный формат СМС.', 950);
            }
        }
    }

    /**
     * Получение статуса сообщения(ий) по его(их) ID
     * @param int/array $messages_id
     * @return array
     */
    public static function Status($messages_id) {
        if(gettype($messages_id) != "array") {
            $arr[] = $messages_id;
            $messages_id = $arr;
        }

        foreach($messages_id as $message_id) {
            $params = array(
                'user' => self::$_login,
                'pwd' => self::$_password,
                'smsid' => $message_id);
            $url = self::$_url_service . '?' . http_build_query($params);
            $res[$message_id] = self::_push($url);
        }
        return $res;
    }

    /**
     * Проверка баланса
     * @return string
     */
    public static function Balance() {
        $params = array(
            'user' => self::$_login,
            'pwd' => self::$_password,
            'balance' => 1);
        $url = self::$_url_service . '?' . http_build_query($params);
        return self::_push($url);
    }

    /**
     * Будут ли рассылаться СМС
     * в текущем регионе
     * 
     * @return boolean
     */
    public static function isAllowSMS(){
        $login = defined('Constant::SMS_LOGIN') ? Constant::SMS_LOGIN : null;
        $password = defined('Constant::SMS_PASSWORD') ? Constant::SMS_PASSWORD : null;
        $sender_name = defined('Constant::SMS_SENDER') ? Constant::SMS_SENDER : null;
        if(($login==null)||($password==null)||($sender_name==null)){return false;}
        return true;
    }
}
