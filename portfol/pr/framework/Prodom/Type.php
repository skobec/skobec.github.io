<?php

class Prodom_Type {

    public function __construct($state = array()) {
        $fields = get_object_vars($this);
        foreach($state as $name => $value) {
            if(array_key_exists($name, $fields)) {
                $this->$name = $value;
            }
        }
        $this->__init();
    }

    public static function prepare() {
    }

    public function __set($field, $value) {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        throw new Exception("Ошибка записи атрибута ".get_class($this)."::{$field}, атрибут не существует. Вызов произошел в {$caller['file']}:{$caller['line']}.");
    }

    public function __get($field) {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        throw new Exception("Ошибка чтения атрибута ".get_class($this)."::{$field}, атрибут не существует. Вызов произошел в {$caller['file']}:{$caller['line']}.");
    }
    
    public function __init() {
        return true;
    }
}
