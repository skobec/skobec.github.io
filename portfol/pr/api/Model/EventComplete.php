<?php

/**
 * Фактические мероприятия
 *
 * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
 * @since 18.05.2015
 */
class Model_EventComplete {

    const BILLION = 1000000000;
    const MILLION = 1000000;

    public static function getDb() {
        return Prodom_Connector::getConnection('db_general');
    }

    /**
     *
     * Возвращает массив с перечислением общего
     * количества денег по месяцам в течении одного года
     *  SELECT EXTRACT(MONTH FROM dt) AS "month", SUM(ec.money_amount) AS "sum" FROM "event_complete" AS "ec" WHERE (EXTRACT(YEAR FROM dt) = 2015) GROUP BY "month" ORDER BY "month" ASC 
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @param int $year
     * @return array
     */
    public static function getTotalMoneyByYear($year) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ec' => 'event_complete'], ['month' => 'EXTRACT(MONTH FROM dt)', 'sum' => 'SUM(ec.money_amount)'])
                ->where('EXTRACT(YEAR FROM dt) = ?', $year)
                ->group('month')
                ->order('month');
        $stmt = $db->query($query);
        $output = [];
        while ($row = $stmt->fetch()) {
            $output[] = [self::getRussianMonthName($row->month), (float) $row->sum];
        }

