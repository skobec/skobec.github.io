<?php

/**
* @author sciner
* @since 2015-02-19
*/
class Mikron_Validate_Length extends Mikron_Validate_Abstract {

	/**
	* Валидация
	* 
	* @param mixed $value
	* @param string[] $argument_list
	* 
	* @return string|null
	*/
	static function validate($value, $argument_list = array()) {
		$min = $argument_list['min'];
		$max = $argument_list['max'];
		$len = mb_strlen($value);
		if($len < $min) {
			return "Минимальная длина строки {$min} ".Functions::morph($min, 'символ', 'символа', 'символов');
		}
		if($len > $max) {
			return "Максимальная длина строки {$max} ".Functions::morph($max, 'символ', 'символа', 'символов');;
		}
	}

}