<?php

class IndexController extends Zend_Controller_Action {

    function init() {
        $this->_helper->Init->init();
        if (!$this->user->isLogged()) {
            // Functions::redirect('//'.Settings::get('root_domain').'/login/');
        }
    }

    public function indexAction() {
        $this->view->main_map_data = Service::Widget()->getMainMap();
        $this->view->districts = Service::Region()->findDistricts(true);
        $this->view->regions = Service::Region()->findAll();
        $scenario_list = Service::Scenario()->getAll();
        $main_pie_data = [
            1 => Service::EventPlan()->getTotalMoneyByProgram(1, 1)[0]->y,
            2 => Service::EventPlan()->getTotalMoneyByProgram(2, 1)[0]->y,
            4 => Service::EventPlan()->getTotalMoneyByProgram(4, 1)[0]->y,
            7 => Service::EventPlan()->getTotalMoneyByProgram(7, 1)[0]->y,
            10 => Service::EventPlan()->getTotalMoneyByProgram(10, 1)[0]->y,
            8 => Service::EventPlan()->getTotalMoneyByProgram(8, 1)[0]->y,
            13 => Service::EventPlan()->getTotalMoneyByProgram(13, 1)[0]->y,
            9 => Service::EventPlan()->getTotalMoneyByProgram(9, 1)[0]->y,
            11 => Service::EventPlan()->getTotalMoneyByProgram(11, 1)[0]->y,
            5 => Service::EventPlan()->getTotalMoneyByProgram(5, 1)[0]->y,
            12 => Service::EventPlan()->getTotalMoneyByProgram(12, 1)[0]->y,
            6 => Service::EventPlan()->getTotalMoneyByProgram(6, 1)[0]->y,
        ];
        $this->view->totalMoney = Service::EventPlan()->getTotalMoneyByMinistry(1)[0]->y;
        $this->view->scenarios = $scenario_list;
        $this->view->main_pie_data = $main_pie_data;
        $this->_helper->layout->setLayout('main');
    }

    public function feedbackAction() {

        if (isset($_POST) && !empty($_POST)) {
            require_once dirname(__FILE__) . '/../../../../../framework/Mail/class.phpmailer.php';
            $mailer = new PHPMailer;
            $mail_view = new Zend_View();
            $mail_view->name = $this->getRequest()->getParam('name');
            $mail_view->address = $this->getRequest()->getParam('address');
            $mail_view->email = $this->getRequest()->getParam('email');
            $mail_view->phone = $this->getRequest()->getParam('phone');
            $mail_view->department = $this->getRequest()->getParam('department');
            $mail_view->subject = $this->getRequest()->getParam('subject');
            $mail_view->message = $this->getRequest()->getParam('message');
            $mail_view->setScriptPath(dirname(dirname(__FILE__)) . '/views/scripts/index/');

            $mail_view_html = $mail_view->render('mail_view.phtml');

            $mailer->CharSet = 'utf-8';
            $mailer->IsSMTP();                            // Set mailer to use SMTP
            $mailer->SMTPAuth = true;                     // Enable SMTP authentication
            $mailer->SMTPSecure = 'tls';                  // Enable encryption, 'ssl' also accepted
            $mailer->Host = Constant::DELIVERY_HOST;      // server
            $mailer->Port = Constant::DELIVERY_PORT;      // port
            $mailer->Username = Constant::DELIVERY_USER;  // SMTP username
            $mailer->Password = Constant::DELIVERY_PASS;  // SMTP password
            $mailer->From = Constant::DELIVERY_FROM;
            $mailer->FromName = Constant::DELIVERY_FROM_NAME;
            $mailer->Subject = "Тестирование обратной связи портала";
            $mailer->Body = $mail_view_html;

            if (!empty($_FILES['file']['tmp_name'])) {
                $mailer->AddAttachment($_FILES['file']['tmp_name'], $_FILES['file']['name']);
            }

            $mailer->AddAddress('support1@etton.ru');

            $mailer->IsHTML(true);

            if (!$mailer->Send()) {
                throw new Exception($mailer->ErrorInfo, 951);
            }

            $this->_helper->viewRenderer('feedback-success');
        }
    }

}
