<?php

/**
* Базовый объект
*/
class Mikron_Type {

	const ENTITY_PREFIX = 'Type_';

	/**
	* @var Mikron_Validator
	*/
	private static $validator;
	
    static function getDb() {
        return db_general::$db;
    }

	/**
	* @return string
	*/
	static function getTypesDir() {
		$types_dir = realpath(dirname(__FILE__).'/../../library');
		$types_dir = str_replace('\\', '/', $types_dir);
		return $types_dir;
	}

	/**
	* Возвращает путь к файлу с классом сущности
	* 
	* @param string $entity_name
	* 
	* @return string
	*/
    public static function getFilePath($entity_name) {
    	$types_dir = self::getTypesDir();
    	return $types_dir.'/'.str_replace('_', '/', $entity_name).'.php';
	}

    public static function getMeta() {
        $typeName = get_called_class();
        $docComment = Mikron_Reflection::getDataTypeDoc($typeName);
        $elem = array_shift($docComment);
        return $elem;
    }

    public static function getFilterType() {
        $typeName = get_called_class();
        return $typeName;
    }

    public static function getList($entity_name, $filter_list = null, $sort = null, $paginator = null, $field_list = null, $sub_select_list  = null) {
        return Mikron_Entity_Model::getList($entity_name, $filter_list, $sort, $paginator, $field_list, $sub_select_list);
    }

    public function __construct($input = null, $filter = null) {
	    $state = array();
	    if(is_null($input)) {
	        if(!is_null($filter)) {
	            $list = Mikron_Entity_Model::getList(get_class($this), $filter);
	            $s = $list->items->fetch();
	            if($s) {
	                $state = $s;
	            }
	        }
	    } elseif(is_numeric($input)) {
	        $state = Mikron_Entity_Model::get(get_class($this), $input);
	        if(!$state) {
				throw new Exception('Объект не найден', 950);
	        }
	        $state = (array)$state;
	    } elseif(is_array($input)) {
	        $state = $input;
	    } else {
	        throw new Exception('Ошибка создания объекта', 950);
	    }
	    $fields = get_object_vars($this);
	    foreach($state as $name => $value) {
	        if(array_key_exists($name, $fields)) {
	            $this->$name = $value;
	        }
	    }
	    $this->__init();
    }

    public static function getToStringFormat() {
        $meta = self::getMeta();
        $tostring = 'title';
        foreach($meta as $path => $field) {
            $tostring = array_key_exists('tostring', $field) ? $path : $tostring;
        }
        return $tostring;
    }

    public static function getFieldMeta($field_name) {
        $meta = self::getMeta();
        foreach($meta as $path => $field) {
            if($path == $field_name) {
                return $field;
            }
        }
        // file_put_contents(dirname(__FILE__).'/st.txt', var_export(debug_backtrace(null, 10), 1));
        throw new Exception("Mikron_Type::getFieldMeta() Invalid \$field_name: '{$field_name}'");
    }

    public static function getMikronEntityType($entity_name, $field_name) {
        $mikron_entity = Mikron_Entity_Model::getByFilter('Mikron_Entity', new Mikron_Entity(array('code' => str_replace('Type_', '', $entity_name))));
        if($mikron_entity) {
            $select = self::getDb()->mikron_entity_field();
            $filter = new Mikron_Entity_Field(array('mikron_entity_id' => $mikron_entity['id'], 'name' => $field_name));
            foreach($filter::getFields() as $field) {
                    $field_name = $field->name;
                    if(!is_null($filter->$field_name)) {
                            $select->where($field_name, $filter->$field_name);
                    }
            }
            $item = $select->fetch();
            if($item) {
                $item = $item->jsonSerialize();
                return new Mikron_Entity_Type($item['mikron_entity_type_id']);
            }
        }
        throw new Exception("Mikron_Type::getMikronEntityType() Invalid \$field_name: '{$field_name}'");
    }

