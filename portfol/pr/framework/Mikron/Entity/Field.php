<?php

class Mikron_Entity_Field extends Mikron_Type {

    /**
    * ID
    * @hidden = 1
    * @var string
    */
    public $id;

	/**
	* Имя поля
	* @is_require = 1
	* @var string
    * @validate regexp(regexp="/^[a-zA-Z_]*$/", message="Недопустимые символы в названии поля")
	*/
    public $name;

    /**
    * Описание поля
	* @is_require = 1
    * @var string
    */
    public $description;

    /**
    * Путь к полю в БД
    * @hidden = 1
    * @var string
    */
    public $path;

    /**
    * Тип поля
    * @hidden = 1
    * @var string
    */
    public $type;

    /**
    * Тип
    * @var int
	* @is_require = 1
	* @link Mikron_Entity_Type
    */
	public $mikron_entity_type_id;

    /**
    * Тип
    * @var int
	* @hidden = 1
	* @link Mikron_Entity
    */
	public $mikron_entity_id;

    /**
    * Фильтр
    * @hidden = 1
    * @var mixed
    */
    public $filter;

    /**
    * Только для чтения
    * @var bool
    */
    public $readonly;

    /**
    * Формат офрмления вывода поля
    * @hidden = 1
	* @var callback function($row) { ... }
	*/
    public $format;

    /**
    * Скрытое поле
    * @var bool
    */
    public $hidden;

    /**
    * Имя типа связанной сущности
    * @hidden = 1
    * @var string
    */
    public $link;

    /**
    * Имя сущности для связей
    * @hidden = 1
    * @var string
    */
    public $savelink;

    /**
    * Имя html шаблона реализуещего редактор поля в форме
    * @var string
    * @hidden = 1
    */
    public $editor;

    /**
    * Функция, callback, для модификации тела запроса при запросе списка
    * @var function
    * @hidden = 1
    */
    public $query;

    /**
    * Функция агрегирования SUM/MIN/MAX/GROUP
    * @var string
    * @hidden = 1
    */
    public $aggr;

    /**
    * Доп значения
    * @var array
    * @hidden = 1
    */
    public $editor_setting;

    /**
    * Обязательно к заполнению
    * @var bool
    */
    public $is_require;

    /**
    * Правила валидации
    * @var text
    */
    public $validate;

	/**
	* Старое имя поля
	* @hidden = 1
	*/
    public $old_name;

    /**
    * Значение по умолчанию
    * @var string
    */
    public $default_value;

}