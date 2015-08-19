<?php

/**
* Фильтр для виджетов
*/
class Type_Widget_Filter extends Mikron_Type {

    /**
    * Идентификатор виджета
    * @var int
    */
    public $widget_id;

    /**
    * Идентификатор виджета
    * @var int
    */
    public $widget_type;

    /**
    * Вернуть данные для построения виджета
    * @var bool
    */
    public $include_data;

    /**
    * Раскрыт
    * @var bool
    */
    public $is_expanded;

    /**
    * Год(а)
    * @var int[]
    */
    public $years;

}
