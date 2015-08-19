<?php

class Mikron_Entity extends Mikron_Type {

	/**
    * ID
    * @var int
    */
	public $id;
	
    /**
    * Название
    * @var string
    * @validate regexp(regexp="/^[a-zA-Z_]*$/", message="Недопустимые символы в названии объекта")
    */
	public $code;
	
    /**
    * Описание
    * @var string
    */
	public $title;

}