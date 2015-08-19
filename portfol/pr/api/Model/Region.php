<?php

/**
 * Регионы
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 15.05.2015
 */
class Model_Region {

    public static function getDb() {
        return Prodom_Connector::getConnection('db_general');
    }

    /**
     * Возвращает все регионы
     * @return Type_Region_Item[]
     */
    public static function findAll() {
        $db = self::getDb();

        $query = $db
                ->select()
                ->from('region')
                ->where('parent_id IS NOT NULL')
                ->order('title');
        $result = $db->query($query)->fetchAll();
        return $result;
    }

    /**
     * Возвращает федеральные округа
     * @param bool $hideForeign
     * @return Type_Region_Item[]
     */
    public static function findDistricts($hideForeign = true) {
        $db = self::getDb();

        $query = $db
                ->select()
                ->from('region')
                ->where('parent_id IS NULL')
                ->order('title');
        if ($hideForeign) {
            $query->where('id != 10');
        }
        $result = $db->query($query)->fetchAll();
        return $result;
    }

    /**
     * 
     * Возвращает регионы по федеральному округу
     * 
     * @param int $district_id
     * @return Type_Region_Item[]
     */
    public static function findAllByDistrict($district_id) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from('region')
                ->where('parent_id=?', $district_id)
                ->order('title');
        $result = $db->query($query)->fetchAll();
        return $result;
    }

}
