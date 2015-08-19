<?php

class Mikron_Hg_Log extends Mikron_Type {
	/**
	* @var string
	*/
	public $changeset;
	/**
	* put your comment there...
	* @var string[]
	*/
	public $parent;
	/**
	* @var string
	*/
	public $user;
	/**
	* @var datetime
	*/
	public $date;
	/**
	* @var string
	*/
	public $summary;
}