<?php

/**
 * Пользователь (расширенный)
 */
class Type_User_Extended extends Type_User {
	
	/**
    * Идентификатор АРМ
    * 
    * @var int
    */
    public $arm_id;
	
    /**
     * Идентификатор населенного пункта, в котором находится пользователь
     * 
     * @var guid
     */
    public $locality_id = null;
    
    /**
     * Код населенного пункта, в котором находится пользователь
     * 
     * @var string
     */
    public $locality_code = null;
    
    /**
     * Название населенного пункта, в котором находится пользователь
     * 
     * @var string
     */
    public $locality_title = null;
    
    /**
     * Сокращенное наименование типа населенного пункта
     * 
     * @var string
     */
    public $locality_prefix = null;

    /**
     * Адрес
     * 
     * @var string
     */
    public $address_custom = null;
    
    /**
     * Идентификатор организации, в которой пользователь является сотрудником
     * 
     * @var guid
     */
    public $organization_id = null;
    
    /**
     * Название организации, в которой пользователь является сотрудником
     * 
     * @var string
     */
    public $organization_title = null;
	
	/**
     * Уровень доступа
     * 
     * @var int
     */
    public $access_level_id = null;

    /**
    * Список acl
    * @var array
    */
    public $acl = array();

}