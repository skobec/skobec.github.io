<?php

function dump($var) {
    echo '<pre>';
    if(is_string($var)) {
        echo $var;
    } else {
        var_export($var);
    }
    exit;
}

function dumpr($var) {
    echo '<pre>';
    if(is_string($var)) {
        echo $var;
    } else {
        print_r($var);
    }
    exit;
}

function dumpre($var, $error_code = 0) {
    throw new Exception('<pre>'.var_export($var, 1).'</pre>', $error_code);
}

function dumpf($filename, $value, $append = false, $line_endings = true) {
	$trace = debug_backtrace();
	$nearest_call = array_shift($trace);
    $is_windows = (strpos(strtoupper(PHP_OS), 'WIN') !== false);
	$dir_path = IS_DEVELOPER_HOST || $is_windows ? dirname($nearest_call['file']) : '/tmp';
	file_put_contents($dir_path."/{$filename}.tmp", print_r($value, 1).($line_endings ? "\n" : ''), $append ? FILE_APPEND : null);
}

if(defined('SHOW_ERROR')) {
    return;
}

// обработчики ошибок
if(IS_DEVELOPER_HOST) {
    ini_set('display_errors', 1);
} else {
    ini_set('display_errors', 0);
}

if (php_sapi_name() != 'cli') {
    error_reporting(E_ALL | E_STRICT | E_COMPILE_ERROR | E_COMPILE_WARNING);
    set_error_handler(function ($errno, $errstr, $error_file, $error_line) {
            if(defined('SHOW_ERROR') && !SHOW_ERROR) {
                return;
            }
            if(!IS_DEVELOPER_HOST) {
                if(!in_array($errno, array(950, 401))) {
                    $errstr = 'Произошла непредвиденная ошибка';
                }
            }
            $error_file = str_replace('\\', '/', $error_file);
            $error_file = str_replace(str_replace('\\', '/', realpath(dirname(__FILE__) . '/../')), null, $error_file);
            $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            if($is_ajax) {
                die(json_encode(array('status' => 'error', 'message' => "Error: [{$errno}] {$errstr} - {$error_file}:{$error_line}", 'code' => 100)));
            } else {
                die(new Prodom_Api_Response(100, "Error: [{$errno}] {$errstr} - {$error_file}:{$error_line}", null, 0));
            }
        }, E_ALL | E_STRICT);

    register_shutdown_function(function() {
        $error = error_get_last();
        if($error !== null) {
            if(defined('SHOW_ERROR') && !SHOW_ERROR) {
                return;
            }
            $error_file = $error['file'];
            $error_line = $error['line'];
            $errstr = $error['message'];
            $errno = null;
            ob_get_clean();
            $is_ajax = Functions::isAjaxRequest();
            if(ob_get_length()) {
                ob_get_clean();
            }
            $message = "Error: [{$errno}] {$errstr} - {$error_file}:{$error_line}";
            if(strpos($message, 'POST Content-Length') !== false) {
                $message = 'Превышен максимально допустимый размер файла';
            }
            if($is_ajax) {
                echo json_encode(array('status' => 'error', 'message' => $message, 'code' => 100));
            } else {
                if(!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] == 'POST') {
                    echo new Prodom_Api_Response(100, $message, null, 0);
                } else {
                    header('HTTP/1.0 500 Server error');
                    //if(IS_DEVELOPER_HOST) {
                    echo $message;
                    //} else {
                    //    echo '<h3>500, Server error</h3>';
                    //}
                }
            }
        }
    });
}
