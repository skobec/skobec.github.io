<?php

/**
* @author sciner
* @since 2015-02-19
*/
class Mikron_Validate_Email extends Mikron_Validate_Abstract {

	static $message = 'Некорректный адрес электронной почты';

	/**
	* Валидация
	* 
	* @param mixed $value
	* @param string[] $argument_list
	* 
	* @return string
	*/
	static function validate($value, $argument_list = array()) {
		return (strpos($value, '@') === false) || (mb_strlen($value) < 3)
			? self::$message
			: null;
	}

}