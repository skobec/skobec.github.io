<?php

class Model_Log {

    public static function getDb() {
    	return Prodom_Connector::getConnection('db_general');
    }
    
    /**
    * @author sciner
    * @since 2015-01-19
    * 
    * @param int $log_action_id Type_Log_Action
    * @param User $user
    * @param string $entity_name
    * @param int $entity_id
    * @param array $entity_state
    * @param string $description
    * 
    * @return bool
    */
    static function add($log_action_id, $user, $entity_name, $entity_id, $entity_state, $description) {

		$db = self::getDb();

		$entity_state = (array)$entity_state;

		// обрезание особо длинных строк, для того, чтобы описание сущности вместилось в 4кб
		$entity_state = array_map(function($value){
			$max_field_length = 1000;
			return (strlen($value) <= $max_field_length) ? $value : substr($value, 0, $max_field_length).'...';
		}, $entity_state);

		/**
		* Первое редактирование объекта (да тормознуто и неправильно, но...)
		* @author sciner
		* @since 2015-02-24
		*/
		if($log_action_id == Constant_Base::LOG_ENTITY_EDIT) {
			$prev_state = $db->fetchRow($db->select()->from(array('h' => 'mikron_entity_history'))
				->where('h.entity_name = ?', $entity_name)
				->where('h.entity_id = ?', $entity_id)
				->where('h.operation_id = ?', $log_action_id)
			);
			if(!$prev_state) {
				if($entity_actual = Mikron_Entity_Model::getById($entity_name, $entity_id)) {
					$entity_actual = (array)$entity_actual;
					// обрезание особо длинных строк, для того, чтобы описание сущности вместилось в 4кб
					$entity_actual = array_map(function($value){
						$max_field_length = 1000;
						return (strlen($value) <= $max_field_length) ? $value : substr($value, 0, $max_field_length).'...';
					}, $entity_actual);
					$diff = array_diff_assoc($entity_state, $entity_actual);
					if($diff) {
						$entity_state = array(
							'_diff' => $diff,
							'_prev' => $entity_actual,
						);
					}
				}
			}
		}

		$entity_state = json_encode($entity_state, JSON_UNESCAPED_UNICODE);
		$entity_state = substr($entity_state, 0, 4000);
		$dt = date('Y-m-d H:i:s', time());
		$entity_history_row = array(
			'dt' => $dt,
			'user_profile_id' => $user->id,
			'operation_id' => $log_action_id,
			'entity_name' => $entity_name,
			'entity_id' => $entity_id,
			'entity_row' => $entity_state,
		);
		$db->insert('mikron_entity_history', $entity_history_row);
		$mikron_entity_history_id = $db->lastInsertId('mikron_entity_history', 'id');
		$log_row = array(
			'dt' => $dt,
			'user_profile_id' => $user->id,
			'log_action_id' => $log_action_id,
			'description' => $description,
			'mikron_entity_history_id' => $mikron_entity_history_id,
		);
		$db->insert('log', $log_row);
		$log_id = $db->lastInsertId('log', 'id');
		$db->update('mikron_entity_history', array('log_id' => $log_id), array('id = ?' => $mikron_entity_history_id));
		return true;
    }

    static function insert($user, $log_action_id, $description = null) {
    	$db = self::getDb();
    	$log_row = array(
    		'log_action_id' => (int)$log_action_id,
    		'dt' => date('Y-m-d H:i:s'),
    		'user_profile_id' => $user ? $user->id : null,
    		'description' => $description,
    	);
		$db->insert('log', $log_row);
    }

	private static function formatDiffValue($value) {
		// Булевые значения (чекбоксы)
		if($value === true) {
			$value = 'Да';
		} elseif($value === false) {
			$value = 'Нет';
		}
		if($value === null) {
			$value = Constant_Base::NO_VALUE;
		} else {
			$value = htmlspecialchars($value);
		}
		return $value;
	}

