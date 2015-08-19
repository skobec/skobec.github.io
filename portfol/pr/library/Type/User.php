<?php

/**
* Пользователь
*/
class Type_User extends Mikron_Type {

    /**
     * ID
     * @var int
     */
    public $id;

    /**
     * Фамилия
     * @var string
     */
    public $lname;

	/**
	 * Имя
	 * @var string
	 */
	public $fname;

	/**
	 * Отчество
	 * @var string
	 */
	public $mname;

	/**
	 * Логин
	 * @var string
	 * @require
	 * @tostring
	 */
	public $login;

	/**
	 * Пароль
	 * @var string
	 * @require
     * @hidden = 1
	 */
	public $password;

	/**
	 * Email
	 * @var string
	 */
	public $email;

	/**
	 * Файл аватара
	 * @var string
	 */
	public $avatar;

	/**
	 * Телефон
	 * @var string
	 */
	public $phone;

	/**
	 * Соль
	 * @hidden = 1
	 * @var string
	 */
	public $salt;

    /**
    * Статус записи
    * @var int
    */
    public $record_status;

    /**
    * Дата последней активности пользователя
    * @var string
    */
    public $last_activity_date;

    /**
    * Уровень доступа
    * @var int
    * @hidden = 1
    */
    public $flags_user;

    /**
    * Уровень доступа (админ.)
    * @var int
    * @hidden = 1
    */
    public $flags_admin;

}