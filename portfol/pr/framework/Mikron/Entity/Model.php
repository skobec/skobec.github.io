<?php

class Mikron_Entity_Model {

    const DEF_ITEMS_PER_PAGE = 10;

    private static $list;
    private static $item;
    private static $response;

    // Деревянный кеш
    private static $cache = array();

    static function getDb() {
        return db_general::$db;
    }
    
    static function getDbDriverName() {
		return self::getConnectionAttribute(PDO::ATTR_DRIVER_NAME);
    }

    static function getDbServerInfo() {
        return self::getConnectionAttribute(PDO::ATTR_SERVER_INFO);
    }

    /**
    * @author sciner
    * @return PDO
    */
    static function getConnection() {
    	$nc = chr(0);
    	$fn = "{$nc}*{$nc}connection";
    	$a = self::getDb();
    	$a = (array)$a;
    	$con = $a[$fn];
    	return $con;
	}

    /**
    * @author sciner
    * @usage Mikron_Entity_Model::getConnectionAttribute(PDO::ATTR_DRIVER_NAME);
    * @param int $attribute_id PDO::ATTR_DRIVER_NAME
    */
    public static function getConnectionAttribute($attribute_id) {
    	$con = self::getConnection();
    	return $con->getAttribute($attribute_id);
	}

    public static function getTableName($entity_name) {
        if (property_exists($entity_name, 'table_name')) {
            return $entity_name::$table_name;
        } elseif(substr($entity_name, 0, 7) == 'Mikron_') {
        	return strtolower($entity_name);
        } else {
            return strtolower(substr($entity_name, 5));
        }
    }

    static function getItemFieldName($path) {
    }

    static function getItemFieldValue($field, $value, $item = null) {
        if($field->format !== null) {
            if(!$item) {
                throw new Exception('Mikron_Entity_Model::getItemFieldValue() error, $item is not object', 950);
            }
            $value = call_user_func($field->format, $item);
            if(!in_array($field->type, array('float'))) {
            	return $value;
			}
        }
        switch($field->type) {
            case 'datetime':
            case 'date': return mb_convert_case(Functions::dateFormat(strtotime($value), true, false, false), MB_CASE_LOWER, 'utf-8'); break;
            case 'float': $value = (float)$value; return number_format($value, 2, ',', ' '); break;
            default: return $value;
        }
    }

    /*
    Type_Billing_Bill_Spec::__set_state(array(   'id' => 5,   'number' => '3245',   'home_id' => 20051,  
     'count' => 1,   'name_bank' => 'tttttttttttttttttt',   'inn_bank' => NULL,   'kpp_bank' => NULL, 
       'bik_bank' => NULL,   'kor_bank' => NULL,   'fio' => NULL,   'min_payment' => NULL,   'open_date' => NULL,
          'close_date' => NULL,   'organization_id' => NULL,   'protocol' => NULL,   'dogovor' => NULL,))
    */

    static function getRowValueByPath($row, $field) {
        $path = explode('/', $field->path);
        $field_name = array_shift($path);
        if(count($path) == 0) {
            if(property_exists($field, 'format') && is_callable($field->format)) {
                $class_name = $field->link;
                $field_name = $field->name;
                $row = self::getById($class_name, $row->$field_name);
                return call_user_func($field->format, $row);
            }
            switch($field->type) {
                case 'datetime': return $row->$field_name ? mb_convert_case(Functions::dateFormat(strtotime($row->$field_name), true, false, false), MB_CASE_LOWER, 'utf-8') : null; break;
                case 'bool': return $row->$field_name ? 'checked="checked"' : ''; break;
                case 'float': return (float)$row->$field_name; break;
                default: return property_exists($row, $field_name) ? $row->$field_name : null;
            }
        } else {
            foreach($path as $f) {
                $class_name = $field->link;
                $field_name = $field->name;
                $row = self::getById($class_name, $row->$field_name);
                if(is_callable($field->format)) {
                    return call_user_func($field->format, $row);
                }
                return $row[$f];
            }
        }
    }

