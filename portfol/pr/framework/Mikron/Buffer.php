<?php

/**
* @author sciner
* @since 2014-11-01
*/
class Mikron_Buffer {

	private $buffer = array();
	private $header = false;
	private $index = 0;
	private $limit = 0;
	private $is_simple_list = false;
	private $simple_list_keys = array();

	function append($item) {
		if(!is_array($item)) {
			$item = (array)$item;
		}
		if(!$this->header) {
			$this->header = array_keys($item);
			$this->buffer[] = json_encode($this->header, JSON_UNESCAPED_UNICODE);
		}
		$this->buffer[] = json_encode(array_values($item), JSON_UNESCAPED_UNICODE);
	}

	function &getResult() {
		return $this->buffer;
	}

	/**
	* @param string[] $buffer
	*/
	function fetch(&$buffer, $type = null) {
		if($this->index == 0) {
			if(!$buffer) {
				return null;
			}
			$this->simple_list_keys = array_keys($buffer);
			$key = $this->simple_list_keys[0];
			$item = $buffer[$key];
			$jsd = is_string($item) ? json_decode($item) : false;
			$this->is_simple_list = ($jsd === false);
			$this->limit = count($buffer);
			if(!$this->is_simple_list) {
				$this->header = (array)$jsd;
				$this->index++;
			}
		}
		if($this->index >= $this->limit) {
			return null;
		}
		if($this->is_simple_list) {
			$key = $this->simple_list_keys[$this->index++];
			$item = $buffer[$key];
			return $item;
		} else {
			$item = json_decode($buffer[$this->index++]);
			$item = array_combine($this->header, (array)$item);
			if($type) {
				if(substr($type, -2, 2) == '[]') {
					return Functions::castAll($item, substr($type, 0, strlen($type) - 2));
				} else {
					return Functions::cast($item, $type);
				}
			} else {
				return $item;
			}
		}
	}

}