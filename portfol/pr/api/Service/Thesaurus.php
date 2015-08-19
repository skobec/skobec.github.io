<?php

/**
 * Словарь для подсказок
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 11.08.2015
 */
class Service_Thesaurus extends Prodom_Api_Service {

    /**
     * Возвращает все подсказки
     * @return Type_Thesaurus_Item[]
     */
    public function findAll() {
        return Model_Thesaurus::findAll();
    }

}