    private static function prepareFieldList($field_list) {
        $output = array();
        foreach($field_list as $field_name => $field) {
            if($field->path) {
                $output[$field->path] = $field;   
            } else {
                $output[$field_name] = $field;
            }
        }
        return $output;
    }

    public static function getById($entity_name, $id) {
        return self::get($entity_name, $id);
    }

    public static function getByFilter($entity_name, $filter) {
        return self::get($entity_name, null, $filter);
    }

    /**
    * put your comment there...
    * 
    * @param string $entity_name
    * @param mixed $filter_list
    * @param mixed $sort
    * @param mixed $paginator
    * @param mixed $field_list
    * @param mixed $sub_select_list
    * 
    * @return Mikron_Entity_List_Result
    */
    public static function getList($entity_name, $filter_list = null, $sort = null, $paginator = null, $field_list = null, $sub_select_list = null) {
        $table_name = Mikron_Entity_Model::getTableName($entity_name);
        /** @var \Zend_Db_Select $select */
        $select = self::getDb()->$table_name();
        // название PDO-драйвера
        $driver_name = self::getConnectionAttribute(PDO::ATTR_DRIVER_NAME);
        switch($driver_name) {
			case 'mysql': {
		        $sub_sel = array();
		        if(is_array($sub_select_list) && count($sub_select_list)) {
					// $sub_sel = array();
					foreach($sub_select_list as $alias => $value) {
						$sub_sel[] = "({$value}) as {$alias}";
					}
					$sub_sel[] = "{$table_name}.*";
					$select->select(implode(', ', $sub_sel));
		        }
				break;
			}
			default: {
				// Psql
				$sub_sel = array('count(*) OVER() as full_count');
				if(is_array($sub_select_list) && count($sub_select_list)) {
					foreach($sub_select_list as $alias => $value) {
						$sub_sel[] = "({$value}) as {$alias}";
					}
				}
				break;
			}
        }
        $sub_sel[] = "{$table_name}.*";
		$select->select(implode(', ', $sub_sel));		
        if(!$field_list) {
            $field_list = $entity_name::getFields();
        }
        $field_list = self::prepareFieldList($field_list);
        // Фильтрация
        if($filter_list) {
            foreach($filter_list as $field_name => $condition) {
                $t_field_name = str_replace('/', '.', $field_name);			
                if((is_scalar($condition) && ($condition !== '') && ($condition !== null)) || is_array($condition)) {
                    if(array_key_exists($field_name, $field_list)) {
	                    if(is_array($sub_select_list) && array_key_exists($field_name, $sub_select_list)) {
	                        $t_field_name = $sub_select_list[$field_name];
						} else {
							if(strpos($t_field_name, '.') === false) {
								$t_field_name = "{$table_name}.{$t_field_name}";
							}
						}
                        switch($field_list[$field_name]->type) {
                            case 'string': {								
	                            $condition2 = str_replace(' ', '%', $condition);
	                            $like_function = ($driver_name == 'pgsql') ? 'ILIKE' : 'LIKE';
                            	$select->where("TRIM(cast({$t_field_name} as char(500))) {$like_function} ?", "%{$condition2}%");
                            	break;
                            }
                            case 'datetime': {
                                if(strpos($condition, ' - ')) {
                                    $date_arr = explode(' - ', $condition);
                                    if(count($date_arr) > 1) {
                                        $select->where("DATE({$t_field_name}) >= ? and DATE({$t_field_name}) <= ?", date('Y-m-d', strtotime(str_replace('/','-',$date_arr[0]))), date('Y-m-d', strtotime(str_replace('/','-',$date_arr[1])))); 
                                    } else {
                                        $select->where("DATE({$t_field_name}) = ?", date('Y-m-d', strtotime($date_arr[0]))); 
                                    }
                                } else {
                                    $select->where("DATE({$t_field_name}) = ?", date('Y-m-d', strtotime($condition))); 
                                }
                                break;
                            }
                            case 'float': {
								$select->where($t_field_name, (float)str_replace(',', '.', trim($condition)));
								break;
                            }
                            case 'int':								
								$select->where("({$t_field_name})", $condition);
                            	break;
                            default: {
                            	$select->where($t_field_name, $condition);
                            	break;
                            }
                        }
					} else {
						if(is_array($condition)) {
							if($condition = array_filter($condition)) {
								$select->where($t_field_name, $condition);
							}
						} else {
                        	$select->where($t_field_name, $condition);
                        }
                    }
                } elseif (!is_scalar($condition)) {
                    if(is_object($condition)) {
                        if($condition instanceof Mikron_Entity_Filter) {
							if(isset($sub_select_list[$field_name])) {
								$t_field_name = $sub_select_list[$field_name];
							}
							$condition->apply($select, $t_field_name);
                        }
					}
                }
            }
        }
        // Сортировка
        if($sort) {
            $sort = (object)$sort;
	        $sort_field = str_replace('/', '.', $sort->field);
			$select->order($sort_field.' '.$sort->dir);
        }
        // Постраничный вывод
        if($paginator) {
        	$select->limit($paginator->getItemsPerPage() , $paginator->getStartIndex());
        }
        // die(trim($select));
        return new Mikron_Entity_List_Result(array(
            'items' => $select,
            'paginator' => ($paginator) ? $paginator->getCalculated($select) : null,
        ));
    }

