<?php

/**
* Шаблонизатор
* @author Notfoolen
* @since 14.07.2014
*/
class Template {

    private $data;
    private $view;

    public function __construct($view_path) {
        $this->setView($view_path);
    }

    public function &__get($name) {
        if(array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
    }

    public function __set($name, $value) {
        if(!$this->data) {
            $this->data = array();
        }
        $this->data[$name] = $value;
    }

    public function setView($view_path) {
        $this->view = base64_encode(file_get_contents($view_path));
    }

    public function draw() {        
        include "data://text/plain;base64,".$this->view;
    }

}
