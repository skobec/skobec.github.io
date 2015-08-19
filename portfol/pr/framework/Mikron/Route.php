<?php

class Mikron_Route {

    private static $routes = array();
    public static $lastException = null;

    public static $action = null;

    public static function init() {
    }

    /**
    * Load file with route:add() commands
    * 
    * @param string $file
    */
    private static function load($file) {
        require_once($file);
    }

    public static function assign($uri, $action = null, $layout = null, $hasGet = array(), $hasPost = array(), $params = array()) {
        self::add(
            array(
                    'uri' => $uri,
                    'hasget' => $hasGet,
                    'haspost' => $hasPost,
                    'action' => $action,
                    'layout' => $layout,
                    'params' => $params,
                )   
        );
    }

    public static function add() {
        foreach(func_get_args() as $route) {
            self::$routes[] = $route;
        }
    }

    public static function call() {
        $application = 'index';
        $controller = 'index';
        $action = 'index';
        $layout = null;
        $view = null;
        $uri = $_SERVER['REQUEST_URI'];
        if(substr($uri, 0, 1) == '/') {
            $uri = substr($uri, 1);
        }
        $uri = explode('?', $uri);
        $uri = array_shift($uri);
        // адрес страницы разбитый слешами
        $uri_components = explode('/', $uri);
        $uri_components = array_filter($uri_components);
        $uri_components_count = count($uri_components);
        // переменные из адреса страницы
        $route_variables = array();
        $uri_variables = array();
        foreach(self::$routes as $route) {
            $route_uri = $route['uri'];
            if(!is_array($route_uri)) {
                $route_uri = array($route_uri);
            }
            // индекс секции рассматриваемой маски
            $part_number = 0;
            // если указано несколько адресных масок
            foreach($route_uri as $ruri) {
                // разбиваем на секции
                $ruri = explode('/', $ruri);
                $ruri = array_filter($ruri);
                if(count($ruri) != count($uri_components)) {
					continue;
                }
                $use_this_route = true;
                $part_number = 0;
                // просматриваем каждую секцию и проверяем ее в текущем адресе
                foreach($ruri as $r) {
	                    // если в маске пути больше секций, чем в рассматриваемом адресе
	                    // переход на следующую маску
	                    if($part_number >= $uri_components_count) {
	                        $use_this_route = false;
	                        break;
	                    }
                        $component = $uri_components[$part_number++];
                    // если сектор рассматривается в качестве переменной
                    if(substr($r, 0, 1) == ':') {
                        $v = explode('=', substr($r,1), 2);
                        // имя переменной
                        $key = $v[0];
                        // если есть условие для значения переменной
                        if(count($v) == 2) {
                            $cr = $v[1];
                            // простая проверка является ли переменная числом
                            if($cr == 'digit') {
                                if(is_numeric($component)) {
                                    continue;
                                }
                                $use_this_route = false;
                                break;
                            }
                            // иначе рассматриваем условие в качестве регулярного выражения
                            // которому должна соответствовать переменная
                            $cr = '/^'.$cr.'$/';
                            $variables = false;
                            $eq = preg_match_all($cr, $component, $variables);
                            // если соответствует регулярному выражению,
                            // тогда переходим к следующему сектору
                            if(!$eq) {
                                $use_this_route = false;
                                break;
                            }
                        }
                        $uri_variables[$key] = $component;
                    }
                    else {
                        if($r != $component) {
                            $use_this_route = false;
                            break;
                        }
                    }
                }
                if($use_this_route) {
                    $action = $route['action'];
                    $view = Zend_Layout::getMvcInstance()->getView();
                    $action($uri_variables, $view);
                } else {
                    
                }
            }
        }
    }

}
