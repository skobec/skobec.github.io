<?php

class Issue {

	public static $status_changemap = array(
		Constant::ISSUE_STATUS_NEW => array(
            //Constant::ISSUE_STATUS_CONFIRMED,
            Constant::ISSUE_STATUS_INWORK,
            Constant::ISSUE_STATUS_REPEAT,
            Constant::ISSUE_STATUS_REJECTED,
		),
		Constant::ISSUE_STATUS_CONFIRMED => array(
			//Constant::ISSUE_STATUS_NEW
            Constant::ISSUE_STATUS_INWORK,
            Constant::ISSUE_STATUS_COMPLETED,
		),
		Constant::ISSUE_STATUS_INWORK => array(
            Constant::ISSUE_STATUS_COMPLETED,
			//Constant::ISSUE_STATUS_NEW,
			//Constant::ISSUE_STATUS_CONFIRMED
		),
		Constant::ISSUE_STATUS_READY => array(
			//Constant::ISSUE_STATUS_NEW,
			//Constant::ISSUE_STATUS_CONFIRMED,
			Constant::ISSUE_STATUS_INWORK,
			Constant::ISSUE_STATUS_REPEAT
		),
		Constant::ISSUE_STATUS_COMPLETED => array(),
		Constant::ISSUE_STATUS_REJECTED => array(
			//Constant::ISSUE_STATUS_NEW,
			//Constant::ISSUE_STATUS_CONFIRMED,
			Constant::ISSUE_STATUS_INWORK
		),
		Constant::ISSUE_STATUS_REPEAT => array(),
		Constant::ISSUE_STATUS_ARCHIVE => array()
	);

}