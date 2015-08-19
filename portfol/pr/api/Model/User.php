<?php

class Model_User {

	// Соль для паролей
	private static $salt = '740ca30c-f4bd-11e4-b9b2-1697f925ec7b';

    /**
    * Возвращает объект пользователя по идентификатору сессии
    * @author sciner
    * @since 02.07.2013
    * 
    * @param string $session_id
    * 
    * @return Type_User
    */
    public static function getUserBySessionId($session_id) {
    	$cache = Prodom_Connector::getConnection('db_redis');
        $user_id = $cache->get("usid_{$session_id}");
        $req_user = $cache->get("user_{$user_id}");
    	// dump($req_user);
    	// dumpr(get_included_files());
        if(is_string($req_user)) {
            $user = json_decode($cache->get("user_{$user_id}"));   
        } else {
            $user = $req_user;
        }
        if(!$user) {
            throw new Exception('Вы не авторизованы, необходимо войти на портал введя логин и пароль', 401);
        }
        return $user;
    }

    /**
     * Авторизует пользователя и возвращает идентификатор сессии
     * @since 01.07.2013
     *
     * @param string $login Логин
     * @param string $password Пароль
     *
     * @return Type_User_Login_Result
     */
    public static function login($login, $password) {
        if(!$login or !$password) {
            throw new Exception('Некорректный логин или пароль', 950);
        }
        $db = Prodom_Connector::getConnection('db_general');
        $select = $db->select()
            ->from(array('up' => 'user_profile'))
            // регистронезависимый логин
            ->where('lower(up.login) = ?', mb_strtolower($login, 'utf-8'));
            // ->where('up.pwd = ?', self::encryptingPassword($password));
        $user = $db->fetchRow($select);
        if(!$user) {
            throw new Exception('Пользователь не найден', 950);
        }
        $user = Functions::cast($user, 'Type_User');
        if($user->record_status & Constant::STATUS_BLOCKED) {
            throw new Exception('Учетная запись заблокирована.', 950);
        }
		if($user->record_status == Constant::STATUS_DELETED) {
            throw new Exception('Учетная запись удалена.', 950);
        }
        if(!IS_DEVELOPER_HOST) {
	        $pwd_salted = md5($user->salt.$password.$user->salt);
	        if($pwd_salted != $user->pwd) {
				throw new Exception('Некорректный логин или пароль', 950);
	        }
	    }
        // идентификатор сессии
        $sid = md5(time() . $login . rand(0, 999999));
        // обновление даты последнего посещения
        $last_activity_date = date('Y-m-d H:i:s');
        Model_Log::insert($user, Constant::LOG_USER_LOGIN);
        $db->update('user_profile', array('last_activity_date' => $last_activity_date), array('id = ?' => $user->id));
        // кэширование в редис
        $cache = Prodom_Connector::getConnection('db_redis');
        $key = new Rediska_Key("usid_{$sid}");
        $key->setAndExpire($user->id, Constant::VAR_USER_AUTH_PERIOD);
        $key = new Rediska_Key("user_{$user->id}");
        $key->setAndExpire($user, Constant::VAR_USER_AUTH_PERIOD);
        return new Type_User_Login_Result(array(
            'session_id' => $sid,
            'user' => $user,
        ));
    }

    /**
     * Выход пользователя
     * @author Lenar Vafin
     * @since 01.07.2013
     *
     * @param string $sessionId Идентификатор сессии
     *
     * @return boolean
     */
    public static function logout($sessionId) {
        if (!$sessionId) {
            throw new Exception('Некорректный идентификатор сессии');
        }
        //удаление объекта из редиса
        $cache = Prodom_Connector::getConnection('db_redis');
        $userId = $cache->get("usid_{$sessionId}");
        $userNumber = $cache->get("uid_{$userId}");
        if($userId) {
            // throw new Exception('Невозможно очистить данные сессии');
            $cache->delete(array(
                    "usid_{$sessionId}",
                    "user_{$userId}",
                )
            );
        }
        return true;
    }

    /**
     * Проверяет Login на занятость
     * @author Lenar Vafin
     * @since 01.07.2013
     *
     * @param string $login Имя пользователя
     *
     * @return bool
     */
    public static function checkLogin($login) {
        if (!$login) {
            throw new Exception('Некорректный логин');
        }
        $db = Prodom_Connector::getConnection('db_general');
        $select = $db->select();
        $select->from(array('up' => 'user_profile'), array('id'));
        $select->where('up.login = ?', $login);
        $result = $db->fetchRow($select);
        return $result ? false : true;
    }

    /** Шифрует пароль
    * @author Lenar Vafin
    * @since 01.07.2013
    * 
    * @param string $password Пароль
    *
    * @return string
    */
    public static function encryptingPassword($password) {
        return $password;
    }

	/**
     * Возвращает данные пользователя по логину
     * @author Roman
     *
     * @param string $login Логин
     *
     * @return Type_User_Extended
     */
    public static function loadProfileByLogin($login) {
        $db = Prodom_Connector::getConnection('db_general');
        $select = $db->select()->from(array('u' => 'user_profile'), array(
                'id',
                'login',
                'fname',
                'lname',
                'mname',
                'email',
                'avatar',
                'record_status',
                'phone',
                'organization_id',
                'access_level_id',
            ))
            ->join(array('org' => 'organization'), 'org.id = u.organization_id', array('organization_title' => 'title', 'arm_id',))
            ->where('u.login = ? ', $login);
        $row = $db->fetchRow($select);
        if(!$row) {
            throw new Exception('Неверные логин или пароль', 100);
        }
		$user = Functions::cast($row, 'Type_User_Extended');
        // ACL by Notfoolen
        $select = $db->select()
			->from(array('af' => 'arm_function'), array('function_id', 'role'))
            ->where('af.arm_id = ?', $user->arm_id);
        $acl = $db->fetchAll($select);
		$normalize_acl = array();
        foreach($acl as $a) {
            $normalize_acl[$a->function_id] = $a->role;
        }
        $user->acl = $normalize_acl;
        return $user;
}
	
	
	/**
     * Добавление пользователя
     * @author Roman
     *
     * @param Type_User $user Объект пользователя
     *
     * @return int
     */
    public static function add($user) {
		if(!$user || !isset($user->login) || !isset($user->pwd)) {
			throw new Exception('Некорректные данные пользователя', 500);
		}
		unset($user->id);
		$db = Prodom_Connector::getConnection('db_general');
		if(!self::checkLogin($user->login)) {
			throw new Exception('Указанный Email уже используется', 500);
		}
		$db->insert('user_profile', (array)$user);
		return $db->lastInsertId('user_profile', 'id');
	}

}