    public static function getFields($elem = null) {
        $typeName = null;
        if(!$elem) {
            $typeName = get_called_class();
            $docComment = Mikron_Reflection::getDataTypeDoc($typeName);
            $elem = array_shift($docComment);
        }
        $field_list = array();
        foreach($elem as $path => $field) {
            $field = (array)$field;
            if(array_key_exists('link', $field) && $field['link']) {
                // 'link' => array_key_exists('link', $field) ? $field['link'] : null,
                $link_class = $field['link'];
                $field_name = $link_class::getToStringFormat();
                $field_meta = $link_class::getFieldMeta($field_name);
                $field_list[$path] = new Mikron_Entity_Field(array(
                    'name' => $path,
                    'description' => $field['description'],
                    'is_require' => array_key_exists('is_require', $field) && $field['is_require'] ? true : false,
                    'path' => array_key_exists('path', $field) && $field['path'] ? $field['path'] : Mikron_Entity_Model::getTableName($link_class).'/'.$field_name,
					'query' => array_key_exists('query', $field) && is_callable($field['query']) ? $field['query'] : $path,
                    'type' => $field_meta['type'],
                    'hidden' => array_key_exists('hidden', $field) && $field['hidden'] ? true : false,
                    'readonly' => array_key_exists('readonly', $field) && $field['readonly'] ? true : false,
                    'link' => $field['link'],
					'savelink' => array_key_exists('savelink', $field) ? $field['savelink'] : null,
                    'editor' => array_key_exists('editor', $field) ? $field['editor'] : null,
                    'filter' => array_key_exists('filter', $field) ? $field['filter'] : null,
                    'format' => array_key_exists('format', $field) ? $field['format'] : null,
                    'aggr' => array_key_exists('aggr', $field) ? $field['aggr'] : null,
                    'validate' => array_key_exists('validate', $field) ? $field['validate'] : null,
                    'editor_setting' => array_key_exists('editor_setting', $field) ? $field['editor_setting'] : null,
                    'id' => array_key_exists('id', $field) ? $field['id'] : null,
                ));
            } else {
                $field_list[$path] = new Mikron_Entity_Field(array(
                    'name' => $path,
                    'description' => $field['description'],
                    'is_require' => array_key_exists('is_require', $field) && $field['is_require'] ? true : false,
                    'path' => array_key_exists('path', $field) && $field['path'] ? $field['path'] : $path,
                    'type' => $field['type'],
                    'hidden' => array_key_exists('hidden', $field) && $field['hidden'] ? true : false,
                    'readonly' => array_key_exists('readonly', $field) && $field['readonly'] ? true : false,
                    'link' => '',
                    'editor' => array_key_exists('editor', $field) ? $field['editor'] : null,
                    'filter' => array_key_exists('filter', $field) ? $field['filter'] : null,
					'format' => array_key_exists('format', $field) ? $field['format'] : null,
					'validate' => array_key_exists('validate', $field) ? $field['validate'] : null,
                    'editor_setting' => array_key_exists('editor_setting', $field) ? $field['editor_setting'] : null,
                    'id' => array_key_exists('id', $field) ? $field['id'] : null,
                ));
            }
        }
        // return json_decode(json_encode(array_shift($docComment)));
        return $field_list;
    }

    public static function prepare() {
    }

    public function __set($field, $value) {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        throw new Exception("Ошибка записи атрибута ".get_class($this)."::{$field}, атрибут не существует. Вызов произошел в {$caller['file']}:{$caller['line']}.");
    }

    public function __get($field) {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        throw new Exception("Ошибка чтения атрибута ".get_class($this)."::{$field}, атрибут не существует. Вызов произошел в {$caller['file']}:{$caller['line']}.");
    }
    
    public function __init() {
        return true;
    }

    /**
    * Валидирование объекта по всем правилам
    * @author sciner
    * @since 2015-02-17
    * 
    * @param Mikron_Type $object
    * @param bool $validate_all_fields
    * 
    * @return string[]
    */
	static function validate($object, $validate_all_fields = true) {
		$response = array();
		$item = clone $object;
		$original_field_list = array_keys((array)$item);
		$class_name = get_called_class();
		$item = Functions::cast($item, $class_name);
		$field_list = $class_name::getFields();
		foreach($field_list as $field_name => $meta) {
			if(!$validate_all_fields && !in_array($field_name, $original_field_list)) {
				continue;
			}
			// обязательность заполнения поля
			if($meta->is_require) {
				if(property_exists($item, $field_name)) {
					if($item->$field_name === null || $item->$field_name === '') {
						$response[$field_name] = null;	
					}
				} else {
					$response[$field_name] = null;
				}
			}
			// список валидаторов из описания поля объекта
			if($meta->validate) {
				try {
					if(!self::$validator) {
						self::$validator = new Mikron_Validate;
					}
					// список правил валидации для данного поля
					$rule_list = self::$validator->parseValidateAnnotation($meta->validate);
					if(!count($rule_list)) {
						continue;
					}
					if(!property_exists($item, $field_name)) {
						$response[$field_name] = null;
						continue;
					}
					// если поле не заполнено, то его не нужно валидировать
					if($item->$field_name !== null && $item->$field_name !== '') {
						// перебор правил
						foreach($rule_list as $rule) {
							// имя валидатора
							$validator_class_name = Mikron_Validate::getValidatorClassName($rule->name);
							if($message = $validator_class_name::validate($item->$field_name, (array)$rule->argument_list)) {
								$response[$field_name] = $message;
							}
						}
					}
				} catch(Exception $ex) {
					throw new Exception($ex->getMessage()."<br>Поле: {$class_name}::{$field_name}.", 950);
				}
			}
		}
		return $response;
	}

}
