<?php

/**
* @author sciner
*/
class Module_Common {

    static $list;
    static $arm_list;

    static function initList() {
        if(!self::$list) {
            // Список всех возможных модулей
            $mjf 			= (object)array('id' => Constant::PROJECT_MJF,         	'code' => 'mjf',        'title' => 'Мониторинг жилищного фонда',			'description' => 'Единая база электронных паспортов жилого фонда региона');
			$regfond 		= (object)array('id' => Constant::PROJECT_REGFOND,     	'code' => 'regfond',    'title' => 'Формирование региональных программ',	'description' => 'Формирование адресных программ региона по модернизации ЖКХ');
            $billing 		= (object)array('id' => Constant::PROJECT_BILLING,     	'code' => 'billing',    'title' => 'Биллинговый центр',						'description' => 'Автоматизированные расчеты с потребителями коммунальных и жилищных услуг');
            $gji 			= (object)array('id' => Constant::PROJECT_GJI,         	'code' => 'gji',        'title' => 'Жилищная инспекция',					'description' => 'Электронный документооборот Государственной жилищной инспекции');
            $kaprem 		= (object)array('id' => Constant::PROJECT_KAPREM,      	'code' => 'kaprem',     'title' => 'Капитальный ремонт',					'description' => 'Контроль исполнения программ капитального ремонта');
            $stroicontrol 	= (object)array('id' => Constant::PROJECT_STROICONTROL, 'code' => 'sc',     	'title' => 'Строительный контроль',					'description' => 'Строительный контроль');
            $report 		= (object)array('id' => Constant::PROJECT_REPORT,       'code' => 'report',     'title' => 'Генератор отчетов',						'description' => 'Генератор отчетов');
            $psd 			= (object)array('id' => Constant::PROJECT_PSD,      	'code' => 'psd',     	'title' => 'Проектно-сметная документация',			'description' => 'Проектно-сметная документация');
            $admin 			= (object)array('id' => Constant::PROJECT_ADMIN,       	'code' => 'admin',		'title' => 'Панель администратора - '.Constant::VAR_ROOT_TITLE,					'description' => 'Панель администратора');
            $portal 		= (object)array('id' => Constant::PROJECT_PORTAL,      	'code' => 'portal',     'title' => 'Единый Web-портал',						'description' => 'Информирование граждан о процессах в сфере ЖКХ');
            $tender			= (object)array('id' => Constant::PROJECT_TENDER,      	'code' => 'otbor',     	'title' => 'Отбор подрядных организаций',			'description' => 'Отбор подрядных организаций');
            $debit			= (object)array('id' => Constant::PROJECT_DEBIT,      	'code' => 'debit',     	'title' => 'Дебиторская задолженность',				'description' => 'Взыскание задолженности по взносам на капитальный ремонт в судебном порядке');
            $delm = ACL::getDomainDelimeter();
            if(in_array(Constant::VAR_REGION_CODE, array('udmurt'))) {
				$mjf->title = 'Жилищный фонд';
				$regfond->title = 'Управление программой капитального ремонта';
				$billing->title = 'Биллинговый центр';
				$gji->title = 'Жилищный надзор';
				$kaprem->title = 'Капитальный ремонт';
				$stroicontrol->title = 'Строительный контроль';
				$report->title = 'Генератор отчетов';
				$psd->title = 'Проектно-сметная документация';
				// $admin->title = 'Панель администратора';
				$portal->title = 'Единый Web-портал';
				$mjf->description = 'Единая база электронных паспортов жилого фонда региона';
				$regfond->description = 'Формирование адресных программ региона по модернизации ЖКХ';
				$billing->description = 'Автоматизированные расчеты с потребителями коммунальных и жилищных услуг';
				$gji->description = 'Электронный документооборот Государственной жилищной инспекции';
				$kaprem->description = 'Контроль исполнения программ капитального ремонта';
				$stroicontrol->description = 'Строительный контроль';
				$report->description = 'Генератор отчетов';
				$psd->description = 'Проектно-сметная документация';
				$admin->description = 'Панель администратора';
				$portal->description = 'Информирование граждан о процессах в сфере ЖКХ';
                $list = array(
                    $mjf, $regfond, $billing, $gji, $kaprem, $stroicontrol, $report, $psd, $admin, $portal, $tender,
                );
            } elseif(in_array(Constant::VAR_REGION_CODE, array('yanao'))) {
				$mjf->title = 'Мониторинг МКД';
				$regfond->title = 'Актуализация программы капитального ремонта';
				$billing->title = 'Биллинговый центр и финансовый учет фонда капитального ремонта';
				$gji->title = 'Контроль формирования фонда капитального ремонта';
				$kaprem->title = 'Планирование и проведение капитального ремонта';
				$stroicontrol->title = 'Строительный контроль';
				$report->title = 'Генератор отчетов';
				$psd->title = 'Проектно-сметная документация';
				// $admin->title = 'Панель администратора';
				$portal->title = 'Личный кабинет абонента';
				$tender->title = 'Отбор подрядных организаций';
				$mjf->description = null;
				$regfond->description = null;
				$billing->description = null;
				$gji->description = null;
				$kaprem->description = null;
				$stroicontrol->description = null;
				$report->description = null;
				$psd->description = null;
				$admin->description = null;
				$portal->description = null;
				$tender->description = null;
                $list = array(
                    $mjf, $regfond, $billing, $gji, $kaprem, $stroicontrol, $report, $psd, $admin, $portal, $tender,
                );
            } elseif(in_array(Constant::VAR_REGION_CODE, array('penza'))) {
            	$tender->description = 'Проведение конкурсов по отбору подрядных организаций';
                $list = array(
                    $mjf, $regfond, $billing, $gji, $kaprem, $stroicontrol, $report, $psd, $admin, $portal, $tender, $debit,
                );
            } else {
                $list = array(
                    $mjf, $regfond, $billing, $gji, $kaprem, $stroicontrol, $report, $psd, $admin, $portal, $tender,
                );
            }
            $resp = array();
            // Генерация URI адресов на модули
            $rd = Settings::get('root_domain');
            foreach($list as $index => $item) {
                $code = $item->code;
                $item->uri = "//{$code}{$delm}{$rd}";
                if($code == 'sc') {
                	$item->uri = "//{$code}{$delm}{$rd}/dictionary/";
				}
                if($code == 'debit') {
                	$item->uri = "//billing{$delm}{$rd}/";
				}
				if($code == 'otbor') {
					if(Constant::VAR_REGION_CODE == 'penza') {
						$item->uri = 'http://auction.company42.ru/';
					}
				}
                $item->enable = true;
                $resp[$item->id] = $item;
            }
            self::$list = $resp;
        }
        return self::$list;
    }

