<?php

/**
* Api виджетов
*/
class Service_Widget extends Prodom_Api_Service {

    /**
    * Возвращает список виджетов для рабочего стола граждан текущего пользователя
    */
    public function getList() {
        // формирование списка виджетов
        $widget_filter_list = [
            new Type_Widget_Filter(['widget_id' => 130, 'is_expanded' => true, 'include_data' => true, 'years' => [2015],]),
            new Type_Widget_Filter(['widget_id' => 80, 'is_expanded' => true, 'include_data' => true, 'years' => [2015],]),
            new Type_Widget_Filter(['widget_id' => 91, 'is_expanded' => false, 'include_data' => true, 'years' => [2015],]),
            new Type_Widget_Filter(['widget_id' => 97, 'is_expanded' => false, 'include_data' => true, 'years' => [2015],]),
            new Type_Widget_Filter(['widget_id' => 140, 'is_expanded' => false, 'include_data' => true, 'years' => [2015],]),
        ];
        $widget_list = [];
        foreach ($widget_filter_list as $filter) {
            $widget_data = $this->getData($filter);
            $widget_list[] = $widget_data;
        }
        return $widget_list;
    }

    /**
    * Возвращает список виджетов для персонального рабочего стола текущего пользователя
    * 
    * @param string $user_session_id
    */
    public function getPersonalList($user_session_id) {
        // формирование списка виджетов
        $widget_filter_list = [
            new Type_Widget_Filter(['widget_id' => 80, 'is_expanded' => false, 'include_data' => true, 'years' => [2015],]),
        ];
        $widget_list = [];
        foreach ($widget_filter_list as $filter) {
            $widget_id = $filter->widget_id;
            $widget_data = $this->getData($widget_id, $filter);
            $widget_list[] = $widget_data;
        }
        return $widget_list;
    }

    /**
    * 
    * Возвращает виджет по id
    * 
    * @param string $user_session_id
    * @param int $id
    *
    * @return object[]
    */
    public function getCustomWidget($user_session_id, $id) {
        // формирование списка виджетов
        $widget_filter_list = [
            new Type_Widget_Filter(['widget_id' => $id, 'is_expanded' => false, 'include_data' => true, 'years' => [2015],]),
        ];
        $widget_list = [];
        foreach ($widget_filter_list as $filter) {
            $widget_data = $this->getData($filter);
            $widget_list[] = $widget_data;
        }
        return $widget_list;
    }

    /**
    * Данные для карты на главной
    * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
    */
    public function getMainMap() {
        $filter = new Type_Widget_Filter(['widget_id' => 130, 'is_expanded' => true, 'include_data' => true, 'years' => [2015],]);
        return $this->getData($filter)->chart_data;
    }

    /**
     * Возвращает данные для указанного виджета
     * @author sciner
     * @since 2015-06-25
     *
     * @param Type_Widget_Filter $filter
     * 
     * @return Type_Widget_Data
     */
    function getData($filter) {
        return Model_Widget::get($filter);
    }

}
