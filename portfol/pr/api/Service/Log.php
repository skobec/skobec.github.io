<?php

/**
* Логирование
* @author sciner
* @since 2015-01-19
*/
class Service_Log extends Prodom_Api_Service {

    /**
    * @author sciner
    * @since 2015-01-19
    * 
    * @param string $session_id
    * @param int $log_action_id Type_Log_Action
    * @param string $entity_name
    * @param int $entity_id
    * @param array $entity_state
    * @param string $description
    * 
    * @return bool
    */
    function add($session_id, $log_action_id, $entity_name, $entity_id, $entity_state, $description) {
    	return Model_Log::add($log_action_id, $this->user, $entity_name, $entity_id, $entity_state, $description);
	}

    /**
    * @author Tagir Zinnurov
    * @since 2015-04-14
    * 
    * @param string $session_id
    * @param int $log_action_id
    * @param string $description
    * 
    * @return bool
    */
    function insert($session_id, $log_action_id, $description) {
        return Model_Log::insert($this->user, $log_action_id, $description);
    }

    /**
    * История редактирования сущности
    * @author sciner
    * @since 2014-12-04
    * 
    * @param string $session_id
    * @param string $entity_name
    * @param int $entity_id
    * @param Type_Paginator $paginator
    * @param Type_Sort $sort
    * 
    * @return object[]
    */
    function getList($session_id, $entity_name, $entity_id, $paginator, $sort) {
    	return Model_Log::getLog($paginator, $entity_name, $entity_id, $sort);
    }

	/**
	 * Возвращает операции из истории, при которых нельзя удалять лицевой счет
	 * Все типы операций находятся в таблице log_action
	 * @author sharafanmaxim78
	 * @since 2015-03-12
	 *
	 * @param string $session_id
	 * @param type $entity_name
	 * @param type $entity_id
	 * 
	 * @return array 
	 */
	static function getCriticalLogOperation($session_id, $entity_name , $entity_id){
		$log_action_id_array=array(1000, 2001, 2002);
		$all_operations= Model_Log::getLog(null, $entity_name, $entity_id, null);		

		$critical_operations=array();
		if(!empty($all_operations->items)){
			foreach($all_operations->items as $op) {
				if(in_array($op->log_action_id, $log_action_id_array) ){
					$critical_operations[]=$op;
				}
			}
		}
		return $critical_operations;
	}
}