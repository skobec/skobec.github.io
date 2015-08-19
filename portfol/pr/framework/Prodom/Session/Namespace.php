<?php

/**
* Zend_Session replacement
* @author sciner
* @since 2013-04-15
*/
class Prodom_Session_Namespace implements IteratorAggregate {

    private $_namespace = null;

    public function __construct($namespace) {
        $this->_namespace = $namespace;
    }

    public function & __get($name) {
        if(array_key_exists($this->_namespace, $_SESSION)) {
            if(array_key_exists($name, $_SESSION[$this->_namespace])) {
                return $_SESSION[$this->_namespace][$name];
            }
        } else {
            $_SESSION[$this->_namespace][$name] = null;
            return $_SESSION[$this->_namespace][$name];
        }
    }

    public function __set($name, $value) {
        if(!array_key_exists($this->_namespace, $_SESSION)) {
            $_SESSION[$this->_namespace] = array();
        }
        $_SESSION[$this->_namespace][$name] = $value;
    }

    public function __isset($name) {
        if(array_key_exists($this->_namespace, $_SESSION)) {
            if(array_key_exists($name, $_SESSION[$this->_namespace])) {
                return true;
            }
        }
        return false;
    }

    public function __unset($name) {
        if(isset($this->$name)) {
            unset($_SESSION[$this->_namespace][$name]);
        }
    }

    public function getNamespace() {
        return $this->_namespace;
    }

    /**
    * Return an iteratable object for use in foreach and the like,
    * this completes the IteratorAggregate interface
    *
    * @return ArrayObject - iteratable container of the namespace contents
    */
    public function getIterator() {
        $this->_start();
        $items = array();
        if(array_key_exists($this->_namespace, $_SESSION)) {
            if(is_array($_SESSION[$this->_namespace])) {
                $items = $_SESSION[$this->_namespace];
            }
        }
        return new ArrayObject($items);
    }

}
