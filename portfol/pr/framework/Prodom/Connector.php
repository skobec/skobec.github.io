<?php

class Prodom_Connector {

    private static $savedConnections = array();

    static function getPDO($config_name) {
        $options = Config::get($config_name);
        $dbms = $options->dbms;
        switch($dbms) {
            case 'pgsql': {
                // return "pgsql:dbname={$options->dbname};host={$options->host};port={$options->port};user={$options->username};password={$options->password}";
                throw new Exception('Not implemented');
            }
            case 'mysql': {
                // return array("mysql:dbname={$options->dbname};host={$options->host}", $options->username, $options->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                return new PDO("mysql:dbname={$options->dbname};host={$options->host};port={$options->port}", $options->username, $options->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            }
            case 'redis': {
                throw new Exception('Not implemented');
            }
            case 'mongo': {
                throw new Exception('Not implemented');
            }
        }
    }

    static function getConnectionString($config_name) {
        $options = Config::get($config_name);
        $dbms = $options->dbms;
        switch($dbms) {
            case 'pgsql': {
                return "pgsql:dbname={$options->dbname};host={$options->host};port={$options->port};user={$options->username};password={$options->password}";
            }
            case 'mysql': {
                return "mysql:host={$options->host};port={$options->port};dbname={$options->dbname};Uid={$options->username};Pwd={$options->password}";
            }
            case 'redis': {
                throw new Exception('Not implemented');
            }
            case 'mongo': {
                throw new Exception('Not implemented');
            }
        }
    }
    
    static function getConnection($config_name) {
        $options = Config::get($config_name);
        $dbms = $options->dbms;
        switch($dbms) {
            case 'pgsql': {
                return self::getPgsqlConnection($options);
            }
            case 'mysql': {
                return self::getMysqlConnection($options);
            }
            case 'redis': {
                return self::getRedisConnection($options);
            }
            case 'mongo': {
                return self::getMongoConnection($options);
            }
        }
    }

    /**
    * @return MongoClient
    */
    private static function getMongoConnection($options) {
        $key = md5(json_encode($options));
        if (array_key_exists($key, self::$savedConnections)) {
            return self::$savedConnections[$key];
        }
        $cs = "mongodb://{$options->host}:{$options->port}";
        $connection = new MongoClient($cs);
        self::$savedConnections[$key] = $connection;
        return self::$savedConnections[$key];
    }

    /**
    * @return Rediska
    */
    private static function getRedisConnection($options) {
        $key = md5(json_encode($options));
        if (array_key_exists($key, self::$savedConnections)) {
            return self::$savedConnections[$key];
        }
        /*
        // http://windows.php.net/downloads/pecl/snaps/redis/
        $redis = new Redis();
        ini_set('session.prefix', $options->namespace); // 'lipetsk_redis');
        $redis->connect($options->host, $options->port);
        self::$savedConnections[$key] = $redis;
        */
        self::$savedConnections[$key] = new Rediska(
            array(
                'namespace' => $options->namespace,
                'servers' => array(
                    array(
                        'host' => $options->host,
                        'port' => $options->port
                    )
                )
            )
        );
        return self::$savedConnections[$key];
    }

    /**
    * @return Zend_Db_Adapter_Pdo_Mysql
    */
    private static function getMysqlConnection($options) {
        $key = md5(json_encode($options));
        if (array_key_exists($key, self::$savedConnections)) {
            return self::$savedConnections[$key];
        }
        // настройка подключения к базе
        $con = new Zend_Db_Adapter_Pdo_Mysql(array(
            'host'     => $options->host,
            'username' => $options->username,
            'password' => $options->password,
            'dbname'   => $options->dbname,
            'port'     => property_exists($options, 'port') ? $options->port : 3306,
            // 'driver_options'=> array(PDO_MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8') // 'SET CHARACTER SET utf8') //'SET NAMES UTF8')
        ));
        $con->query("set character_set_client = 'utf8'");
        $con->query("set character_set_results = 'utf8'");
        $con->query("set collation_connection = 'utf8_general_ci'");        
        $con->setFetchMode(Zend_Db::FETCH_OBJ);
        self::$savedConnections[$key] = $con;
        return self::$savedConnections[$key];
    }

    /**
    * @return Zend_Db_Adapter_Pdo_Pgsql
    */
    private static function getPgsqlConnection($options) {
        $key = md5(json_encode($options));
        if (array_key_exists($key, self::$savedConnections)) {
            return self::$savedConnections[$key];
        }
        // PostgreSQL
        $con = new Zend_Db_Adapter_Pdo_Pgsql(array(
            'host'     => $options->host,
            'port'     => $options->port,
            'username' => $options->username,
            'password' => $options->password,
            'dbname'   => $options->dbname
            /*'profiler' => false,
            'persistent' => true,*/
        ));
        $con->setFetchMode(Zend_Db::FETCH_OBJ);
        self::$savedConnections[$key] = $con;
        return self::$savedConnections[$key];
    }

}

