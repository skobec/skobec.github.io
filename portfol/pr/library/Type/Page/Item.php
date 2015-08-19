<?php
/**
 * Created by PhpStorm.
 * User: mart
 * Date: 13.08.15
 * Time: 9:10
 */
class Type_Page_Item extends Mikron_Type {

    /**
    * id
    * @var int
    */
    public $id;

    /**
    * id Родителя
    * @var int
    */
    public $parent_id;

    /**
    * Название страницы
    * @var string
    */
    public $name;

    /**
    * Описание страницы
    * @var string
    */
    public $description;

    /**
    * Адрес страницы
    * @var string
    */
    public $link;

    /**
    * Ссылка на превью страницы
    * @var string
    */
    public $preview;

    /**
    * Группа страницы
    * @var int
    * @link Type_Page_Group
    */
    public $page_group_id;

    /**
    * Видимость страницы
    * @var int
    */
    public $published;

}
