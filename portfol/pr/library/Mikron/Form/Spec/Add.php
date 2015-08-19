<?php

class Mikron_Form_Spec_Add {

	public static function draw($form_field_list) {
		if(defined('READ_ONLY_MODE') && !READ_ONLY_MODE) {
			return false;
		}
        $form_params = new Mikron_Entity_Designer_Form_Params(array(
			'id' => 2,
            'popup' => true,
            'text_btn_add' => 'Новый спец счёт...',
			'field_list' => $form_field_list,
        ));
        return Mikron_Entity_Designer::drawForm(new Type_Billing_Bill_Spec, null, $form_params);
	}

}
