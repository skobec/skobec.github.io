<?php

class Sort extends Type_Sort {

    public function __construct($id, $default_field, $default_dir) {
        if(!isset($id)) {
            throw new Exception('Не задан идентификатор сортировки', 950);
        }
        if(!isset($default_field)) {
            throw new Exception('Не задано поле сортировки для «'.$id.'»', 950);
        }
        if(!isset($default_dir)) {
            throw new Exception('Не задано направление сортировки для «'.$id.'»', 950);
        }
        $default_dir = strtolower($default_dir);
        if(!in_array($default_dir, array('asc', 'desc'))) {
            throw new Exception('Задано неверное направление сортировки для «'.$id.'» ожидалось "asc" либо "desc"', 950);
        }
        parent::__construct(array(
            'field' => $default_field,
            'dir' => $default_dir,
        ));
        $ns = new Prodom_Session_Namespace("sort_{$id}_attributes");
        if(isset($_POST['sort'])) {
            $sort = $_POST['sort'];
        } elseif (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
        } elseif (isset($ns->sort)) {
            $sort = (object)$ns->sort;
            $this->field = $sort->field;
            $this->dir = $sort->dir;
        }
        if(isset($sort)) {
            if(is_array($sort) || is_object($sort)) {
                $sort = (array)$sort;
                $this->field = $sort['field'];
                $this->dir = $sort['dir'];
            } else {
                $this->field = $sort;
            }
        }
        $ns->sort = $this;
    }
    
    function getField() {
        return $this->field;
    }

    function getDirection() {
        return $this->dir;
    }

    function draw($field_name) {
        echo 'data-sort="'.$field_name.'"';
        if($field_name == $this->getField()) {
            echo ' data-sort-dir="'.$this->getDirection().'"';
        }
    }

}
