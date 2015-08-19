<?php

/**
* Zend_Session replacement
* @author sciner
* @since 2013-04-15
*/
class Prodom_Session {

    public static function namespaceUnset($namespace) {
        $s = array_keys($_SESSION);
        foreach($s as $key) {
            if(substr($key, 0, strlen($namespace)) == $namespace) {
                unset($_SESSION[$key]);
            }
        }
    }

    public static function start() {
        if(!isset($_SESSION)) {
            $startedCleanly = session_start();
        }
    }

}
