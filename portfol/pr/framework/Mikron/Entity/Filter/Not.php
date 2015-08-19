<?php

class Mikron_Entity_Filter_Not extends Mikron_Entity_Filter {

	private $_condition;

	public function __construct($condition) {
		$this->_condition = $condition;
	}

	public function apply(NotORM_Result $select, $t_field_name) {
		$condition = str_replace("'", "\'", trim($this->_condition));
		$select->where("({$t_field_name}) != '{$condition}'");
	}

}
