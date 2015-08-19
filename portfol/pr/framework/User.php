<?php

/**
* Пользователь
* @author sciner
*/
class User {

    private $id = null;
    private $login = null;
    private $session_id = null;
    private $info = null;
    // администраторские флаги прав
    private $adminRoles = 0;
    // пользовательские флаги прав
    private $userRoles = 0;

    public function __construct($session_id = null, $user = null) {
    	$this->info = null;
    	return;
		$env_name = Settings::get('env_name');
        if($user) {
            $user = (object)$user;
            $this->info = $user;
        } elseif(isset($_SESSION[$env_name.'.user_profile'])) {
            $user_profile = (object)$_SESSION[$env_name.'.user_profile'];
            $this->info = json_decode($user_profile->info);
            $session_id = $user_profile->session_id;
        } elseif(isset($_COOKIE[$env_name.'_usid'])) {
            if($user_profile = self::reloadProfile()) {
	            $this->info = json_decode($user_profile->info);
            	$session_id = $user_profile->session_id;
            }
        }
        if($this->info) {
	        $this->id = $this->info->id;
            $this->session_id = $session_id;
	        $this->login = $this->info->login;
	        $this->setAdminRoles(isset($this->info->flags_admin) ? $this->info->flags_admin : 0);
	        $this->setUserRoles(isset($this->info->flags_user) ? $this->info->flags_user : 0);
		}
    }

    /**
    * Инициализация флагов
    * @author sciner
    * 
    * @param int $flags
    */
    public function setAdminRoles($flags) {
        $this->adminRoles = $flags;
    }

    /**
    * Инициализация флагов
    * @author sciner
    * 
    * @param int $flags
    */
    public function setUserRoles($flags) {
        $this->userRoles = $flags;
    }

    /**
    * Проверка наличия администраторской роли
    * @author sciner
    * 
    * @param int $flag
    * 
    * @return bool
    */
    public function hasAdminRole($flag) {
        return (($this->adminRoles & $flag) == $flag);
    }

    /**
    * Перечитывание профиля пользователя из шлюза
    * @author sciner
    * 
    * @param string $session_id
    * @param string $login
    * 
    * @return bool
    */
    public static function reloadProfile($session_id = null, $login = null) {
		$env_name = Settings::get('env_name');
        $session_id = $session_id ?: (isset($_COOKIE[$env_name.'_usid']) ? $_COOKIE[$env_name.'_usid'] : null);
        $login = $login ?: (isset($_COOKIE[$env_name.'_login']) ? $_COOKIE[$env_name.'_login'] : null);
        if(!$session_id || !$login) {
            return false;
        }
        try {
            $user = (object)Service::User()->updateProfile($session_id, $login);
            $user_profile = array(
                'session_id' => $session_id,
                'info' => json_encode($user),
            );
            $_SESSION[$env_name.'.user_profile'] = $user_profile;
            return (object)$user_profile;
        } catch(Exception $ex) {
            $_COOKIE[$env_name.'.usid'] = null;
            return false;
        }
	}

    public function getInfo() {
        return $this->info;
    }

    /**
    * Проверка авторизованности пользователя
    * @return bool
    */
    function isLogged() {
        return !is_null($this->id);
    }

    function logout() {
        session_destroy();
        if($this->session_id) {
            Service::User()->logout($this->session_id);        
        }
    }

    /**
    * Идентификатор пользователя в БД
    * @return int
    */
    function getId() {
        return $this->id;
    }

    /**
    * Логин пользователя
    * @return string
    */
    function getLogin() {
        return $this->login;
    }

    /**
    * Идентификатор сессии
    * @return string
    */
    function getSessionId() {
        return $this->session_id;
    }

    /**
    * Проверка наличия пользовательской роли
    * @author sciner
    * 
    * @param int $flag
    * 
    * @return bool
    */
    public function hasUserRole($flag) {
        return (($this->userRoles & $flag) == $flag);
    }

}