    /**
    * Create entity
    * 
    * @param string $class_name
    * @param mixed $form
    * 
    * @return int
    */
    public static function create($class_name, $form) {
        $form = (array)$form;
        unset($form['id']);
        $table_name = Mikron_Entity_Model::getTableName($class_name);
        $field_list = $class_name::getFields();
        foreach($form as $key => $value) {
            if(strpos($key, '_') === 0) {
                continue;
            }
            if(!is_null($value) && $value !== '') {
                $form[$key] = Mikron_Functions::cast($value, $field_list[$key]->type);
            } else {
                unset($form[$key]);
            }
        }
        if($id = Mikron_Crud::raiseTrigger(Mikron_Crud::TRIGGER_PRE_CREATE, $class_name, $form)) {
            if(is_numeric($id)) {
                return $id;   
            } else {
                throw new Exception('Not correct TRIGGER_PRE_CREATE usage');
            }
        } else {
	        if($validate_result = $class_name::validate((object)$form)) {
				throw new Exception(json_encode($validate_result, JSON_UNESCAPED_UNICODE), 951);
	        }
	        $db = self::getDb();
	        $make_transaction = !self::getConnection()->inTransaction();
	        if($make_transaction) {
	        	$db->transaction = 'BEGIN';
			}
	        try {
	            $resp = $db->$table_name()->insert($form);
	            self::$response->data['id'] = $resp['id'];
	            $id = self::$response->data['id'];
	            $form['id'] = $id;
	            Mikron_Crud::raiseTrigger(Mikron_Crud::TRIGGER_CREATE, $class_name, $form);
	            if($make_transaction) {
	            	$db->transaction = 'COMMIT';
				}
	            return $id;
			} catch(Exception $ex) {
				if($make_transaction) {
					$db->transaction = 'ROLLBACK';
				}
				throw $ex;
			}
        }
    }

