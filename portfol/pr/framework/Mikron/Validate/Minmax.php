<?php

/**
* @author sciner
* @since 2015-03-28
*/
class Mikron_Validate_Minmax extends Mikron_Validate_Abstract {

	/**
	* Валидация числа на минимальное и максимальное значение
	* 
	* @param mixed $value
	* @param string[] $argument_list
	* 
	* @return string|null
	*/
	static function validate($value, $argument_list = array()) {
		$min = $argument_list['min'];
		$max = $argument_list['max'];
		$value = (float)$value;
		if(is_numeric($min) && ($value < $min)) {
			return "Минимальное значение {$min}";
		}
		if(is_numeric($max) && ($value > $max)) {
			return "Максимальное значение {$max}";
		}
	}

}