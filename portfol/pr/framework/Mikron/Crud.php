<?php

class Mikron_Crud {

    const ERR_INVALID_ACTION = 405;
    const ERR_INVALID_CAPTCHA = 406;

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    const TRIGGER_CREATE = 'create';
    const TRIGGER_UPDATE  = 'update';
    const TRIGGER_DELETE  = 'delete';

    const TRIGGER_PRE_CREATE = 'pre_create';
    const TRIGGER_PRE_UPDATE  = 'pre_update';
    const TRIGGER_PRE_DELETE  = 'pre_delete';

    private static $upload_directory = null;
    private static $id;
    private static $response;

	private static $trigger_list = array(
		self::TRIGGER_CREATE => array('pre' => array(), 'post' => array()),
		self::TRIGGER_UPDATE => array('pre' => array(), 'post' => array()),
		self::TRIGGER_DELETE => array('pre' => array(), 'post' => array()),
		self::TRIGGER_PRE_CREATE => array('pre' => array(), 'post' => array()),
		self::TRIGGER_PRE_UPDATE => array('pre' => array(), 'post' => array()),
		self::TRIGGER_PRE_DELETE => array('pre' => array(), 'post' => array()),
	);

    /**
    * @return Mikron_Crud_Response
    */
    public static function init() {
        self::$response = new Mikron_Crud_Response();
        self::$response->data = array();
        try {
            $resp = self::crud();
            if($resp === false) {
                return false;
            }
			self::$response->data = $resp;
            self::$response->status = self::STATUS_SUCCESS;
        } catch(Exception $ex) {
        	if($ex->getCode() == 951) {
				self::$response->error_field_list = json_decode($ex->getMessage());
				self::$response->message = 'Ошибка валидации';
        	} else {
				self::$response->message = $ex->getMessage();
        	}
            self::$response->code = $ex->getCode();
            self::$response->status = self::STATUS_ERROR;
        }
        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        // @sciner
        if(!$is_ajax) {
        	if(isset($_SERVER['HTTP_ACCEPT'])) {
        		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$is_ajax = strpos($_SERVER['HTTP_ACCEPT'], 'application/x-ms-application') !== false;
				}
			}
        }
        if($is_ajax) {
            $layout = Zend_Controller_Action_HelperBroker::getStaticHelper('layout');
            $layout->disableLayout();
            echo json_encode(self::$response);
        }
        return self::$response;
    }

    /**
    * @return Mikron_Crud_Response
    */
    static function getResponse() {
        return self::$response;
    }

    /**
    * put your comment there...
    * 
    * @param string $upload_directory
    * @return bool
    */
    static function setUploadDirectory($upload_directory) {
        self::$upload_directory = $upload_directory;
        return true;
    }
    
    /**
    * put your comment there...
    * 
    * @return string
    */
    static function getUploadDirectory() {
        return self::$upload_directory;
    }

    /**
    * @return bool
    */
    private static function crud() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }
        if(!array_key_exists('_mikron', $_POST)) {
            return false;
        }
        $form = $_POST['form'];
        $_mikron = $_POST['_mikron'];
        $_info = $_mikron['state'];
        $_info = Mikron_Crypt::decrypt($_info);
        if($_info->need_captcha) {
            $captcha = $_mikron['captcha'];
            $captchaNS = new Mikron_Session_Namespace('Captcha');
            $captcha_result = $captcha && ($captchaNS->str == $captcha);
            if(!$captcha_result) {
                throw new Exception('Введен некоректный код подтверждения', self::ERR_INVALID_CAPTCHA);
            }
        }
        $action = $_info->action;
        $class_name = $_info->class_name;
        self::$response->action = $action;
        switch($action) {
            case 'create': {
                if(is_object($_info->read_only_values) || is_array($_info->read_only_values)) {
                    foreach((array)$_info->read_only_values as $field_name => $value) {
                        $form[$field_name] = $value;
                    }
                }
                $id = Mikron_Entity_Model::create($class_name, $form);
                break;
            }
            case 'update': {
                $id = $_info->id ?: $form['id'];
                Mikron_Entity_Model::update($class_name, $id, $form);
                break;
            }
            case 'delete': {
                $id = $form['id'];
                return Mikron_Entity_Model::delete($class_name, $id);
            }
            case 'reorder': {
                $id = $form['id'];
                return Mikron_Entity_Model::reorder($class_name, json_decode($id));
            }
            case 'delete_file': {
                $id = $_info->id;
                return Mikron_Entity_Model::delete_file($class_name, $id, $form);
            }
            case 'get': {
                $id = $form['id'];
                $item = Mikron_Entity_Model::get($class_name, $id);
                unset($item->id);
                return Mikron_Functions::cast($item, $class_name);
            }
            case 'getList': {
            	Mikron_Entity_Designer::drawList($_info->class_name, null, null, $_info->params);
            	exit;
            }
            case 'getListSerialize': {
                $filter = $form['filter'];
                $sort = array_key_exists('sort', $form) ? $form['sort'] : null;
                $resp = Mikron_Entity_Model::getList($class_name, $filter, $sort);
                $items = array();
                foreach($resp->items as $item) {
                    $item = (object)$item->jsonSerialize();
                    $items[] = Mikron_Functions::cast($item, $class_name);
                }
                $resp->items = $items;
                return $resp;
            }
            case 'getForm': {
                $id = $form['id'];
                $form_id = Mikron_Crud::getRandomId();
                $p = new Mikron_Entity_Designer_Form_Params(array(
                    'id' => $form_id,
                    'popup' => true,
                    'text_caption' => 'Изменить запись'
                ));
                $item = Mikron_Entity_Model::get($class_name, $id);
                $item = Mikron_Functions::cast($item, $class_name);
                ob_start();
                Mikron_Entity_Designer::drawForm($item, null, $p);
                unset($item->id);
                return array(
                    'html' => ob_get_clean(),
                    'form_id' => 'form-'.$form_id,
                    'item' => $item,
                );
                
            }
            default: {
                throw new Exception('Mikron: Invalid action', self::ERR_INVALID_ACTION);
            }
        }
        if(isset($_FILES['form'])) {
            $table_name = Mikron_Entity_Model::getTableName($class_name);
            $dir_name = str_replace('_', DIRECTORY_SEPARATOR, $table_name);
            $files = $_FILES['form'];
			$upload_dir = self::$upload_directory."/upload/{$dir_name}/{$id}";
            // @sciner -> Только для проекта Регион
			if(empty(self::$upload_directory)){
				$upload_dir = dirname(__FILE__)."/../../project/".APPLICATION_CODE."/public/upload/{$dir_name}/{$id}";
			}
            if(!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_form = array();
            $item = Mikron_Entity_Model::get($class_name, $id);
            $item = Mikron_Functions::cast($item, $class_name);
			$sizes = array();
            foreach($files['name'] as $file_code => $file_name){
                $doc = $class_name::getFieldMeta($file_code);
                if(array_key_exists('size', $doc)) {
                    $size = str_replace('"', '', $doc['size']);
                    $size = explode(',', $size);
                    foreach($size as $s) {
                        if(strpos($s, '=>') !== false) {
                            $s = explode('=>', $s);
                            $s = trim($s[1]);
                        }
                        $s = explode('x', $s);
                        if(count($s) == 2) {
                            $sizes[] = array('x' => $s[0], 'y' => $s[1]);
                        }
                    }
                }
                if(is_array($file_name)) {
                    $file_name = array_merge(explode('|', $item->$file_code), $file_name);
                    $old_files = explode('|', $item->$file_code);
                    $file_name = array_filter($file_name);
                    $file_name = array_unique($file_name);
                    if(count($file_name) > Constant::VAR_MAX_UPLOAD_FILES) {
                        throw new Exception('Нельзя загружать больше '.Constant::VAR_MAX_UPLOAD_FILES.' файлов', 950);
                    }
                    foreach($file_name as $key_file => $file) {
                        $file = $file;
                        $index_file = array_search($file, $files['name'][$file_code]);
                        if($index_file === FALSE || !array_key_exists($index_file, $files['tmp_name'][$file_code])) {
                            if(!in_array($file, $old_files)) {
                                unset($file_name[$key_file]);
                            }
                            continue;
                        }
                        $file = str_replace('.php', '.bin', $file);
                        $file = str_replace('/', null, $file);
                        $file = str_replace('\\', null, $file);
                        $file_path = $files['tmp_name'][$file_code][$index_file];
                        $upload_dir .= '/'.$file_code;
                        if(!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        $upload_dir = realpath($upload_dir);
                        $full_file_name = "{$upload_dir}/{$file}";
                        if(file_exists($full_file_name)) {
                                unlink($full_file_name);
                        }
                        copy($file_path, $full_file_name);
                        chmod($full_file_name, 0777);
                        self::resizeImageMikron($sizes, $file, $upload_dir, $file_path);
                        move_uploaded_file($file_path, $full_file_name);
                    }   
                    $file_form[$file_code] = implode('|', $file_name);
                } else {
                    $new_file_name = null;
                    if(property_exists($item, 'file_name') && $item->file_name_new) { // если есть поле для ручного ввода имени файла
                        $new_file_name = $item->file_name_new;
                    }
                    $file_name = $file_name;
                    $file_name = str_replace('.php', '.bin', $file_name);
                    $file_name = str_replace('/', null, $file_name);
                    $file_name = str_replace('\\', null, $file_name);
                    if($new_file_name) {
                        $file_name_arr = explode('.', $file_name);
                        $ext = array_pop($file_name_arr);
                        $file_name = $new_file_name.'.'.$ext;
                    }
                    $file_path = $files['tmp_name'][$file_code];
                    $upload_dir = realpath($upload_dir);
                    $full_file_name = "{$upload_dir}/{$file_name}";
                    if(file_exists($full_file_name)) {
                            unlink($full_file_name);
                    }
                    move_uploaded_file($file_path, $full_file_name);
                    chmod($full_file_name, 0777);
                    // $form[$file_code] = $file_name;
                    $file_form[$file_code] = $file_name;
                    self::resizeImageMikron($sizes, $file_name, $upload_dir, $full_file_name);
                }
            }
            Mikron_Entity_Model::update($class_name, $id, $file_form, false);
        }
    }

    /**
    * @return int
    */
    static function getNextId() {
        return self::$id = self::$id?++self::$id:1;
    }

    /**
    * @return int
    */
    static function getRandomId() {
        // инициализация текущими микросекундами
        function make_seed() {
            list($usec, $sec) = explode(' ', microtime());
            return (float) $sec + ((float) $usec * 100000);
        }
        mt_srand(make_seed());
        return mt_rand();
    }

/* Система триггеров на действия с сущностями */

    /**
    * put your comment there...
    * @author sciner
    * 
    * @param string $trigger_type
    * @param string $entity_name
    * @param callback $callback
    * @param bool $pre_trigger
    * 
    * @return bool
    */
    public static function addTrigger($trigger_type, $entity_name, $callback) {
    	if(!$entity_name) {
			$entity_name = '__all_entities__';
    	}
    	if(is_array($entity_name)) {
			$entity_name_list = $entity_name;
    	} else {
			$entity_name_list = array($entity_name);
    	}
    	foreach($entity_name_list as $entity_name) {
	        if(array_key_exists($entity_name, self::$trigger_list[$trigger_type])) {
	    		throw new Exception("Триггер {$trigger_type} для типа {$entity_name} уже существует");
			}
	        self::$trigger_list[$trigger_type][$entity_name] = array();
	        self::$trigger_list[$trigger_type][$entity_name][] = $callback;
		}
        return true;
    }

    /**
    * put your comment there...
    * 
    * @param string $trigger_type
    * @param string $entity_name
    * @param callback $callback
    * @param bool $pre_trigger
    * 
    * @return bool
    */
    public static function raiseTrigger($trigger_type, $entity_name, &$row, $delete_item = null) {
        $trigger_list = self::$trigger_list[$trigger_type];
        $resp = null;
        if(array_key_exists($entity_name, $trigger_list)) {
	        foreach($trigger_list[$entity_name] as $trigger) {
	            if(is_callable($trigger)) {
                    /* передача сущности по ссылке для возможности её редактирования в триггере */
	                $resp = call_user_func_array($trigger, array(&$row, $entity_name, $delete_item));
	            } else {
	                throw new Exception('Непредвиденная ошибка');
	            }
	        }
		}
        if(array_key_exists('__all_entities__', $trigger_list)) {
	        foreach($trigger_list['__all_entities__'] as $trigger) {
	            if(is_callable($trigger)) {
	                call_user_func($trigger, $row, $entity_name, $delete_item);
	            } else {
	                throw new Exception('Непредвиденная ошибка');
	            }
	        }
        }
        return $resp;
    }
    
    /**
    * 
    * @param array $sizes массив размеров
    * @param string $file_name Имя файла
    * @param string $upload_dir папка сохранения
    * @param string $full_file_name искомый файл
    * 
    * @return string[] Пути к созданным файлам
    */
    public static function resizeImageMikron($sizes, $file_name, $upload_dir, $full_file_name) {
        $resp = [];
       if(isset($sizes) && is_array($sizes) && count($sizes)) {
            foreach($sizes as $size) {
                $size_dir = $upload_dir.'/'.implode('x', array_values($size));
                if(!file_exists($size_dir)) {
                    mkdir($size_dir, 0777, true);
                }
                $full_new_name = $size_dir.'/'.$file_name;
                $f = file_get_contents($full_file_name);
                $orig_image = imagecreatefromstring($f);
                unset($f);
                $cropped_img = Plugin_Graph::cropImage2($orig_image, $size['x'], $size['y']);
                imagedestroy($orig_image);
                if($img = Plugin_Graph::resizeImage($cropped_img, $size['x'], $size['y'])) {
                    imagejpeg($img, $full_new_name, 95);
                    $resp[] = $full_new_name;
                    imagedestroy($img);
                    chmod($full_new_name, 0777);
                }
            }
        }
        return $resp;
    }

}