    static function getLog($paginator, $entity_name = null, $entity_id = null, $sort = null, $filter = null) {
    	$db = Prodom_Connector::getConnection('db_general');
		$history = $db->select()
			->from(array('l' => 'log'))
			//->from(array('h' => 'mikron_entity_history'))
			->joinLeft(array('h' => 'mikron_entity_history'), ' h.log_id = l.id', array('entity_row', 'entity_name', 'entity_id'))
			->joinLeft(array('u' => 'user_profile'), 'u.id = l.user_profile_id', array('lname', 'fname', 'mname', 'login'))
//			->joinLeft(array('l' => 'log'), 'l.id = h.log_id', array('log_description' => 'description'))
			->joinLeft(array('la' => 'log_action'), 'la.id = l.log_action_id', array('log_action_id' => 'id', 'log_action_title' => 'title'))
			->order('h.id ASC')
			->order('l.id ASC');
		$filter = (array)$filter;
		foreach($filter as $column => $value) {
			if($value === '' || $value === null) {
				continue;
			}
			switch ($column) {
				case 'log_action_title':
					$history->where('la.title ilike ?', "%{$value}%");
					break;
				case 'description':
					$history->where('l.description ilike ?', "%{$value}%");
					break;
				case 'login':
					$history->where('u.login || \' \' || u.lname || \' \' || u.fname || \' \' || u.mname ilike ?', "%{$value}%");
					break;
				default:
					break;
			}
		}
		if($entity_name) {
			$history->where('entity_name = ?', $entity_name);
		}
		if($entity_id) {
			$history->where('entity_id = ?', $entity_id);
		}
		if($sort) {
			$history->order(array($sort->field.' '.$sort->dir));
		} else {
			$history->order('h.dt ASC');
		}
		if($paginator) {
			$history->limit($paginator->items_per_page, $paginator->getStartIndex());
		}
		$resp = array();
        $state_old = array();
        $field_list = array();
        $q = $db->query($history);
        $prev_entity = null;
        while($item = $q->fetch()) {
	        $fio = array($item->lname, $item->fname, $item->mname);
			$fio = array_filter($fio);
			$fio = implode(' ', $fio) ?: $item->login;
        	$entity_row = (array)json_decode($item->entity_row);
        	if($prev_entity != "{$item->entity_name}#{$item->entity_id}") {
				$prev_entity = "{$item->entity_name}#{$item->entity_id}";
        		$state_old = array();
        	}
	        	if(array_key_exists('_diff', $entity_row) && array_key_exists('_prev', $entity_row)) {
	        		// предыдущее состояние
	        		$state_old = (array)$entity_row['_prev'];
	        		// только изменения
	        		$state_new = (array)$entity_row['_diff'];
	        	} else {
					$state_new = $entity_row;
	        	}
				$diff = array_diff_assoc($state_new, $state_old);
				if(!$state_old) {
					$diff = array();
				}

			$change_text = null;
			foreach($diff as $field_name => $value) {
				if(!array_key_exists($item->entity_name, $field_list)) {
					$entity_name_var = $item->entity_name;
					$field_list[$item->entity_name] = $entity_name_var::getFields();
				}
				// предыдущее значение
				$old_value = null;
				if(array_key_exists($field_name, $state_old)) {
					$old_value = $state_old[$field_name];
				}
				// Человекопонятное имя поля
				$fl = $field_list[$item->entity_name];
				if(array_key_exists($field_name, $fl)) {
					$field = $fl[$field_name];
					$field_name = $field->description;
					if(strtolower($field->type) == 'bool') {
						$value = $value ? true : false;
						$old_value = $old_value ? true : false;
					}
				}
				$value = self::formatDiffValue($value);
				$old_value = self::formatDiffValue($old_value);
				$change_text .= "<tr><td>{$field_name}</td><td>{$old_value}</td><td>{$value}</td></tr>";
			}
			if($change_text) {
				$change_text = <<<html
<table class="table-entity-history-diff">
	<thead>
		<tr>
			<th>Поле</th>
			<th>Старое значение</th>
			<th>Новое значение</th>
		</tr>
	</thead>
	<tbody>
		{$change_text}
	</tbody>
</table>
html;
			}
			$resp[] = (object)array(
				'fio' => $fio,
				'dt' => strtotime($item->dt),
				'operation_title' => $item->description ?: $item->log_action_title,
				'change_text' => $change_text,
				'entity_name' => $item->entity_name,
				'entity_id' => $item->entity_id,
				'log_action_id' => $item->log_action_id,
				'log_action_title' => $item->log_action_title,
				'description' => $item->description
		);
			$state_old = array_merge($state_old, $state_new);
		}
		$result = array('items' => $resp);
		if($paginator) {
			$result['paginator'] = $paginator->getCalculated($db, $history);
		}
		return (object)$result;
    }

}
