<?php

class Admin_PagesController extends Zend_Controller_Action {

    function init() {
        $this->_helper->Init->init();
        if (!$this->user->isLogged()) {
            // Functions::redirect('/');
        }
        $this->_helper->layout->setLayout('admin');
        Plugin_Menu::setActive('menu-admin', 'pages');
    }

    public function indexAction() {
    }

}
