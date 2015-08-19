<?php

/**
 * Регионы
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 11.08.2015
 */
class Model_Thesaurus
{

    public static function getDb()
    {
        return Prodom_Connector::getConnection('db_general');
    }

    /**
     * Возвращает все подсказки
     * @return Type_Thesaurus_Item[]
     */
    public static function findAll()
    {
        $db = self::getDb();

        $query = $db
            ->select()
            ->from('thesaurus');
        $result = $db->query($query)->fetchAll();
        return $result;
    }
}
