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
    * ���������� ��������� �� ��������
    * @var int
    */
    public $items_per_page = null;

    /**
    * ������ ������ � ���������
    * @var bool
    */
    public $hide_filter = false;

    /**
    * ��������� ���������� �� ����� �� ���������� �������
    * @var bool
    */
    public $hide_sort = false;

    public $can_edit = true;

    public $edit_popup = false;

    public $edit_state;

    public $can_delete = true;

    public $can_reorder = false;

    /**
    * ������ �������� � �����������
    * @var Mikron_Entity_Field[]|string[]
    */
    public $field_list;

    public $extra_fields;

    /**
    * ������ �� �������� �������������� ��������
    * @var string
    */
    public $edit_link;

    public $delete_state;

    /**
    * @var bool
    */
    public $reorder_state;

    /**
    * ������
    * @var Mikron_Type
    */
    public $filter;

    public $toolbar_numerator;

    public $item_menu;

    public $sub_select_list;

    /**
    * ������������� ���� � �������
    * 
    * @var string
    */
    public $template;
    public $form_id;

}