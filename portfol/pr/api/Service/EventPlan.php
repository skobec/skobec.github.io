<?php

/**
 * Description of Event_Plan
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 15.05.2015
 */
class Service_EventPlan extends Prodom_Api_Service {

    /**
     *
     * Возвращает массив с перечислением общего
     * количества денег по месяцам в течении одного года!
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int[] $years
     * 
     * @return object[]
     */
    public function getTotalMoneyByYear($years = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        return Model_EventPlan::getTotalMoneyByYear($years);
    }

    /**
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @param int[] $month
     * @return Type_Ministry_Plan_Money
     * @throws Exception
     */
    public function getTotalMoneyByMinistry($ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getTotalMoneyByMinistry($ministry_id, $years, $month);
    }

    /**
     * 
     * Возвращает общее количество денег по программе и министерству
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * @return type
     * @throws Exception
     */
    public function getTotalMoneyByProgram($program_id, $ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getTotalMoneyByProgram($program_id, $ministry_id, $years, $month);
    }

    /**
     * 
     * Возвращает общее количество денег по программе, министерству и типу работ
     * 
     * @param int $program_id
     * @param int $ministry_id
     * @param int $work_type_id
     * @param int[] $years
     * @param int $month
     * @return type
     * @throws Exception
     */
    public function getTotalMoneyByProgramAndWorkType($program_id, $ministry_id, $work_type_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getTotalMoneyByProgramAndWorkType($program_id, $ministry_id, $work_type_id, $years, $month);
    }

    /**
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @param int $work_type_id 
     * @param int $month
     * @return Type_Ministry_Plan_Money
     * @throws Exception
     */
    public function getTotalMoneyByMinistryAndWorkType($ministry_id, $work_type_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getTotalMoneyByMinistryAndWorkType($ministry_id, $work_type_id, $years, $month);
    }

    /**
     * 
     * @param int $ministry_id
     * @param int $economic_sphere_id
     * @param int[] $years 
     * @param int $month
     * @return Type_Ministry_Plan_Money
     * @throws Exception
     */
    public function getTotalMoneyByMinistryAndEconomicSphere($ministry_id, $economic_sphere_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getTotalMoneyByMinistryAndEconomicSphere($ministry_id, $economic_sphere_id, $years, $month);
    }

    /**
     * 
     * Возвращает массив с перечислением общего количества денег по министерствам
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Ministry_Plan_Money[]
     */
    public function getTotalMoneyByMinistries($years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getTotalMoneyByMinistries($years, $month);
    }

    /**
     * 
     * Список программ по министерствам
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Program_Plan_Money[]
     */
    public function getProgramsByMinistry($ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getProgramsByMinistry($ministry_id, $years, $month);
    }

    /**
     *
     * Список программ по годам внутри министерства + разделение по кварталам
     *
     * @param int $ministry_id
     * @param array $years
     *
     * @return array
     */
    public function getProgramsByMinistryAndQuarters($ministry_id, $years) {
        return Model_EventPlan::getProgramsByMinistryAndQuarters($ministry_id, $years);
    }

    /**
     * 
     * Список программ по министерствам и отрасли
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int $economic_sphere_id
     * @param int[] $years
     * @param int $month
     * @return Type_Program_Plan_Money[]
     */
    public function getProgramsByMinistryAndEconomicSphere($ministry_id, $economic_sphere_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getProgramsByMinistryAndEconomicSphere($ministry_id, $economic_sphere_id, $years, $month);
    }

    /**
     * 
     * Список программ по министерствам и типу работы
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int $work_type_id
     * @param int[] $years
     * @param int $month
     * @return Type_Program_Plan_Money[]
     */
    public function getProgramsByMinistryAndWorkType($ministry_id, $work_type_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getProgramsByMinistryAndWorkType($ministry_id, $work_type_id, $years, $month);
    }

    /**
     *
     * Список программ по министерствам и типу работы с разбивкой по месяцам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $ministry_id
     * @param int $work_type_id
     * @param int[] $years
     * @param int $month
     * @return Type_Program_Plan_Money[]
     */
    public function getProgramsByMinistryAndWorkTypeM($ministry_id, $work_type_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getProgramsByMinistryAndWorkTypeM($ministry_id, $work_type_id, $years, $month);
    }

