<?php

Config::read(dirname(__FILE__).'/../../config/db.json');

class Mongo_Log {

	const COL_EXCEPTION = 'exception';
    
    static function getMongo() {
        return Prodom_Connector::getConnection('mongo_homelog');
    }
    
    static function getDbName() {
        $options = Config::get('mongo_homelog');
        return $options->dbname;
    }

	/**
	 * Возвращает список записей из журнала изменений домов
	 * @author Roman
	 * 
	 * @param array $db
	 * @param array $collectionName
	 * @param array $criteria Массив с условиями выборки
	 * @param int $ipp Количество элементов на странице
	 * @param int $page Номер страницы
         * @param array $sort
	 * 
	 * @return object[]
	 */
	private static function getList($db, $collectionName, $criteria, $ipp = 50, $page = 1, $sort = array()) {
		$ipp = max(min($ipp, 1000), 1); 
		$page = max(min($page, 1000), 1);
		$offset = $ipp * ($page - 1);
        $connection = self::getMongo();
		$db = $connection->$db;
		$collection = $db->$collectionName;
                if (!$sort){
                    $sort = array("date" => -1);
                }else{
                    $sort = array($sort['field'] => (int)$sort['dir']);
                }
		$cursor = $collection->find($criteria)
				->limit($ipp)
				->skip($offset)                
				->sort($sort);
                $result = new Type_Log_List(array(
			'items' => array(),
			'paginator' => new Type_Paginator(array(
				'items_per_page' => $ipp,
				'current_page' => $page,
				'total_pages' => 0,
				'records_count' => $cursor->count(),
			)),
		));
		if(!count($cursor)) {
			return $result;
		}
		$result->paginator->total_pages = ceil($result->paginator->records_count / $ipp);
		foreach ($cursor as $document) {
			$result->items[] = $document;
		}
		$connection->close();
		return $result;
	}

    private static function getItem($db, $collectionName, $criteria) {
    	if(IS_DEVELOPER_HOST) {
    		return null;
		}
        $connection = self::getMongo();
        $db = $connection->$db;
        $collection = $db->$collectionName;
        $cursor = $collection->find($criteria)->sort(array("date" => -1))->limit(1);
        if(!count($cursor)) {
            return null;
        }
		$document = $cursor->getNext();
        $connection->close();
        return $document;
    }
    
	/**
	 * Возвращает список записей из журнала изменений домов
	 * @author Roman
	 * 
	 * @param array $criteria Массив с условиями выборки
	 * @param int $ipp Количество элементов на странице
	 * @param int $page Номер страницы
	 * 
	 * @return Type_Log_List
	 */
	public static function getHomeHistory($criteria, $ipp = 50, $page = 1, $sort = array()) {
		return self::getList('mjf', self::getDbName(), $criteria, $ipp, $page, $sort);
	}

    /**
    * Возвращает последнее изменение дома на текущей странице
    * @author notfoolen
    * @since 02.08.2013
    * 
    * @param array $criteria Массив с условиями выборки
    * 
    * @return Type_Log_Record_HomeHistory
    */
    public static function getLastHomePageChange($criteria) {
        return self::getItem('mjf', self::getDbName(), $criteria);
    }
    
	/**
	 * Возвращает список записей из журнала ошибок
	 * @author Roman
	 * 
	 * @param array $criteria Массив с условиями выборки
	 * @param int $ipp Количество элементов на странице
	 * @param int $page Номер страницы
	 * 
	 * @return Type_Log_List
	 */
	public static function getExceptionList($criteria, $ipp = 50, $page = 1) {
		return self::getList('mjf', self::COL_EXCEPTION, $criteria, $ipp, $page);
	}
	
	/**
	 * Возвращает запись из журнала ошибок по идентификатору
	 * @author Roman
	 * 
	 * @param string $id Идентификатор записи
	 * 
	 * @return Type_Log_Record_Exception
	 */
	public static function getExceptionById($id) {
		return self::getById('mjf', self::COL_EXCEPTION, $id);
	}

	/**
	 * Добавление записи в журнал изменений домов
	 * @author Roman
	 * 
	 * @param Type_Log_Record_HomeHistory $record Массив данных для записи
	 * 
	 * @return bool
	 */
	public static function addHomeRecord($record) {
		if(!($record instanceof Type_Log_Record_HomeHistory)) {
			throw new Exception('Журнал изменений: Неверный тип записи', 500);
		}
		return self::add('mjf', self::getDbName(), $record);
	}
	
	/**
	 * Добавление записи в журнал изменений домов
	 * @author Roman
	 * 
	 * @param Type_Log_Record_Exception $record Массив данных для записи
	 * 
	 * @return bool
	 */
	public static function addExceptionRecord($record) {        
		if(!($record instanceof Type_Log_Record_Exception)) {
			throw new Exception('Журнал изменений: Неверный тип записи', 500);
		}
        if(defined('IS_DEVELOPER_HOST')) {
            if(IS_DEVELOPER_HOST) {
                return false;
            }
        }
        if(strlen($record->exception) > 10000) {
			$record->exception = substr($record->exception, 0, 10000).'...';
        }
        if(strlen($record->message) > 10000) {
			$record->message = substr($record->message, 0, 10000).'...';
        }
        if(strlen($record->post) > 10000) {
			$record->post = substr($record->post, 0, 10000).'...';
        }
		return self::add('mjf', self::COL_EXCEPTION, $record);
	}

	/**
	 * 
	 * @param type $db
	 * @param type $collectionName
	 * @param type $record
	 * @return boolean
	 */
	private static function add($db, $collectionName, $record) {
		if(!$db || !$collectionName || !$record) {
			return false;
		}
        $con = self::getMongo();
		$db = $con->$db;
		$collection = $db->$collectionName;
		$collection->insert($record);
		$con->close();
		return true;
	}
	
	/**
	 * 
	 * @param string $db
	 * @param string $collectionName
	 * @param string $id
	 * 
	 * @return object[]
	 */
	private static function getById($db, $collectionName, $id) {
		if(!$db || !$collectionName || !$id) {
			return false;
		}
		$connection = self::getMongo();
		$db = $connection->$db;
		$collection = $db->$collectionName;
		$criteria = array('_id' => new MongoId($id));
		$document = $collection->findOne($criteria);
		$connection->close();
		return $document;
	}

}