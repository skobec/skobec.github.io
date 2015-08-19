<?php

/**
 * Контроллер поиска на портале
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 23.07.2015
 */
class SearchController extends Zend_Controller_Action {

    function init() {
        $this->_helper->Init->init();
        /* if(!$this->user->isLogged()) {
          Functions::redirect('//'.Settings::get('root_domain').'/login/');
          } */
    }

    /**
     * @ajax
     */
    public function indexAction() {
        $query = $this->getRequest()->getParam('query');
        $data = Service::Scenario()->search($query);
        $json = new stdClass;
        $json->query = $query;
        $json->suggestions = $data;
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

}
