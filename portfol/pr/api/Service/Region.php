<?php

/**
 * Description of Region
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 22.06.2015
 */
class Service_Region extends Prodom_Api_Service {

    /**
     * Возвращает все регионы
     * @return Type_Region_Item[]
     */
    public function findAll() {
        return Model_Region::findAll();
    }

    /**
     * Возвращает все федеральные округа
     * @param string  $test Description
     * @param boolean $hideForeign 
     * @return Type_Region_Item[]
     */
    public function findDistricts($hideForeign = true) {      
        return Model_Region::findDistricts($hideForeign);
    }

    /**
     * Возвращает все федеральные округа
     * @return Type_Region_Item[]
     */
    public function findAllByDistrict($district_id) {
        return Model_Region::findAllByDistrict($district_id);
    }

}
