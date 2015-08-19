<?php

/**
* Сервис админки
* @author: artem
* @since 13.08.15 8:44
*/
class Service_Admin extends Prodom_Api_Service {

    /**
     * Возвращает все регионы c учетом фильтра и пагинации
     *
     * @param array $filter
     * @param Type_Paginator $paginator Объект пагинатора
     * @return Type_Admin_Pages_List[]
     *
     * */
    public function findAllByFilter($filter, $paginator) {
        return Model_Admin::findAllByFilter($filter, $paginator);
    }

}