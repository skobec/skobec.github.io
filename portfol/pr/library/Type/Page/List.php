<?php
/**
 * Created by PhpStorm.
 * User: mart
 * Date: 13.08.15
 * Time: 9:44
 */
class Type_Page_List extends Prodom_Type {

    /**
     * Список домов
     * @var Type_Pages_Item[]
     */
    public $items = array();

    /**
     * Пагинатор
     * @var Type_Paginator
     */
    public $paginator = null;

}