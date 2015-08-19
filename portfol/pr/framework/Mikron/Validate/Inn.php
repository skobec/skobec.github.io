<?php

/**
* @author sciner
* @since 2015-02-19
*/
class Mikron_Validate_Inn extends Mikron_Validate_Abstract {

	static $message = 'ИНН может быть длиной 10 или 12 символов и состоять только из цифр';

	/**
	* Валидация
	* 
	* @param mixed $value
	* @param string[] $argument_list
	* 
	* @return string
	*/
	static function validate($value, $argument_list = array()) {
		if(!is_string($value)) {
			return self::$message;
		}
		if(!is_numeric($value)) {
			return self::$message;
		}
		return in_array(strlen($value), array(10, 12)) ? null : self::$message;
	}

}