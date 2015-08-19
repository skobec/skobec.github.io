<?php

class Filter {

    // private $filter = null;

    public function __construct($id, $filter) {
        if(!isset($filter)) {
            throw new Exception('Не задан объект фильтра', 950);
        }
        foreach($filter as $key => $value) {
            $this->$key = trim($value) ?: null;
        }
        $ns = new Prodom_Session_Namespace("filter_{$id}_attributes");
        if(isset($_POST['filter'])) {
            $new_filter = $_POST['filter'];
        } elseif (isset($_GET['filter'])) {
            $new_filter = (array)$_GET['filter'];
        } elseif (isset($ns->filter)) {
            $new_filter = (array)$ns->filter;
        } else {
            $new_filter = (array)$filter;
        }
        $ns->filter = $new_filter;
        foreach($new_filter as $key => $value) {
            $this->$key = trim($value) ?: null;
        }
    }

    function getFilter($field_name) {
        if(isset($this->filter)) {
            if(array_key_exists($field_name, $this->filter)) {
                return $this->filter[$field_name];
            }
        }
    }

    function draw($field_name, $id = null, $class = null) {
        $value = isset($this->$field_name) ? htmlspecialchars($this->$field_name) : null;
        if($id) {
            $id = 'id="'.$id.'"';
        }
        echo <<<html
        <input type="text" name="filter[{$field_name}]" class="elastik {$class}" value="{$value}" {$id} />
html;
    }

    function drawCheck($field_name, $id = null, $class = null) {
        $value = isset($this->$field_name) ? htmlspecialchars($this->$field_name) : null;
        if($id) {
            $id = 'id="'.$id.'"';
        }
        $flag=strlen($value)>0?"checked":"";
        echo <<<html
        <input type="checkbox" name="filter[{$field_name}]" class="needevent {$class}" $flag  />
html;
    }

    function drawSelect($field_name, $options = array(), $id = null, $class = null)
    {
        $value = isset($this->$field_name) ? htmlspecialchars($this->$field_name) : null;
        $id = $id ? 'id="' . $id . '"' : null;
        $html = '<option value></option>';
        foreach ($options as $option) {
            $html .= '<option value="' . $option->id . '"' . ($option->id == $value ? ' selected' : '') . '>' . $option->title . '</option>';
        }
        return '<select name="filter[test_' . $field_name . ']" class="needevent filterSelect ' . $class . '" ' . $id . '>' . $html . '</select>';
    }
}
