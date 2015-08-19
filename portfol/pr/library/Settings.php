<?php

class Settings {

	static $params = null;

	static function init() {
		self::$params = array();
		$db = Prodom_Connector::getConnection('db_general');
		$select = $db->select()->from(array('s' => 'settings'), array('key', 'value'));
		$respond = $db->fetchAll($select);
		$settings = array();
		foreach($respond as $item) {
			$settings[$item->key] = $item->value;
		}
		self::$params = (array)Functions::cast($settings, 'Type_Settings');
	}

	static function get($name, $default_value = null) {
		// Developer's hook!
		if($name == 'cookie_domain') {
			return IS_DEVELOPER_HOST ? 'mpt.loc' : Constant::COOKIE_DOMAIN;
		} elseif ($name == 'root_domain') {
			return IS_DEVELOPER_HOST ? 'mpt.loc' : Constant::VAR_ROOT_DOMAIN;
		}
		if(!self::$params) {
			self::init();
		}
		if($name == 'env_name') {
			return Constant::VAR_REGION_CODE.(self::get('is_test') ? 'test' : '');
		}
		return array_key_exists($name, self::$params) ? self::$params[$name] : $default_value;
	}

	static function get_list() {
		if(!self::$params) {
			self::init();
		}
		return self::$params;
	}

	static function set($name, $value) {
		$db = Prodom_Connector::getConnection('db_general');
		$select = $db->select()->from(array('s' => 'settings'))->where('s.key = ?', $name);
		if($db->fetchRow($select)) {
			$db->update('settings', array('value' => $value), array('key = ?' => $name));
			self::init();
		} else {
			$db->insert('settings', array('key' => $name, 'value' => $value));
		}
	}
}