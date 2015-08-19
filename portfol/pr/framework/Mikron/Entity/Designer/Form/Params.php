<?php

class Mikron_Entity_Designer_Form_Params extends Mikron_Type {

    public $id = null;
    public $form_id = null;
    public $popup = null;
    public $readonly = null;
    public $state = null;
    public $field_list = null;
    public $read_only_values = array();
    public $action;

    /**
    * Текст на кнопке "Создать"
    * @var string
    */
    public $text_btn_add = 'Добавить...';

    /**
    * Заголовок формы
    * @var string
    */
    public $text_caption = 'Новая запись';

}
