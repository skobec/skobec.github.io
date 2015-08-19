<?php

class Mikron_Entity_Designer_Field extends Mikron_Type {
    
    public $path;

    public function __construct($input) {
        $fields = get_object_vars($this);
        foreach($input as $name => $value) {
            if(array_key_exists($name, $fields)) {
                // $this->$name = $value;
            }
        }
    }

}