        return $output;
    }

    /**
     * 
     * Возвращает количество денег по министерству
     * По умолчанию выдает за текущий год. Опционально можно передать год и месяц
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @param int $year
     * @param int $month
     * 
     * @return Type_Ministry_Plan_Money
     */
    public static function getTotalMoneyByMinistry($ministry_id, $year, $month) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ec' => 'event_complete'], ['sum' => 'SUM(ec.money_amount)'])
                ->where('EXTRACT(YEAR FROM ec.dt) = ?', $year)
                ->joinLeft(['ev' => 'event'], 'ec.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->joinLeft(['mn' => 'ministry'], 'sp.ministry_id=mn.id', ['mn.title', 'mn_id' => 'mn.id'])
                ->where('mn.id = ?', $ministry_id)
                ->group(['mn.id',])
                ->order('mn.title');
        if (null !== $month) {
            $query->where('EXTRACT(MONTH FROM ec.dt) = ?', $month);
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
     * Список программ по министерству
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @param int $ministry_id
     * @param int $year
     * @param int $month
     * @return Type_Program_Complete_Money[]
     */
    public static function getProgramsByMinistry($ministry_id, $year, $month = null) {
        $db = self::getDb();
        $query = $db
                ->select()
                ->from(['ec' => 'event_complete'], ['sum' => 'SUM(ec.money_amount)'])
                 ->where('EXTRACT(YEAR FROM ec.dt) = ?', $year)
                ->joinLeft(['ev' => 'event'], 'ec.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
                ->group(['prp.id',])
                ->order('sum DESC');
        if (null !== $month) {
            $query->where('EXTRACT(MONTH FROM ec.dt) = ?', $month);
        }

        $queryResult = $db->query($query);
        $output = [];
        //http://stackoverflow.com/questions/8514457/set-additional-data-to-highcharts-series
        while ($row = $queryResult->fetch()) {
            $o = new stdClass;
            $o->name = trim(str_replace('ГОСУДАРСТВЕННАЯ ПРОГРАММА РОССИЙСКОЙ ФЕДЕРАЦИИ ', '', $row->prp_title), '"');
            $o->y = self::getFormattedMoney($row->sum, self::BILLION);
            $o->data = ['program_id' => $row->prp_id, 'ministry_id' => $ministry_id];
            $o->type = ['subprograms'];
            $output[] = $o;
        }
        return $output;
    }

    /**
     *
     * Список программ по министерству с разбивкой по месяцам (факт. данные)
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @param int $ministry_id
     * @param int $year
     * @param int $month
     * @return Type_Program_Complete_Money[]
     */
    public static function getProgramsByMinistryMonthly($ministry_id, $year, $month = null) {
        $db = self::getDb();
        $query = $db
            ->select()
            ->distinct()
            ->from(['ec' => 'event_complete'], ['sum' => 'SUM(ec.money_amount)'])
            ->where('EXTRACT(YEAR FROM ec.dt) = ?', $year)
            ->joinLeft(['ev' => 'event'], 'ec.event_id = ev.id', [''])
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
            ->where('sp.ministry_id = ?', $ministry_id)
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
            ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
            ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
            ->group(['prp.id',])
            ->order('sum DESC');

        if (null !== $month) {
            $query->where('EXTRACT(MONTH FROM ec.dt) = ?', $month);
        }
        $queryResult = $db->query($query);
        // А потом посчитать за каждый год, перебирая все программы
        //
        $distinctPrograms = $queryResult->fetchAll();
        $output = [];
        foreach ($distinctPrograms as $k => $program) {
            $output[] = [
                'name' => $program->prp_title,
                'data' => self::getProgramsByMonths($ministry_id, $year, $program->prp_id),
            ];
        }
        return $output;
    }

    /**
     *
     * Список программ по министерству с разбивкой по кварталам (факт. данные)
     *
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @param int $ministry_id
     * @param int $year
     * @param int $month
     * @return Type_Program_Complete_Money[]
     */
    public static function getProgramsByMinistryAndQuarters($ministry_id, $year, $month = null) {
        $db = self::getDb();
        $query = $db
            ->select()
            ->distinct()
            ->from(['ec' => 'event_complete'], ['sum' => 'SUM(ec.money_amount)'])
            ->where('EXTRACT(YEAR FROM ec.dt) = ?', $year)
            ->joinLeft(['ev' => 'event'], 'ec.event_id = ev.id', [''])
            ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
            ->where('sp.ministry_id = ?', $ministry_id)
            ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
            ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
            ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', ['prp_id' => 'prp.id', 'prp_title' => 'prp.title'])
            ->group(['prp.id',])
            ->order('sum DESC');

        if (null !== $month) {
            $query->where('EXTRACT(MONTH FROM ec.dt) = ?', $month);
        }
        $queryResult = $db->query($query);
        // А потом посчитать за каждый год, перебирая все программы
        //
        $distinctPrograms = $queryResult->fetchAll();
        $output = [];
        foreach ($distinctPrograms as $k => $program) {
            $output[] = [
                'name' => $program->prp_title,
                'data' => self::getProgramsByQuarters($ministry_id, $year, $program->prp_id),
            ];
        }
        return $output;
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
        $db = self::getDb();
        $output = [];
        $full_year = [];
        if (is_array($years)) {
            foreach ($years as $year) {
                $query = $db
                    ->select()
                    ->from(['ec' => 'event_complete'], ['sum' => 'SUM(ec.money_amount)','event_month'=>'EXTRACT(MONTH FROM ec.dt)'])
                    ->where('EXTRACT(YEAR FROM ec.dt) = ?', $year)
                    ->joinLeft(['ev' => 'event'], 'ec.event_id = ev.id', [''])
                    ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                    ->where('sp.ministry_id = ?', $ministry_id)
                    ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                    ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                    ->where('pr.parent_program_id = ?', $program_id)
                    ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', [''])
                    ->group(['prp.id'])
                    ->group(['event_month'])
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
                ->from(['ec' => 'event_complete'], ['sum' => 'SUM(ec.money_amount)','event_month'=>'EXTRACT(MONTH FROM ec.dt)'])
                ->where('EXTRACT(YEAR FROM ec.dt) = ?', $years)
                ->joinLeft(['ev' => 'event'], 'ec.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->where('pr.parent_program_id = ?', $program_id)
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', [''])
                ->group(['prp.id'])
                ->group(['event_month'])
                ->order('event_month ASC');
//dumpr($query->assemble());
            $queryResult = $db->query($query);
            while ($row = $queryResult->fetch()) {
                $output[$row->event_month] = isset($row) && !empty($row)?self::getFormattedMoney(round($row->sum), self::BILLION): 0;
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
     * Возвращает массив в котором перечислено количество денег по программе за каждый год
     * с разбивкой по месяцам
     *
     * @param int $ministry_id
     * @param array $years
     * @param int $program_id
     * @return array
     */
    public static function getProgramsByQuarters ($ministry_id, $years, $program_id) {
        $db = self::getDb();
        $output = [];
        $full_year = [];
        $quarter4 = $quarter3 = $quarter2 = $quarter1 = 0;//инициализация переменных для хранения сумм по кварталам

        if (is_array($years)) {
            foreach ($years as $year) {
                $query = $db
                    ->select()
                    ->from(['ec' => 'event_complete'], ['sum' => 'SUM(ec.money_amount)','event_month'=>'EXTRACT(MONTH FROM ec.dt)'])
                    ->where('EXTRACT(YEAR FROM ec.dt) = ?', $year)
                    ->joinLeft(['ev' => 'event'], 'ec.event_id = ev.id', [''])
                    ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                    ->where('sp.ministry_id = ?', $ministry_id)
                    ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                    ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                    ->where('pr.parent_program_id = ?', $program_id)
                    ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', [''])
                    ->group(['prp.id'])
                    ->group(['event_month'])
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
                ->from(['ec' => 'event_complete'], ['sum' => 'SUM(ec.money_amount)','event_month'=>'EXTRACT(MONTH FROM ec.dt)'])
                ->where('EXTRACT(YEAR FROM ec.dt) = ?', $years)
                ->joinLeft(['ev' => 'event'], 'ec.event_id = ev.id', [''])
                ->joinLeft(['sp' => 'spending'], 'ev.spending_id=sp.id', [''])
                ->where('sp.ministry_id = ?', $ministry_id)
                ->joinLeft(['te' => 'target_expend'], 'sp.target_expend_id = te.id', [''])
                ->joinLeft(['pr' => 'program'], 'te.program_id = pr.id', [''])
                ->where('pr.parent_program_id = ?', $program_id)
                ->joinLeft(['prp' => 'program'], 'pr.parent_program_id = prp.id', [''])
                ->group(['prp.id'])
                ->group(['event_month'])
                ->order('event_month ASC');

            $queryResult = $db->query($query);
            while ($row = $queryResult->fetch()) {
                $output[$row->event_month] = isset($row) && !empty($row)?self::getFormattedMoney(round($row->sum), self::BILLION): 0;
            }
            for ($i = 1; $i < 13; $i++) {
                if (!isset($output[$i])) {
                    $output[$i] = 0;
                }
                switch ($i){
                    case $i > 9:
                        $quarter4 += $output[$i];
                        break;
                    case  $i > 6:
                        $quarter3 += $output[$i];
                        break;
                    case  $i > 3:
                        $quarter2 += $output[$i];
                        break;
                    default:
                        $quarter1 += $output[$i];
                        break;
                }
            }

            $full_year[] = $quarter1?$quarter1:null;
            $full_year[] = $quarter2?$quarter2:null;
            $full_year[] = $quarter3?$quarter3:null;
            $full_year[] = $quarter4?$quarter4:null;
        }
        return $full_year;
    }

    /**
     * Возвращает название месяца на русском языке
     * @param integer $index
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

}
