<?php

class Mikron_Hg_Changeset_File extends Mikron_Type {
	/**
	* @var string
	*/
	public $diff;
	/**
	* @var string
	*/
	public $file_name;
	/**
	* @var Mikron_Hg_Changeset_File_Line[]
	*/
	public $line_list;
}
