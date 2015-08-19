<?php

class Valid {

    /**
     * Проверка номера телефона
     * 
     * @param string $phone номер
     * 
     * @result string
     */
    public static function phone($phone = null) {
        if(preg_match("/[^0-9\+()\ \-]/", $phone)) {
            return false;
        }
        return true;
    }

	/**
	 * Проверка кода, которое может состоять только из английских букв и цифр и не начинаться на цифру
	 * @author sciner
     * @since 22.07.2013
     * 
     * @param string $string Код
	 * 
	 * @return bool
	 */
	public static function code($string = null) {
		if(!preg_match('/^[a-z0-9]+$/', $string)) {
			return false;
		}
        if(is_numeric(substr($string, 0, 1))) {
            return false;
        }
		return true;
	}

	/**
	 * Проверка email
	 * 
	 * @param string $email
	 * 
	 * @return bool
	 */
	public static function email($email = null) {
		if(preg_match("/[^0-9a-zA-ZабвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ_\@\.\-]/", $email) || !preg_match("/.+@.+\..+/", $email)) {
			return false;
		}
		return true;
	}

	/**
	 * Проверка GUID
	 * 
	 * @param string $value
	 * 
	 * @result string
	 */
	public static function guid($value) {
        return !empty($value) && preg_match('/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i', $value);
    }
	
	
}