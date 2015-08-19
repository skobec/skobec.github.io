<?php

/**
* Шлюз управления пользователями
*/
class Service_User extends Prodom_Api_Service {

    /**
     * Авторизует пользователя и возвращает идентификатор сессии
     * @author Lenar Vafin
     * @since 01.07.2013
     *
     * @param string $login Логин
     * @param string $password Пароль
     *
     * @return Type_User_Login_Result
     */
    public function login($login, $password) {
        return Model_User::login($login, $password);
    }

    /**
    * Очистка сессии пользователя
    * @author sciner
    * 
    * @param string $sid !!! Не менять название аргумента !!!
    * 
    * @return bool
    */
    public function logout($sid) {
        return Model_User::logout($sid);
    }

    /**
    * Обновляет профиль пользователя
    * 
    * @param string $session_id Идентификатор сессии
    * @param string $login Логин
    * 
    * @return Type_User_Extended
    */
    public function updateProfile($session_id, $login) {
        $cache = Prodom_Connector::getConnection('db_redis');
        $user = Model_User::loadProfileByLogin($login);
        if($user) {
            $key = new Rediska_Key("user_{$user->id}");
            $ttl = $key->getlifetime();
            if($ttl < 1) {
                $ttl = 1;
            }
            $key->setAndExpire(json_encode($user), $ttl);
            return $user;
        }
    }
    
    /**
    * put your comment there...
    * 
    * @param string $session_id
    * @param string $id
    * @param mixed $value
    * 
    * @return bool
    */
    function setVariable($session_id, $id, $value) {
    	$id = "user_{$this->user->id}_var_{$id}";
		$key = new Rediska_Key($id);
		$ttl = 3600 * 24 * 365 * 2;
		$key->setAndExpire(json_encode($value, JSON_UNESCAPED_UNICODE), $ttl);
		return true;
    }
    
    /**
    * put your comment there...
    * 
    * @param string $session_id
    * @param string $id
    * 
    * @return bool
    */
    function getVariable($session_id, $id) {
    	$id = "user_{$this->user->id}_var_{$id}";
		$key = new Rediska_Key($id);
		return json_decode($key->getValue());
    }

}