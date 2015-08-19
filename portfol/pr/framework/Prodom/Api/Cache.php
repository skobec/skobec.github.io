<?php

/**
* Кеширование ответов веб-службы во временной папке
* @author sciner
* @since 2013-04-15
* 
* @example $news = Services::News()->_cache(3600)->getList(); // Кеширование на 1 час
*/
class Prodom_Api_Cache {

    private $super_hash = null;
    private $service = null;
    private $time = null;

    public function __construct($service, $time, $super_hash = null) {
        $this->super_hash = $super_hash;
        $this->service = $service;
        $this->time = $time;
        return $this;
    }

    public function __call($method, $arguments) {
    	$hash = md5(serialize($arguments));
        $dir = sys_get_temp_dir().'/prodom_api/';
        if(!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $cache_path = $dir.$this->super_hash.'_'.$method.'_'.$hash.'.tmp';
        if(file_exists($cache_path)) {
            if(filemtime($cache_path) + $this->time >= time()) {
                return unserialize(file_get_contents($cache_path));
            } else {
                @unlink($cache_path);
            }
        }
        $resp = call_user_func_array(array($this->service, $method), $arguments);
        file_put_contents($cache_path, serialize($resp));
        return $resp;
    }

}