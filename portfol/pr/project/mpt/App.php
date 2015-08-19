<?php

    /**
    * Карточка спецсчёта
    */
    Mikron_Route::assign(array('specaccount/edit/:billing_bill_spec_id'), function($var, $view) {
        $view->MenuCode = 'menu-specaccount';
        Plugin_Menu::setActive('billing-menu', 'specaccount');
        Plugin_Menu::setActive($view->MenuCode, 'card');
        Plugin_Menu::setVariable('id', $var['billing_bill_spec_id']);
        $fields = new Type_Billing_Bill_Spec($var['billing_bill_spec_id']);
	});