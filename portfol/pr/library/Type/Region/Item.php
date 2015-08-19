<?php

/**
 * 
 */
class Type_Region_Item extends Mikron_Type {

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
     * Название
     * @var string
     */
    public $title;

    /**
     * Ключ региона, для формирования карты хайчарт
     * @var string
     */
    public $hc_key;
}
