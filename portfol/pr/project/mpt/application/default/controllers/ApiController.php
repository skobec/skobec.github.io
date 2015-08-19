<?php

class ApiController extends Zend_Controller_Action {

	function init() {
		$this->_helper->Init->init();
		if(!$this->user->isLogged()) {
			Functions::redirect('//'.Settings::get('root_domain').'/login/');
		}
	}

	/**
	* @ajax
	*/
	public function runAction() {
		$service = $this->_getParam('service');
		$method = $this->_getParam('method');
		$form = $_POST['form'];
		$service = Service::{$service}();
		$begin = array('session_id' => $this->user->getSessionId());
                if(isset($_FILES)) {
                    if($this->_getParam('service') == 'Billing_Account' && $method == 'close') {
                        $form['files'] = $_FILES;
                    }
                }
		$form = array_merge($begin, $form);
		$resp = call_user_func_array(array($service, $method), $form);
		echo json_encode(array('status' => 'success', 'message' => 'Вызов успешно обработан', 'data' => $resp), JSON_UNESCAPED_UNICODE);
	}

}