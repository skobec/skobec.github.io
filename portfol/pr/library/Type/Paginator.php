<?php

class Type_Paginator extends Prodom_Type {
	
	/**
	* Количество элементов на страницу
	* @is_require
	* 
	* @var int
	*/
	public $items_per_page;
	 
	/**
	* Номер запрашиваемой страницы
	* @is_require
	* 
	* @var int
	*/
	public $current_page;
	
	/**
	* Количество записей
	* 
	* @var int
	*/
	public $records_count;
	
	/**
	* Количество страниц
	* 
	* @var int
	*/
	public $total_pages;

}