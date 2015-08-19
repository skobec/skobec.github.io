<?php

class Mikron_Entity_Generator {

    /**
    * Создание таблицы
    * 
    * @param string $table_name
    * @param mixed $fields
    */
    public static function createTable($table_name, $fields = null) {
        $driver_name = Mikron_Entity_Model::getDbDriverName();
        $schema = 'public';
        $field_declaration = null;
		if(count($fields)) {
		    foreach($fields as $field) {
		        $type = self::getFieldDbType($field->type);
		        $field_declaration .= ", \"{$field->code}\" {$type} NULL\n";
		    }
		}
        if($driver_name == 'pgsql') {
	        $query = "CREATE TABLE {$schema}.{$table_name} (id serial NOT NULL {$field_declaration}, mikron_order int4 not null default 0, PRIMARY KEY (id)) WITH (OIDS=FALSE);";
        } elseif($driver_name == 'mysql') {
	        $query = "CREATE TABLE {$schema}.{$table_name} (id int AUTO_INCREMENT NOT NULL {$field_declaration}, mikron_order int NOT NULL DEFAULT 0, PRIMARY KEY (id));";
        } else {
			throw new Exception('Not supported PDO driver', 950);
        }
        $stmt = Mikron_Entity_Model::getConnection()->query($query);
        return true;
    }

    static function getFieldDbType($type_name) {
		$driver_name = Mikron_Entity_Model::getDbDriverName();
    }

    /**
    * Добавление полей в таблицу
    * 
    * @param string $table_name
    * @param array $fields
    */
    public static function addTableField($table_name, $field) {
        $db_general = Mikron_Entity_Model::getDb();
        $field = (object)$field;
        $entity_type = new Mikron_Entity_Type(null, Mikron_Functions::cast(array('id' => $field->entity_type_id), 'Mikron_Entity_Type'));
        $type = $entity_type->mikron_entity_id ? 'int' : $entity_type->title;
        $query = "ALTER TABLE {$table_name} DROP COLUMN /*IF EXISTS*/ {$field->code};";
        echo $query;exit;
        $db_general->query($query, array());
        $query = "ALTER TABLE {$table_name} ADD COLUMN {$field->code} {$type};";
        $stmt = $db_general->query($query, array());
        if($entity_type->mikron_entity_id) {
            $rand = rand();
            $parent_table = str_replace('type_', '', mb_strtolower($entity_type->title));
            $query = "ALTER TABLE `{$table_name}` ADD CONSTRAINT `FK_{$parent_table}{$rand}` FOREIGN KEY (`{$parent_table}_id`) REFERENCES `{$parent_table}`(`id`) ON DELETE CASCADE ON UPDATE CASCADE";
            $stmt = $db_general->query($query, array());
        }
        return true;
    }
    
    /**
    * Запись полей в файл
    * 
    * @param string $dir
    * @param int $entity_id
    * @param string $filename
    */
    private static function create_file($entity_id, $filename) {
        $table_list = null;
        $table_list = Mikron_Type::getList('Mikron_Entity_Field', array('mikron_entity_id' => $entity_id));
        $entity = Mikron_Entity_Model::getById('Mikron_Entity', $entity_id);
        if(!$entity) {
            throw new Exception('Ошибка создания файла');
        }
        $entity = (object)$entity;

        // Создание папки
        $dir = explode('/', $filename);
        array_pop($dir);
        $dir = implode('/', $dir);
        if(!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $entity_prefix = Mikron_Type::ENTITY_PREFIX;
        $file = "<?php

/**
* {$entity->title}
*/
class {$entity_prefix}{$entity->code} {
";
        if($table_list) {
            foreach($table_list->items as $table) {
                $table = (object)$table->jsonSerialize();
                if($table->is_deleted) continue;
                $file .= "\n/**\n";
                $file .= "* {$table->description}\n";
                $entity_type = new Mikron_Entity_Type(null, Mikron_Functions::cast(array('id' => $table->mikron_entity_type_id), 'Mikron_Entity_Type'));
                $file .= "* @".($entity_type->mikron_entity_id ? 'link' : 'var')." {$entity_type->title}\n";
                if($table->is_require) {
                    $file .= "* @is_require\n";
                }
                if($table->readonly) {
                    $file .= "* @readonly = 1\n";
                }
                if($table->hidden) {
                    $file .= "* @hidden = 1\n";
                }
                $file .= "*/\n";
                $file .= "public \${$table->name};\n";
            }
        }
        $file .= "\n}";
        file_put_contents($filename, $file);
    }

    /**
    * Функция по работе с директориями модулей
    * 
    * @param string $action действие 'create/update/delete'
    * @param string $entity_name Код сущности
    * @param int $entity_id id сущности
    * @param string $old_name старое имя
    * 
    * @return bool
    */
    public static function entity($action, $entity_name, $entity_id = null, $old_entity_name = null) {
    	$entity_file_name = Mikron_Type::getFilePath($entity_name);
        switch($action) {
            case 'create':
                self::create_file($entity_id, $entity_file_name);
                self::createTable(mb_strtolower(str_replace('Type_', '', $entity_name)));
                break;
            case 'update':
                if($old_entity_name != $entity_name) {
                    $old_entity_file_name = Mikron_Type::getFilePath($old_entity_name);
                    if(file_exists($old_entity_file_name)) {
                        unlink($old_entity_file_name);
                    }
					self::renameTable($old_entity_name, $entity_name);
                }
                self::create_file($entity_id, $entity_file_name);
                break;
            case 'delete':
            	self::deleteEntityFile($entity_file_name);
                break;
            default:
            	throw new Exception('Invalid action', 950);
                break;
        }
        return true;
    }

    /**
    * Переименование таблицы в базе
    * 
    * @param string $prev_entity_name Старое имя сущности
    * @param string $new_entity_name Новое имя сущности
    * 
    * @return bool
    */
    private static function renameTable($old_entity_name, $new_entity_name) {
		if($old_entity_name == $new_entity_name) {
			return false;
		}
    }

    /**
    * Удаление файла с классом сущности
    * 
    * @param string $entity_file_name
    * 
    * @return bool
    */
    private static function deleteEntityFile($entity_file_name) {
    	if(!file_exists($entity_file_name)) {
    		return false;
		}
    	unlink($entity_file_name);
        $dir = explode('/', $entity_file_name);
        array_pop($dir);
        $dir = implode('/', $dir);
        return self::safeDeleteDir($dir);
    }

    /**
    * Удаление непустых папок
    * 
    * @param string $dir
    * 
    * @return bool
    */
    private static function safeDeleteDir($dir) {
    	if($dir == Mikron_Type::getTypesDir()) {
            return true;
    	}
        $dir_arr = explode('/', $dir);
        $cur_folder = array_pop($dir_arr);
        foreach(scandir($dir) as $file) {
            if('.' === $file || '..' === $file) {
            	continue;
            }
            return true;
        }
        rmdir($dir);
        self::safeDeleteDir(implode('/', $dir_arr));
        return true;
	}

}
