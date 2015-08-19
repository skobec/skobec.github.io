<?php

/**
 * Запланированные мероприятия
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 15.05.2015
 */
class Model_EventPlan {

    const BILLION = 1000000000;
    const MILLION = 1000000;

    public static function getDb() {
        return Prodom_Connector::getConnection('db_general');
    }

    /**
     *
     * Возвращает массив с перечислением общего
     * количества денег по месяцам в течении одного года
     * select event_month, SUM(money_amount) from "public".event_plan WHERE event_year=2015 group by event_month;
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @param int[] $years
     * @return object[]
     */
    public static function getTotalMoneyByYear($years) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['ep.event_month', 'sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->group('ep.event_month')
                ->order('ep.event_month');
        $queryResult = $db->query($query);
        $output = [];
        while ($row = $queryResult->fetch()) {
            $output[] = [self::getRussianMonthName($row->event_month), (float) $row->sum];
        }

        return $output;
    }

    /**
     * 
     * Возвращает общее количество денег по программе и министерству
     * 
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * @return type
     * @throws Exception
     */
    public static function getTotalMoneyByProgram($program_id, $ministry_id, $years = null, $month = null) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
                ->group(['prp.id'])
                ->where('prp.id=?', $program_id)
                ->orWhere('pr.id=?', $program_id);
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        $output = [];
        // http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        $row = $queryResult->fetch();
        $o = (object) [
                    'name' => $row->prp_title,
                    'y' => self::getFormattedMoney($row->sum, self::BILLION),
                    'ministry_id' => $ministry_id,
                    'type' => ['programs', 'divisions'],
        ];
        $output[] = $o;

        return $output;
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
    public static function getTotalMoneyByProgramAndWorkType($program_id, $ministry_id, $work_type_id, $years, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->where('ev.work_type_id = ?', $work_type_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
                ->group(['prp.id'])
                ->where('prp.id=?', $program_id);
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        $output = [];
        // http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        $row = $queryResult->fetch();
        $o = (object) [
                    'name' => $row->prp_title,
                    'y' => self::getFormattedMoney($row->sum, self::BILLION),
                    'ministry_id' => $ministry_id,
                    'type' => ['programs', 'divisions'],
        ];
        $output[] = $o;

        return $output;
    }

    /**
     * 
     * Возвращает количество денег по министерству
     * По умолчанию выдает за текущий год. Опционально можно передать год и месяц
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Ministry_Plan_Money
     */
    public static function getTotalMoneyByMinistry($ministry_id, $years, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->joinLeft(['mn' => 'ministry'], 'sp.ministry_id=mn.id', ['mn.title', 'mn_id' => 'mn.id'])
                ->where('mn.id = ?', $ministry_id)
                ->group(['mn.id',])
                ->order('mn.title');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        $output = [];
        // http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        $row = $queryResult->fetch();
        $o = (object) [
                    'name' => $row->title,
                    'y' => self::getFormattedMoney($row->sum, self::BILLION),
                    'ministry_id' => $row->mn_id,
                    'type' => ['programs', 'divisions'],
        ];
        $output[] = $o;

        return $output;
    }

    /**
     * 
     * Возвращает количество денег по министерству и типу работ
     * По умолчанию выдает за текущий год. Опционально можно передать год и месяц
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Ministry_Plan_Money
     */
    public static function getTotalMoneyByMinistryAndWorkType($ministry_id, $work_type, $years, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->where('ev.work_type_id = ?', $work_type)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->joinLeft(['mn' => 'ministry'], 'sp.ministry_id=mn.id', ['mn.title', 'mn_id' => 'mn.id'])
                ->where('mn.id = ?', $ministry_id)
                ->group(['mn.id',])
                ->order('mn.title');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        $output = [];
        // http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        $row = $queryResult->fetch();
        $o = (object) [
                    'name' => $row->title,
                    'y' => self::getFormattedMoney($row->sum, self::BILLION),
                    'ministry_id' => $row->mn_id,
                    'type' => ['programs', 'divisions'],
        ];
        $output[] = $o;

        return $output;
    }

    /**
     * 
     * Возвращает количество денег по министерству  отрасли
     * По умолчанию выдает за текущий год. Опционально можно передать год и месяц
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @param int[] $years
     * @param int $month
     * @param int $economic_sphere_id
     * 
     * @return Type_Ministry_Plan_Money
     */
    public static function getTotalMoneyByMinistryAndEconomicSphere($ministry_id, $economic_sphere_id, $years, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->where('ev.economic_sphere_id = ?', $economic_sphere_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->joinLeft(['mn' => 'ministry'], 'sp.ministry_id=mn.id', ['mn.title', 'mn_id' => 'mn.id'])
                ->where('mn.id = ?', $ministry_id)
                ->group(['mn.id',])
                ->order('mn.title');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        $output = [];
        // http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        $row = $queryResult->fetch();
        $o = (object) [
                    'name' => $row->title,
                    'y' => self::getFormattedMoney($row->sum, self::BILLION),
                    'ministry_id' => $row->mn_id,
                    'type' => ['programs', 'divisions'],
        ];
        $output[] = $o;

        return $output;
    }

    /**
     * 
     * Возвращает массив с перечислением общего количества денег по министерствам
     * По умолчанию выдает за текущий год. Опционально можно передать год и месяц
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Ministry_Plan_Money[]
     */
    public static function getTotalMoneyByMinistries($year, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->joinLeft(['mn' => 'ministry'], 'sp.ministry_id=mn.id', ['mn.title', 'mn_id' => 'mn.id'])
                ->group(['mn.id',])
                ->order('mn.title');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        $output = [];
        // http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = (object) [
                        'name' => $row->title,
                        'y' => (float) $row->sum,
                        'ministry_id' => $row->mn_id,
                        'type' => ['programs', 'divisions'],
            ];
            $output[] = $o;
        }
        return $output;
    }

