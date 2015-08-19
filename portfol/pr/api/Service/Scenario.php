<?php

/**
 * Description of Scenario
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 15.06.2015
 */
class Service_Scenario extends Prodom_Api_Service {

    /**
     *
     * Возвращает все сценарии
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @return Type_Scenario_Item[]
     */
    public function getAll() {
        return Model_Scenario::getAll();
    }

    /**
     *
     * Возвращает cценарий по id
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $id
     * 
     * @return Type_Scenario_Item
     */
    public function findOne($id) {
        return Model_Scenario::findOne($id);
    }

    /**
     * 
     * Массив сценариев, по уровням вложенности, с указанием родителя
     * @return array
     */
    public function getFormattedList() {
        $scenarios = $this->getAll();
        $scenario1 = [];
        $scenario2 = [];
        $scenario3 = [];
        foreach ($scenarios as $scenario) {
            $scenario1[] = ['id' => $scenario['id'], 'name' => $scenario['title']];
            if ($scenario['children']) {
                foreach ($scenario['children'] as $child) {
                    $scenario2[] = ['parent' => $scenario['id'], 'name' => $child['title'], 'id' => $child['id']];
                    if ($child['children']) {
                        foreach ($child['children'] as $child2) {
                            $scenario3[] = ['parent' => $child['id'], 'name' => $child2['title'], 'id' => $child2['id']];
                        }
                    }
                }
            }
        }
        return[
            'scenario1' => $scenario1,
            'scenario2' => $scenario2,
            'scenario3' => $scenario3
        ];
    }

    /**
     * 
     * Поиск по имени сценария
     * @param string $name
     * @return Type_Scenario_Item[]
     */
    public function search($name) {
        return Model_Scenario::search($name);
    }

}
