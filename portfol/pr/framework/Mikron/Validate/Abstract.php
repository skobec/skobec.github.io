<?php

/**
* @author sciner
* @since 2015-02-19
*/
abstract class Mikron_Validate_Abstract {

	static $message = 'Поле заполнено неверно';

	/**
	* Валидация
	* 
	* @param mixed $value
	* @param string[] $argument_list
	* 
	* @return string
	*/
	static function validate($value, $argument_list = array()) {
		return self::$message;
	}

}