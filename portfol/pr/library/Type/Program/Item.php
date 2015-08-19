<?php
/**
 * Модель программы
 */
class Type_Program_Item extends Mikron_Type {

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
     * Код программы
     * @var string
     */
    public $number;

    /**
     * Программные или непрограммные расходы
     * @var bool
     */
    public $is_program;
}