    /**
     *
     * Список программ по министерствам и типу работы с разбивкой по месяцам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $ministry_id
     * @param int $work_type_id
     * @param array $years
     * @param int $month
     * @return Type_Program_Plan_Money[]
     */
    public function getProgramsByMinistryAndWorkTypeAndQuarters($ministry_id, $work_type_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getProgramsByMinistryAndWorkTypeAndQuarters($ministry_id, $work_type_id, $years, $month);
    }

    /**
     *
     * Список программ по министерствам и отраслям с разбивкой по месяцам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $ministry_id
     * @param int $economic_sphere_id
     * @param int[] $years
     * @param int $month
     * @return Type_Program_Plan_Money[]
     */
    public function getProgramsByMinistryAndEconomicSphereAndQuarters($ministry_id, $economic_sphere_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getProgramsByMinistryAndEconomicSphereAndQuarters($ministry_id, $economic_sphere_id, $years, $month);
    }

    /**
     * Список подпрограмм по программе
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * @return Type_Program_Plan_Money[]
     */
    public function getSubprograms($program_id, $ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getSubprograms($program_id, $ministry_id, $years, $month);
    }

    /**
     * Список подпрограмм по программе и виду работ
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $program_id
     * @param int $ministry_id
     * @param int $work_type_id
     * @param int[] $years
     * @param int $month
     * @return Type_Program_Plan_Money[]
     */
    public function getSubprogramsByWorkType($program_id, $ministry_id, $work_type_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getSubprogramsByWorkType($program_id, $ministry_id, $work_type_id, $years, $month);
    }

