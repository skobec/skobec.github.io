<?php

class Prodom_Helper_Init extends Zend_Controller_Action_Helper_Abstract {

    protected $user,
        $moduleName,
        $controllerName,
        $actionName,
        $is_ajax;

    protected $redirector;

    function __construct() {
    	/* Session|Сессии */
		$save_path = sys_get_temp_dir() .'/etton_sessions';
		if(!file_exists($save_path)) {
			mkdir($save_path, 0777, true);
		}
		ini_set('session.save_path', $save_path);
		ini_set('session.gc_maxlifetime', Constant::VAR_USER_AUTH_PERIOD);
		session_set_cookie_params(Constant::VAR_USER_AUTH_PERIOD, '/', Settings::get('cookie_domain'), false, false);
		$options = array('cookie_domain' => Settings::get('cookie_domain'), 'cookie_secure' => 'false');
		Zend_Session::setOptions($options);
		Zend_Session::start();
    }

    public function getName() {
        return 'Init';
    }
 
    /**
    * @author sciner
    * @since 30.09.2014
    */   
    function postDispatch() {        
        if($view = Zend_Layout::getMvcInstance()->getView()) {
            // Отрисовываем пагинатор, если есть.
            if(isset($view->paginator)) {
                $p = $view->paginator;
                if($p instanceof Paginator) {
	                if($p->getTotalPages() > 1) {
	                    $text = ($p->getStartIndex() + 1).'&ndash;'.($p->getStartIndex() + $p->getItemsPerPage()).' из '.$p->getRecordsCount();
	                    Mikron_Entity_Designer_Toolbar::addTag('span', array("class"=>"pull-right","style"=>"margin-top:5px"), $text);
	                }
                }
            }
        }
    }

    /**
    * @author sciner
    * @since 27.09.2013
    * 
    * @param string $url
    */
    public function gotoUrl($url) {
        $this->redirector = $this->redirector ?: Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');         
        $this->redirector->gotoUrl($url);
    }

    /**
    * @author sciner
    * @since 27.09.2013
    * 
    * @param object $url
    */
    public function opportunity($acl_list) {
        if($this->user->isLogged()) {
            foreach($acl_list as $function_id => $function) {
                if($this->user->getAcl($function_id) === Constant::ACCESS_LEVEL_NONE) {
                    $dm = $function['disabled_menu'];
                    $da = $function['disabled_action'];
                    foreach($dm as $key => $value) {
                        // Скрытие пункта меню
                        Plugin_Menu::hideItem($value[0], array($value[1]));
                    }
                    foreach($da as $key => $value) {
                        foreach($value as $url => $goto) {
                            $ca = explode('/', $url);
                            $ca = array_filter($ca);
                            if($this->controllerName == $ca[0] && $this->actionName == $ca[1]) {
                                $this->gotoUrl($goto);
                            }                            
                        }
                    }
                }
            }
        }
    }

    /**
    * Hook
    */
    public function postInit() {
    }

    public function init() {
        if ($zca = $this->getActionController()) {
            if(property_exists($zca, 'user')) {
                return false;
            }
            $zca->user = new User;
            $this->user = $zca->user;
            $zca->menu = new Plugin_Menu;
            Zend_Registry::set('user', $zca->user);
            Zend_Registry::set('menu', $zca->menu);
            $view = Zend_Layout::getMvcInstance()->getView();
            $view->assign('user', $zca->user);
            $view->assign('menu', $zca->menu);
            $this->moduleName = $this->getRequest()->getModuleName();
            $this->controllerName = $this->getRequest()->getControllerName();
            $this->actionName = $this->getRequest()->getActionName();
            $module = ucfirst($this->moduleName);
            $controller = ucfirst($this->controllerName);
            $action = ucfirst($this->actionName);
            $module = ($module == 'Default' ? null : $module.'_');
            // @ajax
			$action_doc = Prodom_Reflection::parseMethodDoc($module.$controller.'Controller', $action.'Action');
            $this->is_ajax = array_key_exists('ajax', $action_doc);
            if($this->is_ajax) {
                Zend_Layout::getMvcInstance()->disableLayout();
                Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
				Zend_Controller_Front::getInstance()->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8', true);
            }
            return $this->postInit();
        }
    }
}