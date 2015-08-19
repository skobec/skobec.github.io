<?php

class MikronController extends Zend_Controller_Action {

    function init()    {
        $this->_helper->Init->init();
        /*if(!$this->user->isLogged()) {
           Functions::redirect('//'.Settings::get('root_domain').'/login/');
        }*/
    }

    public function indexAction() {
        Mikron_Crud::init();
        Mikron_Route::call();
    }

    public function listAction() {
        $table_name = $this->_getParam('table');
        $entity_name = Mikron_Entity::getName($table_name);
        $this->view->item = new $entity_name();        
    }

    public function editAction() {
        $table_name = $this->_getParam('table');
        $entity_name = Mikron_Entity::getName($table_name);
        $id = $this->_getParam('id');
        $this->view->item = new $entity_name($id);
    }

}