    /**
     * Список подпрограмм по программе и виду работ с разбивкой по кварталам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $program_id
     * @param int $ministry_id
     * @param int $work_type_id
     * @param array $years
     * @param int $month
     * @return Type_Program_Plan_Money[]
     */
    public function getSubprogramsByWorkTypeAndQuarters($program_id, $ministry_id, $work_type_id, $years = null, $month = null) {
        if (null === $years) {
            $years = date('Y');
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getSubprogramsByWorkTypeAndQuarters($program_id, $ministry_id, $work_type_id, $years, $month);
    }

    /**
     * Список подпрограмм по программе с разбивкой по месяцам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * @return Type_Program_Plan_Money[]
     */
    public function getSubprogramsMonthly($program_id, $ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getSubprogramsMonthly($program_id, $ministry_id, $years, $month);
    }

    /**
     * Список подпрограмм по программе с разбивкой по кварталам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $program_id
     * @param int $ministry_id
     * @param array $years
     * @param int $month
     * @return Type_Program_Plan_Money[]
     */
    public function getSubprogramsByProgramAndQuarters($program_id, $ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getSubprogramsByProgramAndQuarters($program_id, $ministry_id, $years, $month);
    }

    /**
     * 
     * Список разделов по министерствам
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * @return Type_Division_Plan_Money[]
     */
    public function getDivisionsByMinistry($ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getDivisionsByMinistry($ministry_id, $years, $month);
    }

    /**
     * 
     * Список подразделов раздела министерства
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $division_id
     * @param int $ministry_id
     * @param int[] $years
     * @return Type_Division_Plan_Money[]
     */
    public function getSubdivisions($division_id, $ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getSubdivisions($division_id, $ministry_id, $years, $month);
    }

    /**
     *
     * Возвращает список статей расходов по программе и министерству
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *  
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Spending_Plan_Money[]
     */
    public function getSpendings($program_id, $ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getSpendings($program_id, $ministry_id, $years, $month);
    }

    /**
     *
     * Возвращает список статей расходов по программе и министерству и типу работ
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $program_id
     * @param int $ministry_id
     * @param int $work_type
     * @param int[] $years
     * @param int $month
     *
     * @return Type_Spending_Plan_Money[]
     */
    public function getSpendingsByWorkType($program_id, $ministry_id, $work_type, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getSpendingsByWorkType($program_id, $ministry_id, $work_type, $years, $month);
    }

    /**
     *
     * Возвращает список статей расходов по программе и министерству и типу работ с разбивкой по кварталам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $program_id
     * @param int $ministry_id
     * @param int $work_type
     * @param array $years
     * @param int $month
     *
     * @return Type_Spending_Plan_Money[]
     */
    public function getSpendingsByProgramAndWorkTypeAndQuarters($program_id, $ministry_id, $work_type, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getSpendingsByProgramAndWorkTypeAndQuarters($program_id, $ministry_id, $work_type, $years, $month = null);
    }

    /**
     *
     * Возвращает список статей расходов по программе и министерству с разбивкой по кварталам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $program_id
     * @param int $ministry_id
     * @param array $years
     * @param int $month
     *
     * @return Type_Spending_Plan_Money[]
     */
    public function getSpendingsByProgramAndQuarters($program_id, $ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getSpendingsByProgramAndQuarters($program_id, $ministry_id, $years, $month);
    }

    /**
     * 
     * Возвращает список по экономическим отраслям по министерству
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Economy_Plan_Money[]
     */
    public function getEconomicSpheresByMinistry($ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getEconomicSpheresByMinistry($ministry_id, $years, $month);
    }

    /**
     *
     * Возвращает список по экономическим отраслям по министерству с разбивкой по месяцам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     *
     * @return Type_Economy_Plan_Money[]
     */
    public function getEconomicSpheresByMinistryAndQuarters($ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getEconomicSpheresByMinistryAndQuarters($ministry_id, $years, $month);
    }

    /**
     *
     * Возвращает список по экономическим отраслям по министерству с разбивкой по месяцам
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     *
     * @return Type_Economy_Plan_Money[]
     */
    public function getEconomicSpheresByMinistryWithMonths($ministry_id, $years = null, $month = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        if ((null !== $month) && !$this->isMonth($month)) {
            throw new Exception('Month index is out of range in ' . __CLASS__ . '::' . __METHOD__ . '() Line:' . __LINE__);
        }
        return Model_EventPlan::getEconomicSpheresByMinistryWithMonths($ministry_id, $years, $month);
    }

    /**
     * 
     * Список программ по годам внутри министерства
     * 
     * @param int $ministry_id
     * @param array $years
     * 
     * @return Type_Program_Plan_Percentage[]
     */
    public function getPercentage($ministry_id, $years) {
        return Model_EventPlan::getPercentage($ministry_id, $years);
    }

    /**
     *
     * Список программ по годам внутри министерства + разделение по месяцам
     *
     * @param int $ministry_id
     * @param array $years
     * @param int $limit
     *
     * @return Type_Program_Plan_Percentage[]
     */
    public function getPercentageByMonths($ministry_id, $years, $limit = null) {
        return Model_EventPlan::getPercentageByMonths($ministry_id, $years, $limit);
    }

    /**
     * 
     * Общее количество денег, запланирпованных по региону внутри министеорства в определенном году
     * 
     * @param int $region_id
     * @param int $ministry_id
     * @param int[] $years
     * 
     * @return Type_Region_Plan_Money
     * 
     */
    public function getTotalMoneyByRegion($region_id, $ministry_id, $years = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        return Model_EventPlan::getTotalMoneyByRegion($region_id, $ministry_id, $years);
    }

    /**
     * 
     * Общее количество денег, запланированных по региону внутри федерального округа в определенном году
     * 
     * @param int $district_id
     * @param int $ministry_id
     * @param int[] $years
     * 
     * @return Type_Region_Plan_Money
     * 
     */
    public function getTotalMoneyByDistrict($district_id, $ministry_id, $years = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        return Model_EventPlan::getTotalMoneyByDistrict($district_id, $ministry_id, $years);
    }

    /**
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @return Type_Region_Plan_Money[]
     */
    public function getByRegions($ministry_id, $years = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        return Model_EventPlan::getByRegions($ministry_id, $years);
    }

    /**
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @return Type_District_Plan_Money[]
     */
    public function getByDistricts($ministry_id, $years = null) {
        if (null === $years) {
            $years = [date('Y')];
        }
        return Model_EventPlan::getByDistricts($ministry_id, $years);
    }

    public function getRegionsByDistrict($district_id, $ministry_id, $years) {
        if (null === $years) {
            $years = [date('Y')];
        }
        return Model_EventPlan::getRegionsByDistrict($district_id, $ministry_id, $years);
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

}
