<?php

class Mikron_Entity_Designer {

    const DEF_ITEMS_PER_PAGE = Constant::DEF_ITEMS_PER_PAGE;
    const NO_VALUE = '<span class="font-no-data">нет данных</span>';

    public static $datetime_format = 'Y-m-d';

    private static $item;
    private static $list;
    private static $dir_name;
    private static $delete_file_state;
    private static $params;

    /**
    * @param string $entity_name
    * @param Mikron_Entity_Field[] $field_list
    * @param array $sub_select_list
    */
    public static function prepareFieldList($entity_name, $field_list, $sub_select_list = null) {
		$sub_select_list = (array)$sub_select_list;
		foreach ($field_list as $key => $field) {
            if ($field instanceof Mikron_Entity_Field) {
                $field_name = $field->name;
                if($field->type == 'virtual' || $field->link || in_array($field_name, $sub_select_list) || $field->format || $field->path) {
                    $field_list[$field_name] = $field;
                    unset($field_list[$key]);
                    continue;
                }
            } elseif (is_string($field)) {
                $field_name = $field;
            } else {
                throw new Exception(__CLASS__ . '.' . __FUNCTION__ . '.field_list undefined data type');
            }
            try {
                $field_meta = $entity_name::getFieldMeta($field_name);
            } catch (Exception $ex) {
                echo $entity_name, '.', $field_name, ': ', $ex->getMessage(), '<br>';
                dump($field_list);
            }
            $data = array(
                'description' => $field_meta['description'],
                'type' => $field_meta['type'],
                'path' => array_key_exists('path', $field_meta) ? $field_meta['path'] : null,
                'name' => $field_name,
                'link' => array_key_exists('link', $field_meta) ? $field_meta['link'] : null,
                'savelink' => array_key_exists('savelink', $field_meta) ? $field_meta['savelink'] : null,
                'readonly' => array_key_exists('readonly', $field_meta) ? $field_meta['readonly'] : null,
                'editor' => array_key_exists('editor', $field_meta) ? $field_meta['editor'] : null,
                'format' => array_key_exists('format', $field_meta) ? $field_meta['format'] : null,
                'aggr' => array_key_exists('aggr', $field_meta) ? $field_meta['aggr'] : null,
                'is_require' => array_key_exists('is_require', $field_meta) ? $field_meta['is_require'] : null,
            );
            if ($field instanceof Mikron_Entity_Field) {
                foreach($field as $k => $v) {
                    if(array_key_exists($k, $data) && $v) {
                        $data[$k] = $v;
                    }
                }
                $field_list[$field->name] = new Mikron_Entity_Field($data);
            } elseif (is_string($field)) {
                $field_list[$field_name] = new Mikron_Entity_Field($data);
            }
            unset($field_list[$key]);
        }
        return Mikron_Type::getFields($field_list);
    }

    /**
    * Draw HTML Form
    * 
    * @param mixed $item
    * @param string $form_id
    * @param Mikron_Entity_Designer_Form_Params $params
    * 
    * @return int Идентификатор HTML формы
    */
    public static function drawForm($item, $form_id = null, $params = null, $hidefilder = null) {
        $class_name = get_class($item);
        $params = $params ?: new Mikron_Entity_Designer_Form_Params();
        $params->id = $params->id ?: Mikron_Crud::getNextId();
        $params->state = $params->state ?: ($item->id ? 'update' : 'create');
        $state = new Mikron_Crud_State(array(
            'id' => $item->id ?: null,
            'action' => $params->state,
            'need_captcha' => false,
            'class_name' => $class_name,
            'entity' => $item,
            'read_only_values' => $params->read_only_values,
        ));
        $params->state = Mikron_Crypt::encrypt($state);
        self::$item = $item;               
        if($params->field_list) {
            $params->field_list = self::prepareFieldList($class_name, $params->field_list);
        }
		if(is_array($params->read_only_values) && is_array($params->field_list)) {
			foreach($params->field_list as $fieldname => &$fieldvalue_new) {
				if(in_array($fieldname, array_keys($params->read_only_values))) {
					$fieldvalue_new->readonly = true;
				}
			}
		}
        $table_name = Mikron_Entity_Model::getTableName($class_name);
        self::$dir_name = str_replace('_', '/', $table_name);
        if ($item->id) {
            $delete_file_state = new Mikron_Crud_State(array(
                'id' => $item->id,
                'action' => 'delete_file',
                'need_captcha' => false,
                'class_name' => $class_name,
                'entity' => null,
                'read_only_values' => $params->read_only_values,
            ));
            self::$delete_file_state = Mikron_Crypt::encrypt($delete_file_state);
        }
        self::$params = $params;
        include dirname(__FILE__).'/Designer/Template/form.phtml';
        return $params->id;
    }

