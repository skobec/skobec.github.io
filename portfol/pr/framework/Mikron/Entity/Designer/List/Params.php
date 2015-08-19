<?php

class Mikron_Entity_Designer_List_Params extends Mikron_Type {

    public $id = null;
    
    /**
    * @var string
    */
    public $state;
    
    /**
    * @var string
    */
    public $action;

    /**
    * Количество элементов на страницу
    * @var int
    */
    public $items_per_page = null;

    /**
    * Скрыть строку с фильтрами
    * @var bool
    */
    public $hide_filter = false;

    /**
    * Запретить сортировку по клику на заголовках таблицы
    * @var bool
    */
    public $hide_sort = false;

    public $can_edit = true;

    public $edit_popup = false;

    public $edit_state;

    public $can_delete = true;

    public $can_reorder = false;

    /**
    * Список столбцов к отображению
    * @var Mikron_Entity_Field[]|string[]
    */
    public $field_list;

    public $extra_fields;

    /**
    * Ссылка на страницу редактирования элемента
    * @var string
    */
    public $edit_link;

    public $delete_state;

    /**
    * @var bool
    */
    public $reorder_state;

    /**
    * Фильтр
    * @var Mikron_Type
    */
    public $filter;

    public $toolbar_numerator;

    public $item_menu;

    public $sub_select_list;

    /**
    * Относительный путь к шаблону
    * 
    * @var string
    */
    public $template;
    public $form_id;

}