<?php

class WidgetController extends Zend_Controller_Action {

    function init() {
        $this->_helper->Init->init();
        if (!$this->user->isLogged()) {
            // Functions::redirect('//'.Settings::get('root_domain').'/login/');
        }
    }

    /**
    * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
    * @since 2015-06-16
    */
    public function getAction() {
        $this->_helper->layout->disableLayout();
        $widget_id = $this->getRequest()->getParam('id');
        $this->view->is_expanded = $this->getRequest()->getParam('is_expanded') === 'true';
        $filter = new Type_Widget_Filter([
            'widget_id' => $widget_id,
            'include_data' => true,
            'years' => 2015,
        ]);
        $this->view->widget = Service::Widget()->getData($filter);
    }

}
