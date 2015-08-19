<?php

/**
 * 
 */
class Type_Subprogram_Plan_Money extends Mikron_Type {

    /**
     * Название 
     * @var string
     */
    public $name;

    /**
     * Сумма
     * @var float
     */
    public $y;

    /**
     * Идентификатор министерства
     * @var int
     */
    public $ministry_id;

    /**
     * Список
     * @var string[]
     */
    public $type;

    /**
     * Дополнительные данные
     * @var string[] 
     */
    public $data;

}
