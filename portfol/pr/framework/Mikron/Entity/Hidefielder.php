<?php

/*
 * 2014-09-04 sharafanmaxim78
 * Возникают ситуации, когда необходимо скрыть некоторые поля в форме 
 * при определенном значении другого поля.
 * Инициализируйте этот класс и передавайте его 6м параметром в
 * App_Billing:: editForm ($type, $id, $form_field_list = null, $not_save = null, $read_only_values = null, ВОТ_СЮДА= null)
 */

class Mikron_Entity_Hidefielder {

    private $_this_field;
    private $_equqal_field;
    private $_hide_fields = array();

    /**
     * 
     * @param string $this_field      При условии, что это поле
     * @param string $equqal_field   равно этому значению
     * @param array $hide_fields     скрыть эти поля
     */
    function __construct($this_field, $equqal_field, $hide_fields) {
        $this->_this_field = $this_field;
        $this->_equqal_field = $equqal_field;
        $this->_hide_fields = $hide_fields;
    }

    
    /**
     * Если найдено поле с необходимым значением,
     * значит надо скрыть назначенные для скрытия колонки.
     * @param type $item            значения сущности
     * @param type $fields          поля сущности
     * @return модифицированный     список $fields
     */
    public function hideFields($item, $fields) {
        $_flag_need_hide = false;

        foreach($fields as $field) {

            if($field->name == $this->_this_field) {
                $field->hidden = true;
                $vars = get_object_vars($item);
                $value = $vars[$this->_this_field];

                if((string) $this->_equqal_field == (string) $value) {
                    $_flag_need_hide = true;
                }
            }
        }
        
        
        if($_flag_need_hide){
             foreach($this->_hide_fields as $hide_fld){                 
                 $fields[$hide_fld]->hidden=true;                             
             }
        }

        return $fields;
    }

    
}