    static function getById($id) {
    	if($list = self::$list) {
			// do nothing
    	} else {
			$list = self::getList();
    	}
        foreach($list as $i) {
            if($i->id == $id) {
                return $i;
            }
        }
        throw new Exception('Неверный идентификатор модуля '.$id, 950);
    }

    static function getByCode($code) {
    	if($list = self::$list) {
			// do nothing
    	} else {
			$list = self::getList();
    	}
        foreach($list as $i) {
            if($i->code == $code) {
                return $i;
            }
        }
        throw new Exception('Неверный код модуля '.$code, 950);
    }

	static function getList() {
		$user = new User;
        $list = self::initList();
	    $rights_mask = array();
        // Модули "Строительного контроля" доступны для демо версий для всех пользователей
        if(IS_DEMO_HOST) {
        	$def_rights = array(
                Constant::ARM_ID_EXECUTIVE,
                Constant::ARM_ID_LOCALGOVERNMENT,
                Constant::ARM_ID_MANAGEMENT,
                Constant::ARM_ID_REGIONALOPERATOR,
                Constant::ARM_ID_CONTRACTOR,
                Constant::ARM_ID_OWNER,
                Constant::ARM_ID_ADMINISTRATOR,
                Constant::ARD_ID_BOOKKEEPER,
            );
            $rights_mask[Constant::PROJECT_MJF] = $def_rights;
            $rights_mask[Constant::PROJECT_REGFOND] = $def_rights;
            $rights_mask[Constant::PROJECT_GJI] = $def_rights;
            $rights_mask[Constant::PROJECT_BILLING] = $def_rights;
            $rights_mask[Constant::PROJECT_KAPREM] = $def_rights;
            $rights_mask[Constant::PROJECT_STROICONTROL] = $def_rights;
            $rights_mask[Constant::PROJECT_REPORT] = $def_rights;
            $rights_mask[Constant::PROJECT_PSD] = $def_rights;
        } else {
		    // Список модулей доступен только авторизованным пользователям
		    if($user->isLogged()) {
		    	if(!self::$arm_list) {
		    		self::$arm_list = Service::Arm()->getArmProjectAccess(new Type_Arm());
				}
		        foreach(self::$arm_list as $arm) {
		            foreach($arm->project_access_list as $project) {
		                if(!array_key_exists($project->id, $rights_mask)) {
		                    $rights_mask[$project->id] = array();
		                }
		                $rights_mask[$project->id][] = $arm->id;
		            }
		        }
			}
	        // Модуль "Портал" (разрешён всем)
	        $rights_mask[Constant::PROJECT_PORTAL] = array(
	            Constant::ARM_ID_EXECUTIVE,
	            Constant::ARM_ID_LOCALGOVERNMENT,
	            Constant::ARM_ID_INSPECTION,
	            Constant::ARM_ID_MANAGEMENT,
	            Constant::ARM_ID_REGIONALOPERATOR,
	            Constant::ARM_ID_CONTRACTOR,
	            Constant::ARM_ID_OWNER,
	            Constant::ARM_ID_ADMINISTRATOR,
	            Constant::ARD_ID_BOOKKEEPER,
	            Constant::ARM_ID_DISPATCHER,
	        );
        }
        $resp = array();
        // Удаление из списка тех модулей, к которым у пользователя нет доступа
        foreach($list as $index => $module) {
        	if(array_key_exists($module->id, $rights_mask)) {
                if($user->isLogged() && !in_array($user->getInfo()->arm_id, $rights_mask[$module->id])) {
                	$list[$index]->enable = false;
                }
                $resp[$index] = $list[$index];
        	}
		}
		return $resp;
	}

	/**
	* Добавляет новое меню доступных модулей
	* @author sciner
	*/
	static function initMenu() {
		$module_list = self::getList();
		$menu_list = array();
		foreach($module_list as $module) {
			if(!$module->enable) {
				continue;
			}
			if($module->id == Constant::PROJECT_ADMIN) {
				$menu_list[] = new Plugin_Menu_Item(array('code' => null, 'title' => null, 'uri' => null, 'class' => 'divider'));
			}
			$menu_list[] = new Plugin_Menu_Item(array('code' => $module->code, 'title' => $module->title, 'uri' => $module->uri, 'class' => null));
		}
	    Plugin_Menu::add('project-menu', $menu_list);
	}

}