    /**
    * 
    * Список программ по министерству
    * 
    * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
    * @param int $ministry_id
     * @param int[] $years
    * @param int $month
    * 
    * @return Type_Program_Plan_Money[]
    */
    public static function getProgramsByMinistry($ministry_id, $years, $month = null) {
        $db = self::getDb();
        $query = $db
            ->select()
            ->from(['ea' => 'event_action'], ['plan_sum' => 'SUM(ea.money_plan)', 'fact_sum' => 'SUM(ea.money_fact)'])
                ->where('ea.event_year IN (?)', $years)
            ->joinLeft(['ev' => 'event'], 'ea.event_id = ev.id', [])
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [])
            ->where('sp.ministry_id = ?', $ministry_id)
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [])
            ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [])
            ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
            ->group(['prp.id',])
            ->order('fact_sum DESC');
        if (null !== $month) {
            $query->where('ea.event_month = ?', $month);
        }

        $queryResult = $db->query($query);
        $plan = [];
        $fact = [];

        //http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = new stdClass;
            $o->name = trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $row->prp_title), '"');
            $o->y = self::getFormattedMoney($row->plan_sum, self::BILLION);
            $o->data = ['program_id' => $row->prp_id, 'ministry_id' => $ministry_id];
            $o->type = ['subprograms'];
            $plan[] = $o;

            $i = new stdClass();
            $i->name = trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $row->prp_title), '"');
            $i->y = self::getFormattedMoney($row->fact_sum, self::BILLION);
            $i->data = ['program_id' => $row->prp_id, 'ministry_id' => $ministry_id];
            $i->type = ['subprograms'];
            $fact[] = $i;
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
    *
    * Список программ по годам внутри министерства с разбивкой по кварталам
    * Должен вернуть массив объектов вида {
    *     name: 'Название программы',
    *     data: [502, 635, 809]
    * }
    * Будеи учитывать, что часть программ может быть в одном годе,
    * но отсутствовать в другом
    *
    * @param int $ministry_id
    * @param int[] $years
    *
    * @return Type_Program_Plan_Percentage[]
    */
    public static function getProgramsByMinistryAndQuarters($ministry_id, $years) {
        $db = self::getDb();
        $result = [];
        // Для начала нужно выбрать все возможные программы внутри министерства
        $query = $db
                ->select()
                ->distinct()
                ->from(['ep' => 'event_action'], ['prp.title', 'prp.id', 'money_plan' => 'SUM(ep.money_plan)', 'money_fact' => 'SUM(ep.money_fact)'])
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id'])
                ->group(['prp.id',])
                ->order('money_plan DESC');

        $queryResult = $db->query($query);

        // А потом посчитать за каждый год, перебирая все программы
        //
        $distinctPrograms = $queryResult->fetchAll();

        foreach ($distinctPrograms as $k => $program) {
            $program_data = self::getProgramsByQuarters($ministry_id, $years, $program->id);
            $program_data['name'] = trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $program->title), '"');
            $result[] = $program_data;
        }
        return $result;
    }

    /**
     * 
     * Список программ по министерству и отрасли
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * @param int $economic_sphere_id
     * 
     * @return Type_Program_Plan_Money[]
     */
    public static function getProgramsByMinistryAndEconomicSphere($ministry_id, $economic_sphere_id, $years, $month = null) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->where('ev.economic_sphere_id = ?', $economic_sphere_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
                ->group(['prp.id',])
                ->order('sum DESC');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        $output = [];
        //http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = new stdClass;
            $o->name = trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $row->prp_title), '"');
            $o->y = self::getFormattedMoney($row->sum, self::BILLION);
            $o->data = ['program_id' => $row->prp_id, 'ministry_id' => $ministry_id, 'economic_sphere_id' => $economic_sphere_id];
            $o->type = ['subprogramsByWorkType'];
            $output[] = $o;
        }
        return $output;
    }

    /**
     * 
     * Список программ по министерству и типу работ
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Program_Plan_Money[]
     */
    public static function getProgramsByMinistryAndWorkType($ministry_id, $work_type_id, $years, $month = null) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ea' => 'event_action'], ['plan_sum' => 'SUM(ea.money_plan)', 'fact_sum' => 'SUM(ea.money_fact)'])
                ->where('ea.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ea.event_id = ev.id', [''])
                ->where('ev.work_type_id = ?', $work_type_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
                ->group(['prp.id',])
                ->order('plan_sum DESC');
        if (null !== $month) {
            $query->where('ea.event_month =?', $month);
        }
        $queryResult = $db->query($query);

        $plan = [];
        $fact = [];

        //http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = new stdClass;
            $o->name = trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $row->prp_title), '"');
            $o->y = self::getFormattedMoney($row->plan_sum, self::BILLION);
            $o->data = ['program_id' => $row->prp_id, 'ministry_id' => $ministry_id, 'work_type_id' => $work_type_id];
            $o->type = ['subprogramsByWorkType'];
            $plan[] = $o;

            $i = new stdClass();
            $i->name = trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $row->prp_title), '"');
            $i->y = self::getFormattedMoney($row->fact_sum, self::BILLION);
            $i->data = ['program_id' => $row->prp_id, 'ministry_id' => $ministry_id, 'work_type_id' => $work_type_id];
            $i->type = ['subprogramsByWorkType'];
            $fact[] = $i;
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
     *
     * Список программ по министерству с учетом типа работы с разбивкой по месяцам
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Program_Plan_Money[]
     */
    public static function getProgramsByMinistryAndWorkTypeM($ministry_id, $work_type_id, $years, $month = null) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->distinct()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->where('ev.work_type_id = ?', $work_type_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
                ->group(['prp.id',])
                ->order('sum DESC');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);

        // А потом посчитать за каждый год, перебирая все программы
        //
        $distinctPrograms = $queryResult->fetchAll();
        $output = [];
        foreach ($distinctPrograms as $k => $program) {
            if (isset($colors[$k])) {
                $output[] = [
                    'name' => trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $program->prp_title), '"'),
                    'data' => self::getProgramsByMonthsAndWorkTypes($ministry_id, $work_type_id, $years, $program->prp_id),
                ];
            } else {
                $output[] = [
                    'name' => trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $program->prp_title), '"'),
                    'data' => self::getProgramsByMonthsAndWorkTypes($ministry_id, $work_type_id, $years, $program->prp_id)
                ];
            }
        }
        return $output;
    }

    /**
     *
     * Список программ по министерству с учетом типа работы с разбивкой по месяцам
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Program_Plan_Money[]
     */
    public static function getProgramsByMinistryAndEconomicSphereAndQuarters($ministry_id, $economic_sphere_id, $years, $month = null) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->distinct()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->where('ev.economic_sphere_id = ?', $economic_sphere_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
                ->group(['prp.id',])
                ->order('sum DESC');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);

        // А потом посчитать за каждый год, перебирая все программы
        //
        $distinctPrograms = $queryResult->fetchAll();
        $output = [];
        foreach ($distinctPrograms as $k => $program) {
            $output[] = [
                'name' => trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $program->prp_title), '"'),
                'data' => self::getProgramsByQuartersAndWorkTypes($ministry_id, $economic_sphere_id, $years, $program->prp_id)
            ];
        }
        return $output;
    }

    /**
     *
     * Список программ по министерству с учетом типа работы с разбивкой по месяцам
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param array $years
     * @param int $month
     * 
     * @return Type_Program_Plan_Money[]
     */
    public static function getProgramsByMinistryAndWorkTypeAndQuarters($ministry_id, $work_type_id, $years, $month = null) {
        $db = self::getDb();
        $result = [];
        $query = $db
                ->select()
                ->distinct()
                ->from(['ep' => 'event_action'], ['prp.title', 'prp.id', 'money_plan' => 'SUM(ep.money_plan)', 'money_fact' => 'SUM(ep.money_fact)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->where('ev.work_type_id = ?', $work_type_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
                ->group(['prp.id',])
                ->order('money_plan DESC');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);

        // А потом посчитать за каждый год, перебирая все программы
        //
        $distinctPrograms = $queryResult->fetchAll();
        foreach ($distinctPrograms as $k => $program) {
            $program_data = self::getProgramsByQuartersAndWorkTypes($ministry_id, $work_type_id, $years, $program->prp_id);
            $program_data['name'] = trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $program->prp_title), '"');
            $result[] = $program_data;
        }
        return $result;
    }

    /**
     * Список подпрограмм по программе
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Subprogram_Plan_Money[]
     */
    public static function getSubprograms($program_id, $ministry_id, $years, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ea' => 'event_action'], ['plan_sum' => 'SUM(ea.money_plan)', 'fact_sum' => 'SUM(ea.money_fact)'])
                ->where('ea.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ea.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', ['pr_id' => 'pr.id', 'pr_title' => 'pr.title'])
                ->where('pr.parent_program_id =?', $program_id)
                ->orWhere('pr.id =?', $program_id)
                ->group(['pr.id'])
                ->order('plan_sum DESC');
        if (null !== $month) {
            $query->where('ea.event_month =?', $month);
        }
        $queryResult = $db->query($query);

        $plan = [];
        $fact = [];

        //http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = new stdClass;
            $o->name = $row->pr_title;
            $o->y = self::getFormattedMoney($row->plan_sum, self::BILLION);
            $o->data = ['program_id' => $row->pr_id, 'ministry_id' => $ministry_id];
            $o->type = ['spendings'];
            $plan[] = $o;

            $i = new stdClass();
            $i->name = $row->pr_title;
            $i->y = self::getFormattedMoney($row->plan_sum, self::BILLION);
            $i->data = ['program_id' => $row->pr_id, 'ministry_id' => $ministry_id];
            $i->type = ['spendings'];
            $fact[] = $i;
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
     * Список подпрограмм по программе
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $yearss
     * @param int $month
     * 
     * @return Type_Subprogram_Plan_Money[]
     */
    public static function getSubprogramsByWorkType($program_id, $ministry_id, $work_type_id, $years, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ea' => 'event_action'], ['plan_sum' => 'SUM(ea.money_plan)', 'fact_sum' => 'SUM(ea.money_fact)'])
                ->where('ea.event_year = ?', $years)
                ->joinLeft(['ev' => 'event'], 'ea.event_id = ev.id', [''])
                ->where('ev.work_type_id=?', $work_type_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', ['pr_id' => 'pr.id', 'pr_title' => 'pr.title'])
                ->where('pr.parent_program_id =?', $program_id)
                ->group(['pr.id'])
                ->order('fact_sum DESC');
        if (null !== $month) {
            $query->where('ea.event_month =?', $month);
        }
        $queryResult = $db->query($query);

        $plan = [];
        $fact = [];
        //http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = new stdClass;
            $o->name = $row->pr_title;
            $o->y = self::getFormattedMoney($row->plan_sum, self::BILLION);
            $o->data = ['program_id' => $row->pr_id, 'ministry_id' => (int)$ministry_id, 'work_type_id' => (int)$work_type_id];
            $o->type = ['spendings1'];
            $plan[] = $o;

            $i = new stdClass();
            $i->name = $row->pr_title;
            $i->y = self::getFormattedMoney($row->fact_sum, self::BILLION);
            $i->data = ['program_id' => $row->pr_id, 'ministry_id' => $ministry_id, 'work_type_id' => $work_type_id];
            $i->type = ['spendings1'];
            $fact[] = $i;
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
     * Список подпрограмм по программе с разбивкой по кварталам
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Subprogram_Plan_Money[]
     */
    public static function getSubprogramsByWorkTypeAndQuarters($program_id, $ministry_id, $work_type_id, $years, $month) {
        $db = self::getDb();
        $result = [];
        $query = $db
                ->select()
                ->distinct()
                ->from(['ep' => 'event_action'], ['money_plan' => 'SUM(ep.money_plan)', 'money_fact' => 'SUM(ep.money_fact)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->where('ev.work_type_id=?', $work_type_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', ['pr_id' => 'pr.id', 'pr_title' => 'pr.title'])
                ->where('pr.parent_program_id =?', $program_id)
                ->group(['pr.id'])
                ->order('money_plan DESC');
//        dumpr($query->assemble());
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        // А потом посчитать за каждый год, перебирая все программы
        //
        $distinctPrograms = $queryResult->fetchAll();
        foreach ($distinctPrograms as $k => $program) {
            $program_data = self::getSubprogramsByWorkTypeByQuarters($program->pr_id, $ministry_id, $work_type_id, $years, $month);
            $program_data['name'] = $program->pr_title;
            $result[] = $program_data;
        }
        return $result;
    }

    /**
     * Список подпрограмм по программе
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Subprogram_Plan_Money[]
     */
    public static function getSubprogramsMonthly($program_id, $ministry_id, $years, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->distinct()
                ->from(['ep' => 'event_plan'], array('sum' => 'SUM(ep.money_amount)'))
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', array())
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', ['pr_id' => 'pr.id', 'pr_title' => 'pr.title'])
                ->where('pr.parent_program_id =?', $program_id)
                ->group(['pr.id'])
                ->order('sum DESC');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }

        $queryResult = $db->query($query);

        // А потом посчитать за каждый год, перебирая все программы
        //
        $distinctPrograms = $queryResult->fetchAll();
        $output = [];
        foreach ($distinctPrograms as $k => $program) {
            $output[] = [
                'name' => $program->pr_title,
                'data' => self::getSubprogramsByMonths($ministry_id, $years, $program->pr_id),
            ];
        }
        return $output;
    }

    /**
     * Список подпрограмм по программе с разбивкой по кварталам
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Subprogram_Plan_Money[]
     */
    public static function getSubprogramsByProgramAndQuarters($program_id, $ministry_id, $years, $month) {
        $db = self::getDb();
        $result = [];
        $query = $db
                ->select()
                ->distinct()
                ->from(['ep' => 'event_action'], ['money_plan' => 'SUM(ep.money_plan)', 'money_fact' => 'SUM(ep.money_fact)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', array())
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', ['pr_id' => 'pr.id', 'pr_title' => 'pr.title'])
                ->where('pr.parent_program_id =?', $program_id)
                ->orWhere('pr.id =?', $program_id)
                ->group(['pr.id'])
                ->order('money_plan DESC');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }

        $queryResult = $db->query($query);

        // А потом посчитать за каждый год, перебирая все программы
        //
        $distinctPrograms = $queryResult->fetchAll();
        foreach ($distinctPrograms as $k => $program) {
            $program_data = self::getSubprogramsByQuarters($ministry_id, $years, $program->pr_id);
            $program_data['name'] = $program->pr_title;
            $result[] = $program_data;
        }
        return $result;
    }

    /**
     * Список разделов по министерствам
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Division_Plan_Money[]
     */
    public static function getDivisionsByMinistry($ministry_id, $years, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['dv' => 'division'], 'sp.division_id = dv.id', [''])
                ->joinLeft(['dvp' => 'division'], 'dv.parent_division_id = dvp.id', ['dvp_id' => 'dvp.id', 'dvp_title' => 'dvp.title'])
                ->group(['dvp.id'])
                ->order('dvp.title');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        $output = [];
        //http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = new stdClass;
            $o->name = $row->dvp_title;
            $o->y = (float) $row->sum;
            $o->data = ['division_id' => $row->dvp_id, 'ministry_id' => $ministry_id];
            $o->type = ['subdivisions'];
            $output[] = $o;
        }
        return $output;
    }

    /**
     * 
     * Список подразделов раздела министерства
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $division_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Division_Plan_Money[]
     */
    public static function getSubdivisions($division_id, $ministry_id, $years, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['dv' => 'division'], 'sp.division_id = dv.id', ['dv_id' => 'dv.id', 'dv_title' => 'dv.title'])
                ->where('dv.parent_division_id = ?', $division_id)
                ->group(['dv.id'])
                ->order('dv.title');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        $output = [];
        //http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = new stdClass;
            $o->name = $row->dv_title;
            $o->y = (float) $row->sum;
            $o->data = ['division_id' => $row->dv_id, 'ministry_id' => $ministry_id];
            $o->type = null;
            $output[] = $o;
        }
        return $output;
    }

    /**
     *
     * Возвращает список статей расходов по программе и министерству
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *  
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Spending_Plan_Money[]
     */
    public static function getSpendings($program_id, $ministry_id, $years = null, $month = null) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ea' => 'event_action'], ['plan_sum' => 'SUM(ea.money_plan)', 'fact_sum' => 'SUM(ea.money_fact)'])
                ->where('ea.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ea.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', ['sp_title' => 'sp.title', 'sp_id' => 'sp.id'])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->where('te.program_id = ?', $program_id)
                ->group(['sp.id'])
                ->order('fact_sum DESC');
        if (null !== $month) {
            $query->where('ea.event_month =?', $month);
        }

        $queryResult = $db->query($query);
        $output = [];
        //http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = new stdClass;
            $o->name = $row->sp_title;
            $o->y = self::getFormattedMoney($row->plan_sum, self::BILLION);
            $o->data = ['spending_id' => $row->sp_id, 'program_id' => $program_id, 'ministry_id' => $ministry_id];
            $o->type = null;
            $plan[] = $o;

            $i = new stdClass();
            $i->name = $row->sp_title;
            $i->y = self::getFormattedMoney($row->fact_sum, self::BILLION);
            $i->data = ['spending_id' => $row->sp_id, 'program_id' => $program_id, 'ministry_id' => $ministry_id];
            $i->type = null;
            $fact[] = $i;
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
     *
     * Возвращает список статей расходов по программе и министерству и типу работ
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
    public static function getSpendingsByWorkType($program_id, $ministry_id, $work_type, $years = null, $month = null) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ea' => 'event_action'], ['plan_sum' => 'SUM(ea.money_plan)', 'fact_sum' => 'SUM(ea.money_fact)'])
                ->where('ea.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ea.event_id = ev.id', [''])
                ->where('ev.work_type_id=?', $work_type)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', ['sp_title' => 'sp.title', 'sp_id' => 'sp.id'])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->where('te.program_id = ?', $program_id)
                ->group(['sp.id'])
                ->order('fact_sum DESC');
        if (null !== $month) {
            $query->where('ea.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        $plan = [];
        $fact = [];

        //http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = new stdClass;
            $o->name = $row->sp_title;
            $o->y = self::getFormattedMoney($row->plan_sum, self::BILLION);
            $o->data = ['spending_id' => $row->sp_id, 'program_id' => $program_id, 'ministry_id' => $ministry_id];
            $o->type = null;
            $plan[] = $o;

            $i = new stdClass();
            $i->name = $row->sp_title;
            $i->y = self::getFormattedMoney($row->fact_sum, self::BILLION);
            $i->data = ['spending_id' => $row->sp_id, 'program_id' => $program_id, 'ministry_id' => $ministry_id];
            $i->type = null;
            $fact[] = $i;
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
     *
     * Возвращает список статей расходов по программе и министерству с разбивкой по кварталам
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $program_id
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * 
     * @return Type_Spending_Plan_Money[]
     */
    public static function getSpendingsByProgramAndQuarters($program_id, $ministry_id, $years = null, $month = null) {
        $db = self::getDb();
        $result = [];
        $query = $db

            ->select()
            ->distinct()
            ->from(['ep' => 'event_action'], ['money_plan' =>'SUM(ep.money_plan)','money_fact'=>'SUM(ep.money_fact)'])
            ->where('ep.event_year IN (?)', $years)
            ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', ['sp_title' => 'sp.title', 'sp_id' => 'sp.id'])
            ->where('sp.ministry_id = ?', $ministry_id)
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
            ->where('te.program_id = ?', $program_id)
            ->group(['sp.id'])
            ->order('money_plan DESC');

        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
//dumpr($query->assemble());
        $queryResult = $db->query($query);
        $distinctPrograms = $queryResult->fetchAll();
        foreach ($distinctPrograms as $spending) {
            $program_data = self::getSpendingsByQuarters($ministry_id, $years, $program_id, $spending->sp_id);
            $program_data['name'] = $spending->sp_title;
            $result[] = $program_data;
        }
        return $result;
    }

    /**
     * Возвращает список по экономическим отраслям по министерству
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * 
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * @return type
     * 
     * @return Type_Economy_Plan_Money[]
     */
    public static function getEconomicSpheresByMinistry($ministry_id, $years, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ea' => 'event_action'], ['plan_sum' => 'SUM(ea.money_plan)', 'fact_sum' => 'SUM(ea.money_fact)'])
                ->where('ea.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ea.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->joinLeft(['es' => 'economic_sphere'], 'ev.economic_sphere_id=es.id', ['es_title' => 'es.title', 'es_id' => 'es.id'])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->group(['es.id'])
                ->order('plan_sum DESC');
        if (null !== $month) {
            $query->where('ea.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        $plan = [];
        $fact = [];
        //http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = new stdClass;
            $o->name = $row->es_title;
            $o->y = self::getFormattedMoney($row->plan_sum, self::BILLION);
            $o->data = ['ministry_id' => $ministry_id];
            $o->type = null;
            $plan[] = $o;


            $i = new stdClass();
            $i->name = $row->es_title;
            $i->y = self::getFormattedMoney($row->fact_sum, self::BILLION);
            $i->data = ['ministry_id' => $ministry_id];
            $i->type = null;
            $fact[] = $i;
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
     * Возвращает список по экономическим отраслям по министерству c разбивкой по месяцам
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * @return type
     *
     * @return Type_Economy_Plan_Money[]
     */
    public static function getEconomicSpheresByMinistryAndQuarters($ministry_id, $years, $month) {
        $db = self::getDb();
        $result = [];
        $query = $db
                ->select()
                ->distinct()
                ->from(['ep' => 'event_action'], ['money_plan' => 'SUM(ep.money_plan)', 'money_fact' => 'SUM(ep.money_fact)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->joinLeft(['es' => 'economic_sphere'], 'ev.economic_sphere_id=es.id', ['es_title' => 'es.title', 'es_id' => 'es.id'])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->group(['es.id'])
                ->order('money_plan DESC');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }

        $queryResult = $db->query($query);
        $distinctPrograms = $queryResult->fetchAll();

        foreach ($distinctPrograms as $k => $program) {
            $program_data = self::getProgramsByQuartersAndEconomicSphere($ministry_id, $years, $program->es_id);
            $program_data['name'] = $program->es_title;
            $result[] = $program_data;
        }
        return $result;
    }
    /**
     *
     * Возвращает список по экономическим отраслям по министерству c разбивкой по месяцам
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     *
     * @param int $ministry_id
     * @param int[] $years
     * @param int $month
     * @return type
     *
     * @return Type_Economy_Plan_Money[]
     */
    public static function getEconomicSpheresByMinistryWithMonths($ministry_id, $years, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->distinct()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->joinLeft(['es' => 'economic_sphere'], 'ev.economic_sphere_id=es.id', ['es_title' => 'es.title', 'es_id' => 'es.id'])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->group(['es.id'])
                ->order('sum DESC');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }

        $queryResult = $db->query($query);
        $distinctPrograms = $queryResult->fetchAll();
        foreach ($distinctPrograms as $k => $program) {
            $output[] = [
                'name' => $program->es_title,
                'data' => self::getProgramsByMonthsAndEconomicSphere($ministry_id, $years, $program->es_id)
            ];
        }
        return $output;
    }

    /**
    * Список программ по годам внутри министерства
    * Должен вернуть массив объектов вида {
    *     name: 'Название программы',
    *     data: [502, 635, 809]
    * }
    * Будеи учитывать, что часть программ может быть в одном годе,
    * но отсутствовать в другом
    * 
    * @param int $ministry_id
    * @param array $years
    * 
    * @return Type_Program_Plan_Percentage[]
    */
    public static function getPercentage($ministry_id, $years) {
        $db = self::getDb();
        // Для начала нужно выбрать все возможные программы внутри министерства
        $query = $db
                ->select()
                ->distinct()
                ->from(['ep' => 'event_plan'], ['prp.*'])
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['']);

        $queryResult = $db->query($query);
        // А потом посчитать за каждый год, перебирая все программы
        // 
        $distinctPrograms = $queryResult->fetchAll();
        $output = [];
        foreach ($distinctPrograms as $program) {
            $output[] = [
                'name' => $program->title,
                'data' => self::getProgramsByYears($ministry_id, $years, $program->id),
            ];
        }
        return $output;
    }

    /**
     * Возвращает массив в котором перечислено количество денег по программе за каждый год
     * 
     * @param int $ministry_id
     * @param array $years
     * @param int $program_id
     * 
     * @return object[] 
     */
    private static function getProgramsByYears($ministry_id, $years, $program_id) {
        $db = self::getDb();
        $output = [];
        foreach ($years as $year) {
            $query = $db
                    ->select()
                    ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                    ->where('ep.event_year IN (?)', $years)
                    ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                    ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                    ->where('sp.ministry_id = ?', $ministry_id)
                    ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                    ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                    ->where('pr.parent_program_id = ?', $program_id)
                    ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', [''])
                    ->group(['prp.id',])
                    ->order('sum DESC');

            $queryResult = $db->query($query)->fetch();
            $output[] = !$queryResult ? 0 : self::getFormattedMoney((int) $queryResult->sum, self::BILLION);
            ;
        }
        return $output;
    }

    /**
     * Возвращает название месяца на русском языке
     * 
     * @param int $index
     * 
     * @return string
     */
    private static function getRussianMonthName($index) {
        $monthes = [
            'Январь',
            'Февраль',
            'Март',
            'Апрель',
            'Май',
            'Июнь',
            'Июль',
            'Август',
            'Сентябрь',
            'Октябрь',
            'Ноябрь',
            'Декабрь',
        ];
        $index = (int) $index - 1;
        return $monthes[$index];
    }

    private static function getFormattedMoney($value, $format = self::BILLION) {
        $money = $value / $format;
        return (float) round($money, 2);
    }

    /**
    *
    * Список программ по годам внутри министерства
    * Должен вернуть массив объектов вида {
    *     name: 'Название программы',
    *     data: [502, 635, 809]
    * }
    * Будеи учитывать, что часть программ может быть в одном годе,
    * но отсутствовать в другом
    *
    * @param int $ministry_id
    * @param int[] $years
    * @param int $limit
    *
    * @return Type_Program_Plan_Percentage[]
    */
    public static function getPercentageByMonths($ministry_id, $years, $limit) {
        $db = self::getDb();
        // Для начала нужно выбрать все возможные программы внутри министерства
        $query = $db
            ->select()
            ->distinct()
            ->from(['ep' => 'event_plan'], ['prp.*', 'sum' => 'SUM(ep.money_amount)'])
            ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
            ->where('sp.ministry_id = ?', $ministry_id)
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
            ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
            ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
            ->group(['prp.id',])
            ->order('sum DESC');
        $queryResult = $db->query($query);
        // А потом посчитать за каждый год, перебирая все программы
        $distinctPrograms = $queryResult->fetchAll();
        $output = [];
        foreach ($distinctPrograms as $k => $program) {
            if (!is_null($limit) && $k == ($limit))
                break;
            $plan_data = self::getProgramsByMonths($ministry_id, $years, $program->id);
            $output[] = [
                'name' => trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $program->prp_title), '"'),
                'data' => $plan_data,
            ];
        }
        return $output;
    }

    /**
    * Возвращает массив в котором перечислено количество денег по программе за каждый год
    * с разбивкой по месяцам
    *
    * @param int $ministry_id
    * @param array $years
    * @param int $program_id
    * 
    * @return array
    */
    private static function getProgramsByMonths($ministry_id, $years, $program_id) {
        $db = self::getDb();
        $output = [];
        $full_year = [];
        if (is_array($years)) {
            foreach ($years as $year) {
                $query = $db
                    ->select()
                    ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)', 'event_month' => 'ep.event_month'])
                    ->where('ep.event_year IN (?)', $years)
                    ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                    ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                    ->where('sp.ministry_id = ?', $ministry_id)
                    ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                    ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                    ->where('pr.parent_program_id = ?', $program_id)
                    ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', [''])
                    ->group(['prp.id'])
                    ->group(['ep.event_month'])
                    ->order('event_month ASC');
                $queryResult = $db->query($query);
                while ($row = $queryResult->fetch()) {
                    $output[$row->event_month] = !$row ? 0 : (int) $row->sum;
                }
                for ($i = 1; $i < 13; $i++) {
                    if (!isset($output[$i])) {
                        $output[$i] = 0;
                    }
                    $full_year[] = $output[$i];
                }
                $output = [];
            }
        } else {
            $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)', 'event_month' => 'ep.event_month'])
                ->where('ep.event_year = ?', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->where('pr.parent_program_id = ?', $program_id)
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', [''])
                ->group(['prp.id'])
                ->group(['ep.event_month'])
                ->order('event_month ASC');
            $queryResult = $db->query($query);
            while ($row = $queryResult->fetch()) {
                $output[$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->sum), self::BILLION);
            }
            for ($i = 1; $i < 13; $i++) {
                if (!isset($output[$i])) {
                    $output[$i] = 0;
                }
                $full_year[] = $output[$i] ? $output[$i] : null;
            }
            $output = [];
        }
        return $full_year;
    }

    /**
    *
    * Возвращает массив в котором перечислено количество денег по подпрограмме за каждый год
    * с разбивкой по месяцам
    *
    * @param int $ministry_id
    * @param int[] $years
    * @param int $program_id
    * 
    * @return object[]
    */
    private static function getSubprogramsByMonths($ministry_id, $years, $program_id) {
        $db = self::getDb();
        $output = [];
        $full_year = [];
        if (is_array($years)) {
            foreach ($years as $year) {
                $query = $db
                    ->select()
                    ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)', 'event_month' => 'ep.event_month'])
                    ->where('ep.event_year IN (?)', $years)
                    ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                    ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                    ->where('sp.ministry_id = ?', $ministry_id)
                    ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                    ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', ['pr_id' => 'pr.id', 'pr_title' => 'pr.title'])
                    ->where('pr.id =?', $program_id)
                    ->group(['pr.id'])
                    ->group(['ep.event_month'])
                    ->order('event_month ASC');
                $queryResult = $db->query($query);
                while ($row = $queryResult->fetch()) {
                    $output[$row->event_month] = !$row ? 0 : (int) $row->sum;
                }
                for ($i = 1; $i < 13; $i++) {
                    if (!isset($output[$i])) {
                        $output[$i] = 0;
                    }
                    $full_year[] = $output[$i];
                }
                $output = [];
            }
        } else {
            $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)', 'event_month' => 'ep.event_month'])
                ->where('ep.event_year = ?', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', ['pr_id' => 'pr.id', 'pr_title' => 'pr.title'])
                ->where('pr.id =?', $program_id)
                ->group(['pr.id'])
                ->group(['ep.event_month'])
                ->order('event_month ASC');
            $queryResult = $db->query($query);
            while ($row = $queryResult->fetch()) {
                $output[$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->sum), self::BILLION);
            }
            for ($i = 1; $i < 13; $i++) {
                if (!isset($output[$i])) {
                    $output[$i] = 0;
                }
                $full_year[] = $output[$i] ? $output[$i] : null;
            }
            $output = [];
        }
        return $full_year;
    }

    /**
    *
    * Возвращает массив в котором перечислено количество денег по подпрограмме за каждый год
    * с разбивкой по кварталам
    *
    * @param int $ministry_id
    * @param int[] $years
    * @param int $program_id
    * 
    * @return object[]
    */
    private static function getSubprogramsByQuarters($ministry_id, $years, $program_id) {
        $db = self::getDb();
        $plan = [];
        $fact = [];
        $query = $db
            ->select()
            ->from(['ep' => 'event_action'], ['money_plan' => 'SUM(ep.money_plan)', 'event_year' => 'ep.event_year', 'event_month' => 'ep.event_month', 'money_fact' => 'SUM(ep.money_fact)'])
            ->where('ep.event_year IN (?)', $years)
            ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
            ->where('sp.ministry_id = ?', $ministry_id)
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
            ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', ['pr_id' => 'pr.id', 'pr_title' => 'pr.title'])
            ->where('pr.id =?', $program_id)
            ->group(['pr.id'])
            ->group(['ep.event_month'])
            ->group(['event_year'])
            ->order('event_year ASC')
            ->order('event_month ASC');
        $queryResult = $db->query($query);
        while ($row = $queryResult->fetch()) {
            $plan[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_plan), self::BILLION);
            $fact[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_fact), self::BILLION);
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
    *
    * Возвращает массив в котором перечислено количество денег по сфере экономики за каждый год
    * с разбивкой по месяцам
    *
    * @param int $ministry_id
    * @param int[] $years
    * @param int $economic_sphere
    * 
    * @return array
    */
    private static function getProgramsByMonthsAndEconomicSphere($ministry_id, $years, $economic_sphere) {
        $db = self::getDb();
        $output = [];
        $full_year = [];
        if (is_array($years)) {
            foreach ($years as $year) {
                $query = $db
                    ->select()
                    ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)', 'event_month' => 'ep.event_month'])
                    ->where('ep.event_year IN (?)', $years)
                    ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                    ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                    ->joinLeft(['es' => 'economic_sphere'], 'ev.economic_sphere_id=es.id', ['es_title' => 'es.title', 'es_id' => 'es.id'])
                    ->where('sp.ministry_id = ?', $ministry_id)
                    ->where('es.id = ?', $economic_sphere)
                    ->group(['es.id'])
                    ->group(['ep.event_month'])
                    ->order('event_month ASC');
                $queryResult = $db->query($query);
                while ($row = $queryResult->fetch()) {
                    $output[$row->event_month] = !$row ? 0 : (int) $row->sum;
                }
                for ($i = 1; $i < 13; $i++) {
                    if (!isset($output[$i])) {
                        $output[$i] = 0;
                    }
                    $full_year[] = $output[$i];
                }
                $output = [];
            }
        } else {
            $query = $db
                    ->select()
                    ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)', 'event_month' => 'ep.event_month'])
                    ->where('ep.event_year = ?', $years)
                    ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                    ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                    ->joinLeft(['es' => 'economic_sphere'], 'ev.economic_sphere_id=es.id', ['es_title' => 'es.title', 'es_id' => 'es.id'])
                    ->where('sp.ministry_id = ?', $ministry_id)
                    ->where('es.id = ?', $economic_sphere)
                    ->group(['es.id'])
                    ->group(['ep.event_month'])
                    ->order('event_month ASC');

            $queryResult = $db->query($query);
            while ($row = $queryResult->fetch()) {
                $output[$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->sum), self::BILLION);
            }
            for ($i = 1; $i < 13; $i++) {
                if (!isset($output[$i])) {
                    $output[$i] = 0;
                }
                $full_year[] = $output[$i] ? $output[$i] : null;
            }
            $output = [];
        }
        return $full_year;
    }

    /**
    *
    * Возвращает массив в котором перечислено количество денег по сфере экономики за каждый год
    * с разбивкой по кварталам
    *
    * @param int $ministry_id
    * @param int[] $years
    * @param int $economic_sphere
    * 
    * @return object[]
    */
    private static function getProgramsByQuartersAndEconomicSphere($ministry_id, $years, $economic_sphere) {
        $db = self::getDb();
        $plan = [];
        $fact = [];
        $query = $db
                ->select()
                ->from(['ep' => 'event_action'], ['money_plan' => 'SUM(ep.money_plan)', 'event_year' => 'ep.event_year', 'event_month' => 'ep.event_month', 'money_fact' => 'SUM(ep.money_fact)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->joinLeft(['es' => 'economic_sphere'], 'ev.economic_sphere_id=es.id', ['es_title' => 'es.title', 'es_id' => 'es.id'])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->where('es.id = ?', $economic_sphere)
                ->group(['es.id'])
                ->group(['ep.event_month'])
                ->group(['event_year'])
                ->order('event_year ASC')
                ->order('event_month ASC');

        $queryResult = $db->query($query);

        while ($row = $queryResult->fetch()) {
            $plan[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_plan), self::BILLION);
            $fact[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_fact), self::BILLION);
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
     * Возвращает массив в котором перечислено количество денег по программе за каждый год
     * с разбивкой по месяцам с учетом типов
     *
     * @param int $ministry_id
     * @param int $work_type_id
     * @param int[] $years
     * @param int $program_id
     * 
     * @return object[]
     */
    private static function getProgramsByMonthsAndWorkTypes($ministry_id, $work_type_id, $years, $program_id) {
        $db = self::getDb();
        $output = [];
        $full_year = [];
        if (is_array($years)) {
            foreach ($years as $year) {
                $query = $db
                    ->select()
                    ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)', 'event_month' => 'ep.event_month'])
                    ->where('ep.event_year IN (?)', $years)
                    ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                    ->where('ev.work_type_id = ?', $work_type_id)
                    ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                    ->where('sp.ministry_id = ?', $ministry_id)
                    ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                    ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                    ->where('pr.parent_program_id = ?', $program_id)
                    ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', [''])
                    ->group(['prp.id'])
                    ->group(['ep.event_month'])
                    ->order('event_month ASC');
                $queryResult = $db->query($query);
                while ($row = $queryResult->fetch()) {
                    $output[$row->event_month] = !$row ? 0 : (int) $row->sum;
                }
                for ($i = 1; $i < 13; $i++) {
                    if (!isset($output[$i])) {
                        $output[$i] = 0;
                    }
                    $full_year[] = $output[$i];
                }
                $output = [];
            }
        } else {
            $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)', 'event_month' => 'ep.event_month'])
                ->where('ep.event_year = ?', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->where('ev.work_type_id = ?', $work_type_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->where('pr.parent_program_id = ?', $program_id)
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', [''])
                ->group(['prp.id'])
                ->group(['ep.event_month'])
                ->order('event_month ASC');
            $queryResult = $db->query($query);
            while ($row = $queryResult->fetch()) {
                $output[$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->sum), self::BILLION);
            }
            for ($i = 1; $i < 13; $i++) {
                if (!isset($output[$i])) {
                    $output[$i] = 0;
                }
                $full_year[] = $output[$i] ? $output[$i] : null;
            }
            $output = [];
        }
        return $full_year;
    }

    /**
     * Возвращает массив в котором перечислено количество денег по программе за каждый год
     * с разбивкой по месяцам с учетом типов
     *
     * @param int $ministry_id
     * @param int $work_type_id
     * @param int[] $years
     * @param int $program_id
     * 
     * @return object[]
     */
    private static function getProgramsByQuartersAndWorkTypes($ministry_id, $work_type_id, $years, $program_id) {
        $db = self::getDb();
        $plan = [];
        $fact = [];
        $query = $db
                ->select()
                ->from(['ep' => 'event_action'], ['money_plan' => 'SUM(ep.money_plan)', 'event_year' => 'ep.event_year', 'event_month' => 'ep.event_month', 'money_fact' => 'SUM(ep.money_fact)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->where('ev.work_type_id = ?', $work_type_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->where('pr.parent_program_id = ?', $program_id)
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', [''])
                ->group(['prp.id'])
                ->group(['event_year'])
                ->order('event_year ASC')
                ->group(['ep.event_month'])
                ->order('event_month ASC');

        $queryResult = $db->query($query);
        while ($row = $queryResult->fetch()) {
            $plan[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_plan), self::BILLION);
            $fact[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_fact), self::BILLION);
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
     * Общее количество денег, запланированных по региону внутри министерства в определенном году
     * 
     * @param int $region_id
     * @param int $ministry_id
     * @param int[] $years
     * 
     * @return Type_Region_Plan_Money
     */
    public static function getTotalMoneyByRegion($region_id, $ministry_id, $years) {
        $db = self::getDb();
        $query = $db
            ->select()
            ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
            ->where('ep.event_year IN (?)', $years)
            ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
            ->where('ev.region_id=?', $region_id)
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
            ->joinLeft(['mn' => 'ministry'], 'sp.ministry_id=mn.id', ['mn.title', 'mn_id' => 'mn.id'])
            ->where('mn.id = ?', $ministry_id)
            ->group(['mn.id',])
            ->order('mn.title');
        $queryResult = $db->query($query);
        $output = [];
        $row = $queryResult->fetch();
        $o = (object) [
                    'y' => $row ? self::getFormattedMoney($row->sum, self::BILLION) : 0,
        ];
        $output[] = $o;
        return $output;
    }

    /**
    * Общее количество денег, запланированных по региону внутри министерства в определенном году по программным расходам
    * 
    * @param int $region_id
    * @param int $ministry_id
    * @param int[] $years
    * 
    * @return Type_Region_Plan_Money
    */
    public static function getTotalMoneyByRegionProgrammic($region_id, $ministry_id, $years) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
                ->where('ep.event_year IN (?)', $years)
                ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
                ->where('ev.region_id=?', $region_id)
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->joinLeft(['mn' => 'ministry'], 'sp.ministry_id=mn.id', ['mn.title', 'mn_id' => 'mn.id'])
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->where('pr.parent_program_id <> ?',3)
                ->where('mn.id = ?', $ministry_id)
                ->group(['mn.id',])
                ->order('mn.title');
        $queryResult = $db->query($query);
        $output = [];
        $row = $queryResult->fetch();
        $o = (object) [
                    'y' => $row ? self::getFormattedMoney($row->sum, self::BILLION) : 0,
        ];
        $output[] = $o;
        return $output;
    }
    
    /**
     * Общее количество денег, запланированных по региону внутри министерства в определенном году по непрограммным расходам
     * 
     * @param int $region_id
     * @param int $ministry_id
     * @param int[] $years
     * 
     * @return Type_Region_Plan_Money
     */
    public static function getTotalMoneyByRegionNonprogrammic($region_id, $ministry_id, $years) {
        $db = self::getDb();
        $query = $db
            ->select()
            ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
            ->where('ep.event_year IN (?)', $years)
            ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
            ->where('ev.region_id=?', $region_id)
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
            ->joinLeft(['mn' => 'ministry'], 'sp.ministry_id=mn.id', ['mn.title', 'mn_id' => 'mn.id'])
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
            ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
            ->where('pr.parent_program_id = ?',3)
            ->where('mn.id = ?', $ministry_id)
            ->group(['mn.id',])
            ->order('mn.title');
        $queryResult = $db->query($query);
        $output = [];
        $row = $queryResult->fetch();
        $o = (object) [
            'y' => $row ? self::getFormattedMoney($row->sum, self::BILLION) : 0,
        ];
        $output[] = $o;

        return $output;
    }

    /**
     * Общее количество денег, запланированных по федеральному округу внутри министерства в определенном году
     * 
     * @param int $district_id
     * @param int $ministry_id
     * @param int[] $years
     * 
     * @return Type_Region_Plan_Money
     */
    public static function getTotalMoneyByDistrict($district_id, $ministry_id, $years) {
        $db = self::getDb();
        $query = $db
            ->select()
            ->from(['ep' => 'event_plan'], ['sum' => 'SUM(ep.money_amount)'])
            ->where('ep.event_year IN (?)', $years)
            ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
            ->joinLeft(['re' => 'region'], 'ev.region_id = re.id', [])
            ->joinLeft(['rep' => 'region'], 're.parent_id = rep.id', [])
            ->where('rep.id =?', $district_id)
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
            ->joinLeft(['mn' => 'ministry'], 'sp.ministry_id=mn.id', [''])
            ->where('mn.id = ?', $ministry_id)
            ->group(['rep.id'])
            ->order('rep.title');
        $queryResult = $db->query($query);
        $output = [];
        $row = $queryResult->fetch();
        $o = (object) [
            'y' => $row ? self::getFormattedMoney($row->sum, self::BILLION) : 0,
        ];
        $output[] = $o;
        return $output;
    }

    /**
     * Список регионов
     * 
     * @param int $ministry_id
     * @param int[] $years
     * 
     * @return Type_Region_Plan_Money[]
     */
    public static function getByRegions($ministry_id, $years) {
        $output = [];
        $regions = Model_Region::findAll();
        foreach ($regions as $region) {
            $output[] = (object) [
                        'id' => $region->id,
                        'title' => $region->title,
                        'value' => self::getTotalMoneyByRegion($region->id, $ministry_id, $years)[0]->y,
                        'value_programmic' => self::getTotalMoneyByRegionProgrammic($region->id, $ministry_id, $years)[0]->y,
                        'value_nonprogrammic' => self::getTotalMoneyByRegionNonprogrammic($region->id, $ministry_id, $years)[0]->y,
                        'hc-key' => $region->hc_key,
            ];
        }
        return $output;
    }

    /**
     * Список по федеральным округам
     * 
     * @param int $ministry_id
     * @param int[] $years
     * 
     * @return Type_District_Plan_Money[]
     */
    public static function getByDistricts($ministry_id, $years) {
        $output = [];
        $districts = Model_Region::findDistricts();
        foreach ($districts as $district) {
            $output[] = (object) [
                        'id' => $district->id,
                        'title' => $district->title,
                        'value' => self::getTotalMoneyByDistrict($district->id, $ministry_id, $years)[0]->y,
                        'regions' => self::getRegionsByDistrict($district->id, $ministry_id, $years)
            ];
        }
        return $output;
    }

    public static function getRegionsByDistrict($district_id, $ministry_id, $years) {
        $output = [];
        $regions = Model_Region::findAllByDistrict($district_id, $ministry_id);
        foreach ($regions as $region) {
            $output[] = (object) [
                'title' => $region->title,
                'value' => self::getTotalMoneyByRegion($region->id, $ministry_id, $years)[0]->y,
                'hc-key' => $region->hc_key,
                'district_id' => $district_id,
            ];
        }
        return $output;
    }

    /**
    * Возвращает массив в котором перечислено количество денег по программе за каждый год
    * с разбивкой по кварталам
    *
    * @param int $ministry_id
    * @param int[] $years
    * @param int $program_id
    * 
    * @return object[]
    */
    private static function getProgramsByQuarters($ministry_id, $years, $program_id) {
        $db = self::getDb();
        $output = [];
        $plan = [];
        $fact = [];
        $year = [];
        $plan_quarter4 = $plan_quarter3 = $plan_quarter2 = $plan_quarter1 = 0; // инициализация переменных для хранения сумм по кварталам
        $fact_quarter4 = $fact_quarter3 = $fact_quarter2 = $fact_quarter1 = 0;
        $query = $db
            ->select()
            ->from(['ep' => 'event_action'], ['money_plan' => 'SUM(ep.money_plan)', 'money_fact' => 'SUM(ep.money_fact)', 'event_year' => 'ep.event_year', 'event_month' => 'ep.event_month'])
            ->where('ep.event_year IN (?)', $years)
            ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
            ->where('sp.ministry_id = ?', $ministry_id)
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
            ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
            ->where('pr.parent_program_id = ?', $program_id)
            ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', [''])
            ->group(['prp.id'])
            ->group(['ep.event_month'])
            ->group(['event_year'])
            ->order('event_year ASC')
            ->order('event_month ASC');
        $queryResult = $db->query($query);
        while ($row = $queryResult->fetch()) {
            $plan[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_plan), self::BILLION);
            $fact[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_fact), self::BILLION);
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
     * Возвращает массив в котором перечислено количество денег по программе за каждый год
     * с разбивкой по кварталам
     *
     * @param int $ministry_id
     * @param array $years
     * @param int $program_id
     * @param int $spending_id
     * 
     * @return object[]
     */
    private static function getSpendingsByQuarters($ministry_id, $years, $program_id, $spending_id) {
        $db = self::getDb();
        $plan = [];
        $fact = [];

        $query = $db
            ->select()
            ->from(['ep' => 'event_action'], ['money_plan' => 'SUM(ep.money_plan)','event_year'=>'ep.event_year', 'event_month' => 'ep.event_month','money_fact' => 'SUM(ep.money_fact)'])
            ->where('ep.event_year IN (?)', $years)
            ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', ['sp_title' => 'sp.title', 'sp_id' => 'sp.id'])
            ->where('sp.ministry_id = ?', $ministry_id)
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
            ->where('te.program_id = ?', $program_id)
            ->where('sp.id = ?', $spending_id)
            ->group(['sp.id'])
            ->group(['ep.event_month'])
            ->group(['event_year'])
            ->order('event_year ASC')
            ->order('event_month ASC');
        $queryResult = $db->query($query);
        while ($row = $queryResult->fetch()) {
            $plan[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_plan), self::BILLION);
            $fact[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_fact), self::BILLION);
        }
        return ['plan'=>$plan,'fact'=>$fact];
    }

    /**
    *  Список подпрограмм по программе с разбивкой по кварталам
    *
    * @param int $program_id
    * @param int $ministry_id
    * @param int $work_type_id
    * @param int[] $years
    * @param int $month
    * 
    * @return array
    */
    private static function getSubprogramsByWorkTypeByQuarters($program_id, $ministry_id, $work_type_id, $years, $month) {
        $db = self::getDb();
        $plan = [];
        $fact = [];
        $query = $db
            ->select()
            ->from(['ep' => 'event_action'], ['money_plan' => 'SUM(ep.money_plan)', 'event_year' => 'ep.event_year', 'event_month' => 'ep.event_month', 'money_fact' => 'SUM(ep.money_fact)'])
            ->where('ep.event_year IN (?)', $years)
            ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
            ->where('ev.work_type_id=?', $work_type_id)
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
            ->where('sp.ministry_id = ?', $ministry_id)
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
            ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', ['pr_id' => 'pr.id', 'pr_title' => 'pr.title'])
            ->where('pr.id =?', $program_id)
            ->group(['pr.id'])
            ->group(['event_year'])
            ->order('event_year ASC')
            ->group(['ep.event_month'])
            ->order('event_month ASC');
        $queryResult = $db->query($query);
        while ($row = $queryResult->fetch()) {
            $plan[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_plan), self::BILLION);
            $fact[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_fact), self::BILLION);
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

    /**
    *
    * Возвращает список статей расходов по программе и министерству и типу работ с разбивкой по кварталам
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
    public static function getSpendingsByProgramAndWorkTypeAndQuarters($program_id, $ministry_id, $work_type, $years = null, $month = null) {
        $db = self::getDb();
        $result = [];
        $query = $db
            ->select()
            ->distinct()
            ->from(['ep' => 'event_action'], ['money_plan' => 'SUM(ep.money_plan)', 'money_fact' => 'SUM(ep.money_fact)'])
            ->where('ep.event_year IN (?)', $years)
            ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
            ->where('ev.work_type_id=?', $work_type)
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', ['sp_title' => 'sp.title', 'sp_id' => 'sp.id'])
            ->where('sp.ministry_id = ?', $ministry_id)
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
            ->where('te.program_id = ?', $program_id)
            ->group(['sp.id'])
            ->order('money_plan DESC');
        if (null !== $month) {
            $query->where('ep.event_month =?', $month);
        }
        $queryResult = $db->query($query);
        // А потом посчитать за каждый год, перебирая все программы
        $distinctPrograms = $queryResult->fetchAll();
        foreach ($distinctPrograms as $k => $program) {
            $program_data = self::getSpendingsByWorkTypeByQuarters($program_id, $program->sp_id, $ministry_id, $work_type, $years, $month);
            $program_data['name'] = $program->sp_title;
            $result[] = $program_data;
        }
        return $result;
    }

    /**
    * Список подпрограмм по программе с разбивкой по кварталам
    *
    * @param int $program_id
    * @param int $spending_id
    * @param int $ministry_id
    * @param int $work_type_id
    * @param int[] $years
    * @param int $month
    * 
    * @return object[]
    */
    private static function getSpendingsByWorkTypeByQuarters($program_id, $spending_id, $ministry_id, $work_type_id, $years, $month) {
        $db = self::getDb();
        $plan = [];
        $fact = [];
        $query = $db
            ->select()
            ->from(['ep' => 'event_action'], ['money_plan' => 'SUM(ep.money_plan)', 'event_year' => 'ep.event_year', 'event_month' => 'ep.event_month', 'money_fact' => 'SUM(ep.money_fact)'])
            ->where('ep.event_year IN (?)', $years)
            ->joinLeft(['ev' => 'event'], 'ep.event_id = ev.id', [''])
            ->where('ev.work_type_id=?', $work_type_id)
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', ['sp_title' => 'sp.title', 'sp_id' => 'sp.id'])
            ->where('sp.ministry_id = ?', $ministry_id)
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
            ->where('te.program_id = ?', $program_id)
            ->where('sp.id = ?', $spending_id)
            ->group(['sp.id'])
            ->group(['event_year'])
            ->order('event_year ASC')
            ->group(['ep.event_month'])
            ->order('event_month ASC');
        $queryResult = $db->query($query);
        while ($row = $queryResult->fetch()) {
            $plan[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_plan), self::BILLION);
            $fact[$row->event_year][$row->event_month] = !$row ? 0 : self::getFormattedMoney(round($row->money_fact), self::BILLION);
        }
        return ['plan' => $plan, 'fact' => $fact];
    }

}
