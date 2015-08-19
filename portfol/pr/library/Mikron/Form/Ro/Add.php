<?php

class Mikron_Form_Ro_Add {

	public static function draw() {
		if(defined('READ_ONLY_MODE') && !READ_ONLY_MODE) {
			return false;
		}
        $form_params = new Mikron_Entity_Designer_Form_Params(array(
			'id' => 1,
            'popup' => true,
            'text_btn_add' => 'Новый счёт РО...',
        ));
        return Mikron_Entity_Designer::drawForm(new Type_Billing_Bill_Ro, null, $form_params);
	}

}
