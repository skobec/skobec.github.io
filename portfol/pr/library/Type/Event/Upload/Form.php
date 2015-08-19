<?php

class Type_Event_Upload_Form extends Mikron_Type
{
    /**
     * ID
     *
     * @var int
     * @hidden = 1
     */
    public $id;

    /**
     * тип формы загрузки
     *
     * @var int
     * @link Type_Event_Upload_Form_Type
     */
    public $event_upload_form_type_id;

    /**
     * Файл
     *
     * @var file
     */
    public $file;
}
