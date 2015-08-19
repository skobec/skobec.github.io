<?php

/**
* Результат авторизации
*/
class Type_User_Login_Result extends Prodom_Type {

    /**
    * Идентификатор сессии
    * 
    * @var string
    */
    public $session_id;

    /**
    * Профиль пользователя
    * 
    * @var Type_User
    */
    public $user;

}