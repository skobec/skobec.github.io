<?php

/**
 * Программы
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 21.07.2015
 */
class Model_Program {

    public static function getDb() {
        return Prodom_Connector::getConnection('db_general');
    }

    /**
     * Возвращает программу по id
     * @param int $id 
     * @return Type_Program_Item[]
     */
    public static function findOne($id) {
        $db = self::getDb();

        $query = $db
                ->select()
                ->from('program')
                ->where('id =?', $id);
        $result = $db->query($query)->fetch();
        return $result;
    }

}
