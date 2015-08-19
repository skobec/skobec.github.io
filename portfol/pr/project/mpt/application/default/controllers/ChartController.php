<?php

class ChartController extends Zend_Controller_Action {

    function init() {
        $this->_helper->Init->init();
        if (!$this->user->isLogged()) {
            // Functions::redirect('//'.Settings::get('root_domain').'/login/');
        }
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-05-15
     */
    public function indexAction() {

        Plugin_Menu::setActive('mpt-menu', 'chart');
    }

    /**
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-05-20
     */
    public function otherAction() {
        Plugin_Menu::setActive('mpt-menu', 'chart/other');
        //Данные по запланированным мероприятиям
        $monthPlanMoney = Service::EventPlan()->getTotalMoneyByYear();
        $this->view->monthPlanMoney = json_encode($monthPlanMoney, JSON_UNESCAPED_UNICODE);

        //Данные по произведенным мероприятиям
        $monthCompleteMoney = Service::EventComplete()->getTotalMoneyByYear();
        $this->view->monthCompleteMoney = json_encode($monthCompleteMoney, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 
     * @ajax
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @since 2015-05-19
     */
    public function getdataAction() {
        $type = $this->getRequest()->getParam('type', 'ministries');
        $params = $this->getRequest()->getParam('data', []);
//        $model = Service::EventPlan()->_cache(3600);
        $model = Service::EventPlan();
        $settings = $other_data = [];

        switch ($type) {
            case 'ministries':
                $title = 'Распределение по министерствам';
                $data = $model->getTotalMoneyByMinistries(2015);
                array_map(function($row) {
                    $row->data = ['ministry_id' => $row->ministry_id];
                }, $data);
                break;
            case 'programsByWorkType':
                $title = 'Какая часть средств из бюджета МПТ выделяется в этом году для  финансирования НИОКР?';
                $data = $model->getProgramsByMinistryAndWorkType($params['ministry_id'], $params['work_type_id'], date('Y'));
                break;
            case 'programs':
                $title = 'Расходы бюджета Минпромторга по госпрограммам в 2015 году';
                $data = $model->getProgramsByMinistry($params['ministry_id'], (int)date('Y'));
                break;
            case 'subprograms':
                $title = 'Распределение по подпрограммам внутри программы министерства';
                $data = $model->getSubprograms($params['program_id'], $params['ministry_id']);
                $other_data  = $model->getSubprogramsByProgramAndQuarters($params['program_id'], $params['ministry_id'],2015);
                break;
            case 'subprogramsByWorkType':
                $title = 'Распределение по подпрограммам внутри программы министерства и виду работ';
                $data = $model->getSubprogramsByWorkType($params['program_id'], $params['ministry_id'], $params['work_type_id']);
                $other_data  = $model->getSubprogramsByWorkTypeAndQuarters($params['program_id'], $params['ministry_id'], $params['work_type_id']);
                break;
            
            case 'divisions':
                $title = 'Распределение по разделам внутри министерства';
                $data = $model->getDivisionsByMinistry($params['ministry_id']);
                break;
            case 'subdivisions':
                $title = 'Распределение по подразделам внутри раздела министерства';
                $data = $model->getSubdivisions($params['division_id'], $params['ministry_id']);
                break;
            case 'spendings':
                $title = 'Распределение по статьям подпрограммы внутри программы министерства';
                $data = $model->getSpendings($params['program_id'], $params['ministry_id'],[2015]);
                $other_data = $model->getSpendingsByProgramAndQuarters($params['program_id'], $params['ministry_id'],[2015]);
                break;
            case 'spendings1':
                $title = 'Распределение по статьям подпрограммы внутри программы министерства';
                $data = $model->getSpendingsByWorkType($params['program_id'], $params['ministry_id'],$params['work_type_id'],2015);
                $other_data = $model->getSpendingsByProgramAndWorkTypeAndQuarters($params['program_id'], $params['ministry_id'],$params['work_type_id'],2015);
                break;
            case 'economic':
                $title = 'Как распределяются расходы бюджета МПТ по отраслям промышленности?';
                $data = $model->getEconomicSpheresByMinistry($params['ministry_id']);
                break;
            case 'percentage':
                $title = 'Распределение по процентам';
                $data = $model->getPercentage($params['ministry_id'], [2013, 2014, 2015]);
                $settings = ['x_axis' => [2013, 2014, 2015]];
                break;
            case 'columns':
            case 'stacked_columns':
            case 'basic':
                $years =  $params['year'];
                $title = 'Распределение по месяцам';
                $data = $model->getPercentageByMonths($params['ministry_id'],$years,5);
                $xAxis = [];
                if (is_array($years)) {
                    for ($j = 0; $j < count($years); $j++) {
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == 0) {
                                $xAxis[] = $years[$j];
                            } else {
                                $xAxis[] = $i + 1;
                            }
                        }
                    }
                } else {
                    for ($i = 0; $i < 12; $i++) {
                        $xAxis[] = $i + 1;
                    }
                }
                $settings = ['x_axis' => $xAxis];
                break;
            case 'months':
            case 'area':
            case 'stacked_area':
                $years =  $params['year'];
                $title = 'Распределение по месяцам';
                $data = $model->getPercentageByMonths($params['ministry_id'],$years,5);
                $xAxis = [];
                for($j = 0;$j<3;$j++){
                    for($i = 0;$i<12;$i++){
                        if($i == 0 && is_array($years)){
                            $xAxis[] = $years[$j];
                        } else {
                            $xAxis[] = $i+1;
                        }
                    }
                }

                $settings = ['x_axis' => $xAxis];
                break;
            default:
                // throw new Exception('Запрошен неверный тип данных', 950);
                break;
        }
        if (isset($data)) {
            echo json_encode(['title' => $title, 'series' => $data,'other_series'=>$other_data, 'settings'=>$settings], JSON_UNESCAPED_UNICODE);
        }
    }

}
