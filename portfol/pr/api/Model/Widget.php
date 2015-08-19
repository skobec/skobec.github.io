<?php

class Model_Widget {

    /**
    * @author sciner
    * @since 2015-07-13
    * 
    * @param Type_Widget_Filter $filter Фильтр
    * 
    * @return {Type_Widget_Data|Type_Widget_Data_Map}
    */
    static function get($filter) {
//        $xAxis = App_Mpt::generateQuartersArray($filter->years);
        $xAxis = [];
        $scenario = Service::Scenario()->findOne($filter->widget_id);
        if($scenario->type == 'map') {
            $widget = new Type_Widget_Data_Map;
        } else {
            $widget = new Type_Widget_Data;
        }
        $widget->id = $filter->widget_id;
        $widget->title = $scenario->title;
        $widget->icon_class = $scenario->icon_class;
        $widget->type = $scenario->type;
        $widget->x_axis = $xAxis;
        $widget->is_expanded = $filter->is_expanded;
        if($filter->include_data) {
            $widgetName = 'Widget_' . $filter->widget_id;
            self::$widgetName($widget, $filter);
        }
        return $widget;
    }

    /**
    * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
    * @since 2015-06-18
    * 
    * @param Type_Widget $widget Объект виджета
    * @param Type_Widget_Filter $filter Фильтр
    * 
     * @return bool
    */
    static function Widget_91($widget, $filter) {
        $model = Service::EventPlan();
        $widget->chart_data = $model->getEconomicSpheresByMinistry(1, $filter->years);
        $widget->other_data = $model->getEconomicSpheresByMinistryAndQuarters(1, $filter->years);
        $widget->money = $model->getTotalMoneyByMinistry(1);
        $widget->params = ['ministry_id' => 1];
        return true;
    }

