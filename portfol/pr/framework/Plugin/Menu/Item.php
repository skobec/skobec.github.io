<?php

/**
 * Элемент плагина меню
 * @author sciner
 * @since 2013-04-07
 */
class Plugin_Menu_Item extends Prodom_Type {

    /**
     * Код (уникальный на 1 меню)
     * @var string
     */
    public $code;

    /**
     * Заголовок
     * @var string
     */
    public $title;

    /**
     * Адрес
     * @var string
     */
    public $uri;

    /**
     * Имя (имена) класса(ов)
     * @var string
     */
    public $class;

    /**
     * Подпункты
     * @var Plugin_Menu_Item[]
     */
    public $child;

    /**
     * Скрывает меню
     * @var bool
     */
    public $hidden;

    /**
     * Дополнительные html аттрибуты для контейнера элемента меню
     * @var array 
     */
    public $itemOptions = array();

    /**
     * Дополнительные html аттрибуты для ссылки элемента меню
     * @var array 
     */
    public $linkOptions = array();

}