    /**
    * Draw HTML table
    * 
    * @param string $entity_name
    * @param string $form_id
    * @param Mikron_Entity[] $item_list
    * @param Mikron_Entity_Designer_List_Params $params
    */
    static function drawList($entity_name, $form_id = null, $item_list = null, $params = null) {		
        // $entity_name = self::getTableName();
        // $class_name = $entity_name; // Mikron_Type::getClassName($entity_name);
        $params = $params ?: new Mikron_Entity_Designer_List_Params();
        $params->id = $params->id ?: Mikron_Crud::getNextId();
        $filter_class_name = $entity_name::getFilterType();
        $params->items_per_page = $params->items_per_page  ?: self::DEF_ITEMS_PER_PAGE;
        $form_id = $form_id ?: "{$entity_name}_form_list_10";        
        $filter = class_exists($filter_class_name) ? new Mikron_Filter($form_id, new $filter_class_name()) : null;
        if($params->filter) {
            foreach((array)$params->filter as $field_name => $value) {
                if($value !== null) {
                    $filter->$field_name = $value;
                }
            }
        }
		$table_name = Mikron_Entity_Model::getTableName($entity_name);
        // Сортировка
        $sort_field = $table_name.'.id';
        $sort_dir = 'desc';
        if(isset($entity_name::$sort)) {
            $sort = (object)$entity_name::$sort;
            $sort_field = $sort->field;
            $sort_dir = $sort->dir;
        }
        $sort = new Sort($form_id.'v8', $sort_field, $sort_dir, $is_mikron = true);
		$params->form_id = $form_id.'v8';
        if($params->can_delete) {
            $state = new Mikron_Crud_State(array(
                'action' => 'delete',
                'class_name' => $entity_name
            ));
            $params->delete_state = Mikron_Crypt::encrypt($state);
        }

        if($params->can_reorder) {
            $state = new Mikron_Crud_State(array(
                'action' => 'reorder',
                'class_name' => $entity_name
            ));
            $params->reorder_state = Mikron_Crypt::encrypt($state);
        }

        if($params->edit_popup) {
            $edit_state = new Mikron_Crud_State(array(
                // 'id' => $item->id ?: null,
                'action' => 'getForm',
                // 'need_captcha' => false,
                'class_name' => $entity_name,
                // 'entity' => $item,
                // 'read_only_values' => $params->read_only_values,
            ));
            $params->edit_state = Mikron_Crypt::encrypt($edit_state);
        }
        if($params->field_list) {
            $sub_select_list = is_array($params->sub_select_list) ? array_keys($params->sub_select_list) : array();
            $params->field_list = self::prepareFieldList($entity_name, $params->field_list, $sub_select_list);
        }
        $pg = new Mikron_Paginator('pg_'.strtolower($form_id), $params->items_per_page);
        /** @var Mikron_Type $entity_name */
        $resp = $entity_name::getList($entity_name, $filter, $sort, $pg, $params->field_list, $params->sub_select_list);
		$pg->setState($resp->paginator);

        $state = new Mikron_Crud_State(array(
            'action' => $params->action,
            'class_name' => $entity_name,
            'params' => $params,
        ));
        $params->state = Mikron_Crypt::encrypt($state);

        self::$list = new Mikron_Entity_List(array(
			'id' => 'form-'.Mikron_Crud::getNextId(),
            'items' => $resp->items,
            'paginator' => $pg,
            'params' => $params,
            'sort' => $sort,
            'filter' => $filter,
            'class_name' => $entity_name,
            'field_list' => $params->field_list
        ));
        $zend_mvc = Zend_Layout::getMvcInstance();
		if ($zend_mvc && ($view = $zend_mvc->getView())) {
	        //if($pg->getTotalPages() > 1) {
			if($params->toolbar_numerator){
	            $text = ($pg->getStartIndex() + 1).'&ndash;'.min([$pg->getStartIndex() + $pg->getItemsPerPage(), $pg->getRecordsCount()]).' из '.$pg->getRecordsCount();
				Mikron_Entity_Designer_Toolbar::addTag('span', array("class"=>"pull-right","style"=>"margin-top:5px"), $text);
	        }
        }
        if (!$params->template) $params->template = 'list.phtml';
        if ($params->template != 'list.phtml') {
            $white_space = '&nbsp;';
            $list = self::$list;
            $sub_select_list = $params->sub_select_list;
            $class_name = self::$list->class_name;
            $fields = self::$list->params->field_list ? : $class_name::getFields();
            $result = array();
            foreach ($list->items as $item) {
                $res_item = array();
                foreach ($fields as $field) {
                    if (!$field->hidden) {
                        $value = null;
                        if ($field->type == 'virtual' || strpos($field->path, '/')) {
                            if (strpos($field->path, '/')) {
                                $p = explode('/', $field->path);
                                $value = null;
                                $pc = count($p);
                                if ($pc > 1) {
                                    $tn = array_shift($p);
                                    $pc = count($p);
                                    $row = $item->$tn;
                                    foreach ($p as $_pc_index => $k) {
                                        if ($_pc_index >= $pc - 1) {
                                            break;
                                        }
                                        $row = is_object($row) ? $row->$k : null;
                                    }
                                    $value = $row ? ($row[array_pop($p)] ? : $white_space) : $white_space;
                                }
                            } else {
                                if (is_array($sub_select_list) && array_key_exists($field->name, $sub_select_list)) {
                                    $value = $item[$field->name] ? : $white_space;
                                }
                            }
                        } else {
                            if ($field->link) {
                                $link_class = $field->link;
                                $tsf = $link_class::getToStringFormat();
                                $tn = Mikron_Entity_Model::getTableName($link_class);
                                $row = $item->$tn;
                                $value = $row[$tsf] ? : $white_space;
                            } else {
                                $value = $item[$field->name] ? : $white_space;
                            }
                        }
                        $res_item[$field->name] = Mikron_Entity_Model::getItemFieldValue($field, $value, $item);
                    }
                }
                $result[] = $res_item;
            }
        }
        include dirname(__FILE__) . '/Designer/Template/' . $params->template;
		return self::$list->id;
    }
}