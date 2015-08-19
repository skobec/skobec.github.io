<?php

class Mikron_Entity_Filter_IsNotnull extends Mikron_Entity_Filter {

	public function apply(NotORM_Result $select, $t_field_name) {
		$select->where("NOT ({$t_field_name})", null);
	}
}