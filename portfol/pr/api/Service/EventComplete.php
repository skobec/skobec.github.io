<?php

/**
 * Description of EventComplete
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 18.05.2015
 */
class Service_EventComplete extends Prodom_Api_Service {

    /**
     *
     * Возвращает массив с перечислением общего
     * количества денег по месяцам в течении одного года
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 18.05.2015
     * @param int $year
     * @return array
     */
    public function getTotalMoneyByYear($year = null) {
        if (null === $year) {
            $year = date('Y');
        }
        return Model_EventComplete::getTotalMoneyByYear($year);
    }

    /**
     * 
     * @param int $ministry_id
     * @param int $year
     * @param int $month
     * @return Type_Ministry_Plan_Money
     * @throws Exception
     */
    public function getTotalMoneyByMinistry($ministry_id, $year = null, $month = null) {
        if (null === $year) {
            $year = date('Y');
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventComplete::getTotalMoneyByMinistry($ministry_id, $year, $month);
    }
    
    /**
     * 
     * Список программ по министерствам
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int $year
     * @param int $month
     * @return Type_Program_Complete_Money[]
     */
    public function getProgramsByMinistry($ministry_id, $year = null, $month = null) {
        if (null === $year) {
            $year = date('Y');
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventComplete::getProgramsByMinistry($ministry_id, $year, $month);
    }

    /**
     * 
     * Проверка, является ли допустимым индекс месяца
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $month_index
     * @return bool
     */
    private function isMonth($month_index) {
        return in_array($month_index, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
    }

    /**
     *
     * Список программ по министерствам с разбивкой по месяцам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $ministry_id
     * @param int $year
     * @param int $month
     * @return Type_Program_Complete_Money[]
     */
    public function getProgramsByMinistryMonthly($ministry_id, $year = null, $month = null) {
        if (null === $year) {
            $year = date('Y');
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventComplete::getProgramsByMinistryMonthly($ministry_id, $year, $month);
    }

    /**
     *
     * Список программ по министерствам с разбивкой по кварталам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $ministry_id
     * @param int $year
     * @param int $month
     * @return Type_Program_Complete_Money[]
     */
    public function getProgramsByMinistryAndQuarters($ministry_id, $year = null, $month = null) {
        if (null === $year) {
            $year = date('Y');
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventComplete::getProgramsByMinistryAndQuarters($ministry_id, $year, $month);
    }


    /**
     *
     * Возвращает массив в котором перечислено количество денег по программе за каждый год
     * с разбивкой по месяцам
     *
     * @param int $ministry_id
     * @param array $years
     * @param int $program_id
     * @return array
     */
    public static function getProgramsByMonths($ministry_id, $years, $program_id) {
        return Model_EventComplete::getProgramsByMonths($ministry_id, $years, $program_id);
    }
}
