<?php

class Config {

    private static $settings;

    public static function read($file_name) {
        self::$settings = json_decode(file_get_contents($file_name));
        if(!is_object(self::$settings)) {
            throw new Exception("Can't read settings from '{$file_name}'");
        }
        return self::$settings;
    }
    
    public static function get($name) {
    	return isset(self::$settings->$name) ? self::$settings->$name : null;
    }

}