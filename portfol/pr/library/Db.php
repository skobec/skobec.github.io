<?php
//!IS_DEVELOPER_HOST ? Config::read(CONFIG_DIR.'/db.json'): Config::read(CONFIG_DIR.'/db_local.json');
    Config::read(CONFIG_DIR.'/db.json');

    class Db {

    	private static $db;

        static function get() {
			if(self::$db) {
				return self::$db;
			}
			include dirname(__FILE__).'/../framework/NotORM.php';
			$con = Prodom_Connector::getConnection('db_general');
			$pdo = $con->getConnection();

			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return self::$db = new NotORM($pdo);


        }

    }

class db_general
{
    public static $db;
}

db_general::$db = Db::get();

