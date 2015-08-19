<?php

/**
* @author sciner
* @since 2015-02-26
*/
class Mikron_Validate_Regexp extends Mikron_Validate_Abstract {

	/**
	* Валидация
	* 
	* @param mixed $value
	* @param string[] $argument_list
	* 
	* @return string|null
	*/
	static function validate($value, $argument_list = array()) {
		$regexp = $argument_list['regexp'];
		$message = $argument_list['message'];
		if(!preg_match($regexp, $value)) {
			return $message;
		}
	}

}