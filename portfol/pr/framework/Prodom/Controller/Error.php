<?php

class Prodom_Controller_Error extends Zend_Controller_Action {

    protected $exception = null;
    protected $is_ajax = false;

    public function init() {
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayoutPath(dirname(__FILE__) . '/../../../library/layout/error');
        $response        = $this->getResponse();
        $exceptions      = $response->getException();
        $this->exception = $exceptions[0];
        $this->is_ajax 	 = Functions::isAjaxRequest();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->is_ajax) {
            $this->_helper->layout()->disableLayout();
        }
    }

    public function errorAction() {
        $errors = $this->_getParam('error_handler');
        if($this->view) {
            $this->view->exception = $this->exception;
            $this->view->request   = $errors->request;
        }
        switch($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // ошибка 404 - не найден контроллер или действие
                self::_forward('error404');
                break;
            default:
            	$code = $this->exception->getCode();
                // ошибка приложения
                if($code == 401) {
                    self::_forward('error401');
                } elseif($code == 404) {
                    self::_forward('error404');
                } elseif((950 <= $code) && ($code <= 959)) {
                    self::_forward('error950');
                } else {
                    self::_forward('error500');
                }
                break;
        }
    }

    private function writeLog() {
        if(!IS_DEVELOPER_HOST) {
            $log = new Type_Log_Record_Exception(array(
                'date' => date('Y-m-d H:i:s'),
                'uri' => '//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
                'post' => print_r($_POST, 1),
                'exception' => print_r($this->exception, 1),
                'message' => $this->exception->getMessage(),
                'server' => print_r($_SERVER, 1),
                'status' => $this->exception->getCode(),
                'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
            ));
            Mongo_Log::addExceptionRecord($log);
        }
        return true;
    }

    public function error404Action() {
        $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
        // Удаление добавленного ранее содержимого        
        $this->getResponse()->clearBody();
        if($this->is_ajax) {
            $this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8', true);
            echo json_encode(array('status' => 'error', 'message' => $this->exception->getMessage(), 'code' => 404), JSON_UNESCAPED_UNICODE);
        } else {
            $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8', true);
            $this->view->message = IS_DEVELOPER_HOST ? $this->exception->getMessage() : 'Страница не найдена';
            $this->view->error_code = 404;
        }
    }

    public function error401Action() {
        Functions::redirect('//'.Settings::get('root_domain').'/index/logout/');
    }

    public function error500Action() {
        // Удаление добавленного ранее содержимого
        $this->getResponse()->clearBody();
        self::writeLog();
        if($this->is_ajax) {
            $this->getResponse()->setRawHeader('HTTP/1.1 200 Ok');
            $this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8', true);
            echo json_encode(array('status' => 'error', 'message' => $this->exception->getMessage()), JSON_UNESCAPED_UNICODE);
        } else {
            $this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Server Error');
            $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8', true);
            if($this->view) {
	            $this->view->error_code = $this->exception->getCode();
	            $this->view->message = IS_DEVELOPER_HOST ? $this->exception->getMessage() : 'Произошла непредвиденная ошибка.';
			} else {
				echo '<strong>Ошибка №', $this->exception->getCode(), '</strong>', '<br>', (IS_DEVELOPER_HOST ? $this->exception->getMessage() : 'Произошла непредвиденная ошибка.');
			}
        }
    }
    
    public function error950Action() {
        $this->getResponse()->setRawHeader('HTTP/1.1 200 Ok');
        // Удаление добавленного ранее содержимого
        $this->getResponse()->clearBody();
        self::writeLog();
        if($this->is_ajax) {
            $this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8', true);
            $resp = array('status' => 'error', 'message' => $this->exception->getMessage(), 'code' => 200);
            if($this->exception->getCode() == 951) {
				$resp['error_field_list'] = json_decode($resp['message']);
				$resp['message'] = 'Ошибка валидации';
        	}
            echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        } else {
            $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8', true);
            $this->view->message = $this->exception->getMessage();
            $this->view->error_code = 500;
        }
    }

}
