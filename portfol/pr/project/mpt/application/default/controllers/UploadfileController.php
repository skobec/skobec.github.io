<?php

class UploadfileController extends Zend_Controller_Action {

    function init() {
        $this->_helper->Init->init();
        if (!$this->user->isLogged()) {
            // Functions::redirect('//'.Settings::get('root_domain').'/login/');
        }
    }
/**
 * Апдейтер для проставляения нового поля
 * Потом удалить
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 */
//    public function indexAction() {
//        $this->_helper->viewRenderer->setNoRender(true);
//        $tmpFile = '/home/tugmaks/wkid.csv';
//        $file = new SplFileObject($tmpFile);
//        $db = Prodom_Connector::getConnection('db_general');
//        while (!$file->eof()) {
//            $data = $file->fgetcsv(';');
//            echo $db->update('event', array('work_type_id'=>$data[1]), 'id = '.$data[0]);
//        }
//    }

    public function eventAction() {
        Plugin_Menu::setActive('mpt-menu', 'upload');
    }

}
