<?php

    class Mikron_Entity_Designer_Template_List extends Mikron_Type {

	private static $run_counter = 0;

	static function run() {
	    self::$run_counter++;
	}

	/**
	* @return bool
	*/
	static function isFirstRun() {
	    return self::$run_counter == 1;
	}

    }