    /**
    * put your comment there...
    * 
    * @param string $class_name Type_*
    * @param int $id
    * @param mixed $form
    * @param bool $ignore_file_field
    * 
    * @return int
    */
    public static function update($class_name, $id, $form, $ignore_file_field = true) {
        $form = (array)$form;
        $field_list = $class_name::getFields();
        foreach($form as $key => $value) {
        	if(!array_key_exists($key, $field_list)) {
        		unset($form[$key]);
				continue;
        	}
            if(strpos($key, '_') === 0) {
                continue;
            }
            if(!is_null($value) && $value != '') {
                $form[$key] = Mikron_Functions::cast($value, $field_list[$key]->type);
            } else {
                $form[$key] = null;
            }
            if(array_key_exists($key, $field_list)) {
                $field = $field_list[$key];
                switch($field->type) {
                    case 'file':
                    case 'file[]':
                    case 'image': {
                        if($ignore_file_field) {
                            unset($form[$key]);
                        }
                        break;
                    }
                }
            }
        }
        $form['id'] = $id;
        $table_name = Mikron_Entity_Model::getTableName($class_name);
		if($ignore_file_field) {
			Mikron_Crud::raiseTrigger(Mikron_Crud::TRIGGER_PRE_UPDATE, $class_name, $form);
		}
	    if($validate_result = $class_name::validate((object)$form, false)) {
			throw new Exception(json_encode($validate_result, JSON_UNESCAPED_UNICODE), 951);
	    }
	    $db = self::getDb();
	    $make_transaction = !self::getConnection()->inTransaction();
	    if($make_transaction) {
	        $db->transaction = 'BEGIN';
		}
	    try {
		    $resp = $db->$table_name()->where('id', $id)->update($form);
			if($ignore_file_field) {
				Mikron_Crud::raiseTrigger(Mikron_Crud::TRIGGER_UPDATE, $class_name, $form);
			}
	        if($make_transaction) {
	            $db->transaction = 'COMMIT';
			}
	        return $id;
		} catch(Exception $ex) {
			if($make_transaction) {
				$db->transaction = 'ROLLBACK';
			}
			throw $ex;
		}
        return $resp;
    }

    /**
    * @author sciner
    * 
    * @param string $class_name
    * @param int $id
    * 
    * @return object
    */
    public static function get($class_name, $id, Mikron_Type $filter = null) {
        $table_name = Mikron_Entity_Model::getTableName($class_name);
        $c = md5($class_name.$id.var_export($filter, 1));
        if(!array_key_exists($c, self::$cache)) {
        	$select = self::getDb()->$table_name();
        	if(is_numeric($id)) {
        		$select->where('id', $id);
        	} elseif(is_object($filter) && $filter instanceof Mikron_Type) {
				foreach($filter::getFields() as $field) {
					$field_name = $field->name;
					if(!is_null($filter->$field_name)) {
						$select->where($field_name, $filter->$field_name);
					}
				}
        	} else {
				return self::$cache[$c] = null;
        	}
            $item = $select->fetch();
            if($item) {
                self::$cache[$c] = $item->jsonSerialize();
            }
        }
        if(array_key_exists($c, self::$cache)) {
            return self::$cache[$c];
        }
    }

