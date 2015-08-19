<?php

abstract class Mikron_Entity_Filter {

	function __toString() {
		return '';
	}

	abstract function apply(NotORM_Result $select, $t_field_name);
	
}
