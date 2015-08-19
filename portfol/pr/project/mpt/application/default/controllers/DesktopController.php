<?php

class DesktopController extends Zend_Controller_Action {

    function init() {
        $this->_helper->Init->init();
        if (!$this->user->isLogged()) {
            // Functions::redirect('//'.Settings::get('root_domain').'/login/');
        }
    }

    public function indexAction() {
        Plugin_Menu::setActive('desktop-menu', 'desktop');
        $scenario_list = Service::Scenario()->getAll();
        $scenario1 = [];
        $scenario2 = [];
        $scenario3 = [];
        foreach ($scenario_list as $scenario) {
            $scenario1[] = ['id' => $scenario->id, 'name' => $scenario->title];
            if ($scenario->children) {
                foreach ($scenario->children as $child) {
                    $scenario2[] = ['parent' => $scenario->id, 'name' => $child->title, 'id' => $child->id];
                    if ($child->children) {
                        foreach ($child->children as $child2) {
                            $scenario3[] = ['parent' => $child->id, 'name' => $child2->title, 'id' => $child2->id];
                        }
                    }
                }
            }
        }
        $this->view->money = Service::EventPlan()->_cache(3600)->getTotalMoneyByMinistry(1);
        $this->view->nextLevel = 'programs';
        $this->view->scenario1 = $scenario1;
        $this->view->scenario2 = $scenario2;
        $this->view->scenario3 = $scenario3;
        $this->view->hints = Service::Thesaurus()->findAll();
        $this->view->widget_list = Service::Widget()->getList();
    }

    public function personalAction() {
        $id = $this->getRequest()->getParam('id');
        Plugin_Menu::setActive('desktop-menu', 'personal');
        $scenario_list = Service::Scenario()->getAll();
        $scenario1 = [];
        $scenario2 = [];
        $scenario3 = [];
        foreach ($scenario_list as $scenario) {
            $scenario1[] = ['id' => $scenario->id, 'name' => $scenario->title];
            if ($scenario->children) {
                foreach ($scenario->children as $child) {
                    $scenario2[] = ['parent' => $scenario->id, 'name' => $child->title, 'id' => $child->id];
                    if ($child->children) {
                        foreach ($child->children as $child2) {
                            $scenario3[] = ['parent' => $child->id, 'name' => $child2->title, 'id' => $child2->id];
                        }
                    }
                }
            }
        }
        $this->view->scenario1 = $scenario1;
        $this->view->scenario2 = $scenario2;
        $this->view->scenario3 = $scenario3;
        // Костыль. Перечислен список реализованных сценариев.
        $finished_sceanarios = [80, 91, 92, 93, 95, 96, 97, 99, 130, 135, 139, 140];
        $this->view->widget_list = in_array($id, $finished_sceanarios) ? Service::Widget()->getCustomWidget($this->user->getSessionId(), $id) : null;
        $this->view->widget_list = in_array($id, $finished_sceanarios) ? Service::Widget()->getCustomWidget($this->user->getSessionId(), $id) : null;
    }

    /**
     * Проваливание в бублик с главной
     * Открывается 80 виджет, проваленный в нужную программу
     */
    public function programAction() {
        Plugin_Menu::setActive('desktop-menu', 'personal');
        $scenario_list = Service::Scenario()->getAll();
        $scenario1 = [];
        $scenario2 = [];
        $scenario3 = [];
        foreach ($scenario_list as $scenario) {
            $scenario1[] = ['id' => $scenario->id, 'name' => $scenario->title];
            if ($scenario->children) {
                foreach ($scenario->children as $child) {
                    $scenario2[] = ['parent' => $scenario->id, 'name' => $child->title, 'id' => $child->id];
                    if ($child->children) {
                        foreach ($child->children as $child2) {
                            $scenario3[] = ['parent' => $child->id, 'name' => $child2->title, 'id' => $child2->id];
                        }
                    }
                }
            }
        }
        $this->view->scenario1 = $scenario1;
        $this->view->scenario2 = $scenario2;
        $this->view->scenario3 = $scenario3;

        $id = $this->getRequest()->getParam('id');
        $program_data = Service::EventPlan()->getSubprograms($id, 1);
        $program_data_quarters = Service::EventPlan()->getSubprogramsByProgramAndQuarters($id, 1);
        $total_money = Service::EventPlan()->getTotalMoneyByProgram($id, 1);

        $xAxis = App_Mpt::generateQuartersArray(2015);
        $scenario = Service::Scenario()->findOne(80);
        $widget = new Type_Widget_Data;
        $widget->id = 80;
        $widget->title = Service::Program()->findOne($id)->title;
        $widget->icon_class = $scenario->icon_class;
        $widget->type = $scenario->type;
        $widget->x_axis = $xAxis;
        $widget->is_expanded = false;
        $widget->chart_data = $program_data;
        $widget->other_data = $program_data_quarters;
        $widget->money = $total_money;
        $widget->params = ['ministry_id' => 1];


        $parent_widget = Service::Widget()->getCustomWidget($this->user->getSessionId(), 80);
        $this->view->widget_list = [
            $widget
        ];
        $this->view->parent_widget = $parent_widget;
    }

    public function educationAction() {
        Plugin_Menu::setActive('desktop-menu', 'education');
    }

    public function mediaAction() {
        Plugin_Menu::setActive('desktop-menu', 'media');
    }

    public function createAction() {
        Plugin_Menu::setActive('desktop-menu', 'create');
    }

    public function programsAction() {
        Plugin_Menu::setActive('mpt-menu', 'main/programs');
        $model = Service::EventPlan()->_cache(3600);
        $data = $model->getTotalMoneyByMinistry(1);
        $this->view->money = $data;
        $this->view->nextLevel = 'programs';
    }

    public function economicAction() {
        Plugin_Menu::setActive('mpt-menu', 'main/economic');
        $model = Service::EventPlan()->_cache(3600);
        $data = $model->getTotalMoneyByMinistry(1);
        $this->view->money = $data;
        $this->view->nextLevel = 'economic';
    }


}
