<?php

/**
* @author Notfoolen
* @since 05.07.2013
*/
class Prodom_Api_Service {

    private $_user = null;
    private $_db_general;

    protected function getDbGeneral() {
        if(!$this->_db_general) {
            $this->_db_general = Prodom_Connector::getConnection('db_general');
        }
        return $this->_db_general;
    }

    public function __prepare($class, $method, $args) {
        if(count($args)) {
            $doc = Prodom_Reflection::parseMethodDoc($class, $method);
            if(array_key_exists('session_id', $args)) {
                $this->_user = Model_User::getUserBySessionId($args['session_id']);
            }
            if(isset($doc['acl'])) {
                if(!array_key_exists('session_id', $args)) {
                    throw new Exception('Вызываемому методу требуется указание аргумента $session_id', 950);
                }
                if ($this->_user) {
                    /*
                    $acl = $doc['acl'];
                    $user_acl = $this->_user->acl;
                    if(!in_array($acl, array_keys((array)$user_acl))) {
                        throw new Exception('У пользователя отсутсвует доступ к данному API-методу');
                    }
                    */
                } else {
                    throw new Exception('Пользователь не авторизован', 401);
                }
            }
        }
    }

    public function __get($name) {
        if($name == 'user') {
            if($this->_user) {
               return $this->_user;
            } else {
                throw new Exception('Пользователь не авторизован', 401);
            }
        }
    }

}