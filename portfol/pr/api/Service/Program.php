<?php

/**
 * Программы
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 21.07.2015
 */
class Service_Program extends Prodom_Api_Service {

    /**
     * Возвращает программу по id
     * @param int $id 
     * @return Type_Program_Item[]
     */
    public function findOne($id) {
        return Model_Program::findOne($id);
    }

}
