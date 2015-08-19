<?php

class Engine {
    
    const TYPE_INT = 0;
    const TYPE_STRING = 1;
    const TYPE_BOOL = 2;
    const TYPE_TEXT = 3;
    const TYPE_LIST = 4;
    /* const TYPE_TABLE = 5; */
    const TYPE_DATE = 6;
    const TYPE_FLOAT = 7;
    const TYPE_FILE = 8;
    const TYPE_ENTITY = 9;

    public static $types = array();

    static function getTypes() {
        return self::$types;
    }

    static function getTypeById($id)  {
        reset(self::$types);
        foreach(self::$types as $t) {
            if($t->id == $id) {
                return $t;
            }
        }
    }

    static function getScalarTypes()  {
        $ret = Engine::$types;
        unset($ret[5]);
        return $ret;
    }

    static function getTypeByCode($code)  {
        foreach(self::$types as $t) {
            if($t->code == $code) {
                return $t;
            }
        }
    }

}

Engine::$types = array(

    new Engine_Field_Type(array(
        'id' => Engine::TYPE_INT,
        'code' => 'int',
        'title' => 'Целое число',
        'db_type' => 'int4',
        'validate' => array(
            'numeric',
        ),
        'ex_validate' => array(
            'min',
            'max',
        ),
    )),

    new Engine_Field_Type(array(
        'id' => Engine::TYPE_STRING,
        'code' => 'string',
        'title' => 'Строка',
        'db_type' => 'varchar(255)',
        'validate' => array(
        ),
        'ex_validate' => array(
        ),
    )),

    new Engine_Field_Type(array(
        'id' => Engine::TYPE_BOOL,
        'code' => 'bool',
        'title' => 'Да/нет',
        'db_type' => 'int2 DEFAULT 0',
        'validate' => array(
            'bool'
        ),
        'ex_validate' => array(
        ),
    )),

    new Engine_Field_Type(array(
        'id' => Engine::TYPE_TEXT,
        'code' => 'text',
        'title' => 'Текст',
        'db_type' => 'varchar(2048)',
        'validate' => array(
        ),
        'ex_validate' => array(
        ),
    )),

    new Engine_Field_Type(array(
        'id' => Engine::TYPE_LIST,
        'code' => 'list',
        'title' => 'Выбор из списка',
        'db_type' => 'int4',
        'validate' => array(
        ),
        'ex_validate' => array(
        ),
    )),

    new Engine_Field_Type(array(
        'id' => Engine::TYPE_DATE,
        'code' => 'date',
        'title' => 'Дата',
        'db_type' => 'timestamp',
        'validate' => array(
            'date'
        ),
        'ex_validate' => array(
        ),
    )),

    new Engine_Field_Type(array(
        'id' => Engine::TYPE_FLOAT,
        'code' => 'float',
        'title' => 'Вещественное число',
        'db_type' => 'float',
        'validate' => array(
            'float',
        ),
        'ex_validate' => array(
            'min',
            'max',
        ),
    )),

    new Engine_Field_Type(array(
        'id' => Engine::TYPE_FILE,
        'code' => 'file',
        'title' => 'Файл',
        'db_type' => 'varchar(4096)',
        'validate' => array(
        ),
        'ex_validate' => array(
        ),
    )),

    new Engine_Field_Type(array(
        'id' => Engine::TYPE_ENTITY,
        'code' => 'entity',
        'title' => 'Объект',
        'db_type' => 'int4',
        'validate' => array(
        ),
        'ex_validate' => array(
        ),
    )),

);