    public static function delete($class_name, $id) {
	    $db = self::getDb();
	    $make_transaction = !self::getConnection()->inTransaction();
	    if($make_transaction) {
	        $db->transaction = 'BEGIN';
		}
	    try {
			$table_name = Mikron_Entity_Model::getTableName($class_name);
			Mikron_Crud::raiseTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, $class_name, $id);
			$resp = self::getDb()->$table_name()->where('id', $id)->delete();
			Mikron_Crud::raiseTrigger(Mikron_Crud::TRIGGER_DELETE, $class_name, $id);
	        if($make_transaction) {
	            $db->transaction = 'COMMIT';
			}
	        return $resp;
		} catch(Exception $ex) {
			if($make_transaction) {
				$db->transaction = 'ROLLBACK';
			}
			throw $ex;
		}
    }

    /**
    * Перетаскивание
    * @author sciner
    * @since 2014-07-24
    * 
    * @param string $class_name
    * @param int[] $id_list
    * 
    * @return bool
    */
    public static function reorder($class_name, $id_list) {
        $table_name = Mikron_Entity_Model::getTableName($class_name);
        foreach($id_list as $index => $id) {
            $resp = self::getDb()->$table_name()->where('id = ?', $id)->update(array('mikron_order' => $index));
        }
        return true;
    }

    public static function delete_file($class_name, $id, $form) {
    	$filename = $form['filename'];
    	$code = $form['code'];
        $item = Mikron_Entity_Model::get($class_name, $id);
        $item = Mikron_Functions::cast($item, $class_name);
        $files = explode('|', $item->$code);
        $title = explode('/', $filename);
        $title = array_pop($title);
        foreach($files as $index => $file) {
            if($file == $title) {
                unset($files[$index]);
            }
        }
        $form[$form['code']] = (!count($files) ? null : implode('|', $files));
        unset($form['filename']);
        unset($form['code']);
        $result = Mikron_Entity_Model::update($class_name, $id, $form, false);
        if($result) {
        	$path = Mikron_Crud::getUploadDirectory().$filename;
        	if(file_exists($path)) {
        		unlink($path);
        	}
        	$hash = substr(md5($filename), 0, 5);
            echo json_encode(array('status' => 'success', 'message' => "Файл {$filename} успешно удален", 'hash' => $hash));
            exit;
        } else {
        	throw new Exception("Файл {$filename} не удален", 950);
        }
    }

	/**
	* Массовая вставка записей в таблицу
	* @author Tagir Zinnurov
	* @since 2015-01-27
	* 
	* @param mixed $db
	* @param string $table_name
	* @param mixed $list
	* 
	* @return bool
	*/
	static function massInsert($db, $table_name, $list) {
		$field_list = array();
		$values = array();
		$value_string = null;
		$qry = null;
		foreach($list as $item) {
			$item = (array)$item;
			// id - autoincrement value
			if (array_key_exists('id', $item)) {unset($item['id']);}
			if(!$field_list) {
				$field_list = array_keys($item);
				$v = array();
				foreach($field_list as $i => $field) {
					$field_list[$i] = $db->quoteIdentifier($field);
					$v[] = '?';
				}
				$value_string = implode(',', $v);
				$field_list = implode(',', $field_list);
			}
			$qry .= "({$value_string}), ";
			foreach($item as $item_field) {
				$values[] = $item_field;
			}
		}
		if(!$field_list) {
			return false;
		}
		$query = 'INSERT INTO ' . $db->quoteIdentifier($table_name) . ' ('.$field_list.') VALUES '.trim($qry, ', ');
		// dumpf('mass_insert_'.time().'.sql', $query);
		$stmt2 = $db->prepare($query);
		$stmt2->execute($values);
		return true;
	}

	/**
	 * Массовый апдейт записей
	 * @author sharafanmaxim78
	 * @since 2015-07-06
	 *
	 * @param mixed $db
	 * @param string $table_name Наименование таблицы, в которой производить апдейт
	 * @param int[] $key_list Массив ID, которые будут затронуты апдейтом
	 * @param array|Mikron_Type $update_array Массив пар ключ => значение, либо объект, которые будут установлены в таблице
	 *
	 * @return int Количество затронутых записей.
	 * @throws Exception
	 */
	static function massUpdate($db, $table_name, $key_list, $update_array) {
		if(empty($key_list)) {
			return 0;
		}
		$update_array = (array) $update_array;
		$counter = 0;
		foreach($update_array as $key => $value) {
			// Убираем все NULL элементы
			if($value === null) {
				unset($update_array[$key]);
			}
			// Если передан 'null' как строка, заменим его настоящим null
			if(strtolower($value) == 'null') {
				$update_array[$key] = new Zend_Db_Expr('NULL');
			}
		}
		$db->beginTransaction();
		try {
			$counter = $db->update($table_name, $update_array, array('id in (?)' => $key_list));
			$db->commit();
		} catch(Exception $ex) {
			$db->rollBack();
			throw $ex;
		}
		return $counter;
	}

	/**
	 * Возвращает массив с данными, аналогичные тем, по которым Микрон строит списки.
	 * @author aantonov, sharafanmaxim78
	 * @since 2015-07-29
	 *
	 * @param string $entity_name	Тип сущности
	 * @param string $edit_link		Строка вызова формы для редактирования
	 * @param mixed $filter_list	Объект фильтра
	 * @param mixed $sort			Объект сортировщика
	 * @param mixed $paginator	Объект пагинатора
	 * @param mixed $field_list Список полей
	 * @param Mikron_Entity_Designer_List_Params $list_params Параметры списка
	 *
	 * @return array Массив с данными
	 */
	public static function getData($entity_name, $edit_link, $filter_list = null, $sort = null, $paginator = null, $field_list = null, $list_params = null) {
		// Формируем фильтр
		$form_id = md5($entity_name . $edit_link . strtok($_SERVER['REQUEST_URI'], '/?'));
		$filter_class_name = $entity_name::getFilterType();
		$filter = class_exists($filter_class_name) ? new Mikron_Filter($form_id, new $filter_class_name()) : null;
		// Формируем сортировщик
		$table_name = Mikron_Entity_Model::getTableName($entity_name);
		// Сортировка
		$sort_field = $table_name . '.id';
		$sort_dir = 'desc';
		if(isset($entity_name::$sort)) {
			$sort = (object) $entity_name::$sort;
			$sort_field = $sort->field;
			$sort_dir = $sort->dir;
		}
		$sort = new Sort($form_id . 'v8', $sort_field, $sort_dir, $is_mikron = true);
		// Формируем подзапросы
		$sub_select_list = empty($list_params->sub_select_list) ? array() : $list_params->sub_select_list;
		$sub_select_list_keys = is_array($sub_select_list) ? array_keys($sub_select_list) : array();
		// Предварительная подготовка полей
		$fields = Mikron_Entity_Designer::prepareFieldList($entity_name, $field_list, $sub_select_list_keys);
		// Формируем основной SQL запрос
		$list = self::getList($entity_name, $filter, $sort, null, $fields, $sub_select_list);
		// Постподготовка полученных данных
		$white_space = '';
		$completeList = array();
		foreach($list->items as $item) {
			$completeRow = array();
			foreach($fields as $field) {
				if(!$field->hidden) {
					$value = null;
					if($field->type == 'virtual' || strpos($field->path, '/')) {
						if(strpos($field->path, '/')) {
							$p = explode('/', $field->path);
							$value = null;
							$pc = count($p);
							if($pc > 1) {
								$tn = array_shift($p);
								$pc = count($p);
								$row = $item->$tn;
								foreach($p as $_pc_index => $k) {
									if($_pc_index >= $pc - 1) {
										break;
									}
									$row = is_object($row) ? $row->$k : null;
								}
								$value = $row ? ($row[array_pop($p)] ? : $white_space) : $white_space;
							}
						} else {
							if(is_array($sub_select_list) && array_key_exists($field->name, $sub_select_list)) {
								$value = $item[$field->name] ? : $white_space;
							}
						}
					} else {
						if($field->link) {
							$link_class = $field->link;
							$tsf = $link_class::getToStringFormat();
							$tn = Mikron_Entity_Model::getTableName($link_class);
							$row = $item->$tn;
							$value = ($row[$tsf] !== null) ? $row[$tsf] : $white_space;
						} else {
							$value = ($item[$field->name] !== null) ? $item[$field->name] : $white_space;
						}
					}
					$resultStructure = new stdClass();
					$resultStructure->name = $field->name;
					$resultStructure->description = $field->description;
					$resultStructure->type = $field->type;
					$resultStructure->value = Mikron_Entity_Model::getItemFieldValue($field, $value, $item);
					$completeRow[] = $resultStructure;
				}
			}
			$completeList[] = $completeRow;
		}
		return $completeList;
	}

}