    /**
    * Список программ по годам внутри министерства с разбивкой по кварталам
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-06-18
     *
     * @param Type_Widget $widget Объект виджета
     * @param Type_Widget_Filter $filter Фильтр
     * 
     * @return bool
     */
    static function Widget_80($widget, $filter) {
        $ministry_id = 1;
        $model = Service::EventPlan();
        $widget->type = 'stacked_area';
        $widget->chart_data = $model->getProgramsByMinistry($ministry_id, $filter->years);
        $widget->other_data = $model->getProgramsByMinistryAndQuarters($ministry_id, $filter->years);
        $widget->money = $model->getTotalMoneyByMinistry($ministry_id);
        $widget->params = ['ministry_id' => $ministry_id];
        return true;
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-06-18
     * 
     * @param Type_Widget $widget Объект виджета
     * @param Type_Widget_Filter $filter Фильтр
     * 
     * @return bool
     */
    static function Widget_97($widget, $filter) {
        $model = Service::EventPlan();
        $widget->chart_data = $model->getProgramsByMinistryAndWorkType(1, 2);
        $widget->other_data = $model->getProgramsByMinistryAndWorkTypeAndQuarters(1, 2);
        $widget->money = $model->getTotalMoneyByMinistryAndWorkType(1, 2);
        $widget->params = ['ministry_id' => 1];
        return true;
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-06-18
     * 
     * @param Type_Widget $widget Объект виджета
     * @param Type_Widget_Filter $filter Фильтр
     * 
     * @return bool
     */
    static function Widget_99($widget, $filter) {
        $model = Service::EventComplete();
        $widget->chart_data = $model->getProgramsByMinistry(1);
        $widget->other_data = $model->getProgramsByMinistryAndQuarters(1);
        $widget->money = $model->getTotalMoneyByMinistry(1);
        $widget->params = ['ministry_id' => 1];
        return true;
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-06-19
     * 
     * @param Type_Widget $widget Объект виджета
     * @param Type_Widget_Filter $filter
     * 
     * @return bool
     */
    static function Widget_140($widget, $filter) {
        $model = Service::EventPlan();
        $widget->chart_data = $model->getSubprograms(4, 1);
        $widget->other_data = $model->getSubprogramsByProgramAndQuarters(4, 1,$filter->years);
        $widget->money = $model->getTotalMoneyByProgram(4, 1);
        $widget->params = ['ministry_id' => 1];
        return true;
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-06-19
     * @todo Добавить механизм определения региона пользователя
     * 
     * @param Type_Widget $widget Объект виджета
     * @param Type_Widget_Filter $filter Фильтр
     * 
     * @return bool
     */
    static function Widget_130($widget, $filter) {
        $model = Service::EventPlan();
        $widget->money = $model->getTotalMoneyByMinistry(1);
        $widget->chart_data = $model->getByRegions(1, $filter->years);
        $widget->chart_data_districts = $model->getByDistricts(1, $filter->years);
        return true;
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-06-19
     * 
     * @param Type_Widget $widget Объект виджета
     * @param Type_Widget_Filter $filter Фильтр
     * 
     * @return bool
     */
    static function Widget_92($widget, $filter) {
        $model = Service::EventPlan();
        $widget->chart_data = $model->getProgramsByMinistryAndEconomicSphere(1, 11);
        $widget->other_data = $model->getProgramsByMinistryAndEconomicSphereAndQuarters(1, 11);
        $widget->money = $model->getTotalMoneyByMinistryAndEconomicSphere(1, 11);
        $widget->params = ['ministry_id' => 1];
        return true;
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-06-19
     * 
     * @param Type_Widget $widget Объект виджета
     * @param Type_Widget_Filter $filter Фильтр
     * 
     * @return bool
     */
    static function Widget_93($widget, $filter) {
        $model = Service::EventPlan();
        $widget->chart_data = $model->getSubprograms(2, 1);
        $widget->other_data = $model->getSubprogramsByProgramAndQuarters(2, 1);
        $widget->money = $model->getTotalMoneyByProgram(2, 1);
        $widget->params = ['ministry_id' => 1];
        return true;
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-07-08
     * 
     * @param Type_Widget $widget Объект виджета
     * @param Type_Widget_Filter $filter Фильтр
     * 
     * @return bool
     */
    static function Widget_95($widget, $filter) {
        $model = Service::EventPlan();
        $widget->chart_data = $model->getSubprograms(7, 1);
        $widget->other_data = $model->getSubprogramsByProgramAndQuarters(7, 1);
        $widget->money = $model->getTotalMoneyByProgram(7, 1);
        $widget->params = ['ministry_id' => 1];
        return true;
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-07-08
     * 
     * @param Type_Widget $widget Объект виджета
     * @param Type_Widget_Filter $filter Фильтр
     * 
     * @return bool
     */
    static function Widget_96($widget, $filter) {
        $model = Service::EventPlan();
        $widget->chart_data = $model->getSubprograms(10, 1);
        $widget->other_data = $model->getSubprogramsByProgramAndQuarters(10, 1);
        $widget->money = $model->getTotalMoneyByProgram(10, 1);
        $widget->params = ['ministry_id' => 1];
        return true;
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-07-08
     * 
     * @param Type_Widget $widget Объект виджета
     * @param Type_Widget_Filter $filter Фильтр
     * 
     * @return bool
     */
    static function Widget_139($widget, $filter) {
        $model = Service::EventPlan();
        $widget->chart_data = $model->getSubprogramsByWorkType(4, 1, 2);
        $widget->other_data = $model->getSubprogramsByWorkTypeAndQuarters(4, 1, 2);
        $widget->money = $model->getTotalMoneyByProgramAndWorkType(4, 1, 2);
        $widget->params = ['ministry_id' => 1];
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-07-08
     * 
     * @param Type_Widget $widget Объект виджета
     * @param Type_Widget_Filter $filter Фильтр
     * 
     * @return bool
     */
    static function Widget_135($widget, $filter) {
        $model = Service::EventPlan();
        $widget->chart_data = $model->getSubprograms(41, 1);
        $widget->other_data = $model->getSubprogramsByProgramAndQuarters(41, 1);
        $widget->money = $model->getTotalMoneyByProgram(41, 1);
        $widget->params = ['ministry_id' => 1];
        return true;
    }

}
