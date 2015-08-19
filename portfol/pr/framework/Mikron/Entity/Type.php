<?php

/**
* Тип поля сущности
*/
class Mikron_Entity_Type extends Mikron_Type {

	/**
    * ID
    * @var int
    * @hidden = 1
    */
	public $id;

    /**
    * ID сущноси
    * @var int
	* @link Mikron_Entity
    */
	public $mikron_entity_id;

    /**
    * Описание
    * @var string
    */
	public $title;

}