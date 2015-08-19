<?php

/**
* @author sciner
* @since 20215-04-02
*/
class Mikron_Hg_Parser {

	/**
	* put your comment there...
	* @author sciner
	* @since 20215-04-02
	* 
	* @param string $file_name
	* 
	* @return Mikron_Hg_Log[]
	*/
	function readLog($file_name) {
		/*
			changeset:   328:0680cfab2f20
			parent:      327:322f15778d11
			parent:      325:64cf29ab435f
			user:        Ivan Ivanov (vasya) <email@domain.com>
			date:        Fri Feb 13 10:02:15 2015 +0300
			summary:     Слить ветку
		*/
		$log_list = array();
		$ar = explode("\n\n", iconv('cp1251', 'utf-8', file_get_contents($file_name)));
		foreach($ar as $log) {
			$log_item = new Mikron_Hg_Log(array(
				'parent' => array(),
			));
			$log = explode("\n", $log);
			foreach($log as $l) {
				$l = explode(':', $l, 2);
				if(count($l) !== 2) {
					continue;
				}
				$key = trim($l[0]);
				$value = trim($l[1]);
				switch($key) {
					case 'changeset': {
						$log_item->changeset = $value;
						break;
					}
					case 'parent': {
						$log_item->parent[] = $value;
						break;
					}
					case 'user': {
						$log_item->user = $value;
						break;
					}
					case 'date': {
						$log_item->date = strtotime($value);
						break;
					}
					case 'summary': {
						$log_item->summary = $value;
						break;
					}
				}
			}
			if($log_item->changeset) {
				$log_list[] = $log_item;
			}
		}
		return($log_list);
	}

	/**
	* put your comment there...
	* @author sciner
	* @since 20215-04-02
	* 
	* @param string $file_name
	* 
	* @return Mikron_Hg_Changeset
	*/
	function readChangeset($file_name) {
		$changeset = new Mikron_Hg_Changeset(array('file_list' => array()));
		$changeset_file = null;
		$file = file($file_name);
		foreach($file as $line) {
			if(substr($line, 0, 8) == 'diff -r ') {
				$changeset_file = new Mikron_Hg_Changeset_File(array('diff' => $line, 'line_list' => array()));
				$changeset->file_list[] = $changeset_file;
				continue;
			}
			if($changeset_file) {
				// $line_number++;
				$type_code = 'blank';
				switch(substr($line, 0, 1)) {
					case '-': {
						$type_code = 'remove';
							break;
						}
					case '+': {
						$type_code = 'add';
							break;
						}
					case '@': {
						$type_code = 'info';
							break;
						}
				}
				$changeset_file->line_list[] = new Mikron_Hg_Changeset_File_Line(array('line' => $line, 'type_code' => $type_code));
			}
		}
		return $changeset;
	}
}