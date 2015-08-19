<?php

class Engine_Field_Type extends Prodom_Type {

    /**
    * @var int
    */
    public $id;

    /**
    * @var string
    */
    public $code;

    /**
    * @var string
    */
    public $title;

    /**
    * @var string
    */
    public $db_type;

    /**
    * @var string[]
    */
    public $validate;

    /**
    * @var string[]
    */
    public $ex_validate;

}
