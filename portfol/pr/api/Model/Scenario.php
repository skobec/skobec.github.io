<?php

/**
 * Сценарии
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 15.06.2015
 */
class Model_Scenario {

    public static function getDb() {
        return Prodom_Connector::getConnection('db_general');
    }

    /**
     *
     * Возвращает все сценарии
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @return array
     */
    public static function getAll() {

        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['sc' => 'scenario']);
        $queryResult = $db->query($query);
        $result = $queryResult->fetchAll(Zend_Db::FETCH_ASSOC);

        return self::buildTree($result, null);
    }

    /**
     *
     * @param array $elements
     * @param int $parent_id
     * @return array
     */
    private static function buildTree(&$elements, $parent_id = null) {

        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parent_id) {
                $children = self::buildTree($elements, $element['id']);

                if ($children) {
                    $element['children'] = $children;
                }
                $branch[$element['id']] = $element;
            }
        }
        return $branch;
    }

    /**
     * 
     * Возвращает сценарий по id
     * 
     * @param int $id
     * 
     * @return Type_Scenario_Item
     */
    public static function findOne($id) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['sc' => 'scenario'])
                ->where('id = ?', $id);
        $queryResult = $db->query($query);
        return $queryResult->fetch();
    }

    /**
     * 
     * Поиск по имени сценария
     * @param string $name
     * @return Type_Scenario_Item[]
     */
    public static function search($name) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['sc' => 'scenario'])
                ->order('sc.title')
                ->where('sc.title ILIKE ?', '%' . $name . '%');
        $queryResult = $db->query($query);
        return $queryResult->fetchAll();
    }

}
