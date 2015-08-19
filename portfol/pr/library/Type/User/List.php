<?php

/**
 * Список пользователей
 */
class Type_User_List {
	
	/**
	 * Список пользователей
	 * 
	 * @var Type_User[]
	 */
	public $items = array();
	
	/**
	 * Пагинатор
	 * 
	 * @var Type_Paginator
	 */
	public $paginator = null;
	
}