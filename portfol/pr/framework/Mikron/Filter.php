<?php

class Mikron_Filter {

    public function __construct($id, $filter) {
        if(!isset($filter)) {
            throw new Exception('Не задан объект фильтра', 950);
        }
        foreach($filter as $key => $value) {
            $this->$key = trim($value) ?: null;
        }
        $ns = new Mikron_Session_Namespace("filter_{$id}_attributes");
        if(isset($_POST['filter'])) {
            $new_filter = (array)$_POST['filter'];
        } elseif (isset($_GET['filter'])) {
            $new_filter = (array)$_GET['filter'];
        } elseif (isset($ns->filter)) {
            $new_filter = (array)$ns->filter;
        } else {
            $new_filter = (array)$filter;
        }
        $ns->filter = $new_filter;
        foreach($new_filter as $key => $value) {
        	if(is_array($value)) {
				$this->$key = count($value) ? $value : null;
        	} else {
            	$this->$key = trim($value) !== '' ? trim($value) : null;
			}
        }
    }

    function getFilter($field_name) {
        if($this->filter) {
            if(array_key_exists($field_name, $this->filter)) {
                return $this->filter[$field_name];
            }
        }
    }
    
    function draw($field_name, $hidden = false, $class = null, $id = null) {
        $value = isset($this->$field_name) ? $this->$field_name : null;
        $type = $hidden ? 'type="hidden"' : null;
        $class = $class ? " {$class}" : null;
        $id = $id ? " id=\"{$id}\"" : null;
        if(is_array($value)) {
			foreach($value as $v) {
				$v = (int)$v;
        		echo <<<html
        			<input {$type} {$id} name="filter[{$field_name}][]" type="checkbox" value="{$v}" checked="checked" />
html;
			}
        } else {
        	$value = htmlspecialchars($value);
        	echo <<<html
        		<input {$type} {$id} name="filter[{$field_name}]" type="text" class="elastik{$class}" value="{$value}" />
html;
		}
    }

}
