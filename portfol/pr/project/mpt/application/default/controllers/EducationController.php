<?php

/**
 * Description of EducationController
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 06.07.2015
 */
class EducationController extends Zend_Controller_Action {

    function init() {
        $this->_helper->Init->init();
        if (!$this->user->isLogged()) {
            // Functions::redirect('//'.Settings::get('root_domain').'/login/');
        }
    }

    public function indexAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->setLayout('education');
    }

}
