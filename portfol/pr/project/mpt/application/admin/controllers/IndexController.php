<?php

class Admin_IndexController extends Zend_Controller_Action {

    function init() {
        $this->_helper->Init->init();
        if (!$this->user->isLogged()) {
            // Functions::redirect('/');
        }
        $this->_helper->layout->setLayout('admin');
    }

    public function indexAction() {
        Plugin_Menu::setActive('menu-admin', 'main');
    }

}
