<?php

/**
 * Ручная обработка формы
 * @author sciner
 * @since 2015-03-28
 */
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_CREATE, 'Type_Event_Upload_Form', function($row) {
    // $user = new User;
    // dumpre($_FILES);
    // dumpre($row);
});

return;
/**
* Запрет удаления недоформированной пачки
* @author sciner
* @since 2015-03-20
*/
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, array('Type_Billing_Bill_Ticket_Packet', 'Type_Billing_Bill_Ticket_Packet_Part'), function($id, $entity_name = null) {
	$p = Mikron_Entity_Model::getById($entity_name, $id);
	if($p) {
		$p = (object)$p;
		if(!$p->dt_finished) {
			if(!IS_DEVELOPER_HOST) {
				throw new Exception('Нельзя удалить недоформированную пачку.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
	} else {
		throw new Exception('Указанной пачки не существует', Constant_Base::DEF_USER_ERROR_CODE);
	}
});

/**
 * После удаления пачки/подпачки приводим в соответствие помещения.
 * @author sharafanmaxim78
 * @since 2015-04-22
 */
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_DELETE, array('Type_Billing_Bill_Ticket_Packet', 'Type_Billing_Bill_Ticket_Packet_Part'), function($id, $entity_name = null) {
	Model_Apartment::garbageApartmentInvoice();	
});

/**
* Ручная обработка формы возврата ошибочного платежа
* @author sciner
* @since 2015-03-28
*/
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_CREATE, 'Type_Billing_Bill_Account_Operation_Add_Return', function($row) {
	$user = new User;
	return Service::Billing_Account()->returnPaid($user->getSessionId(), new Type_Billing_Bill_Account_Operation_Add_Return($row));
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, 'Type_Billing_Bill_Account_Owner', function($row) {
    $service = new \Etton\Service\Billing\Home\Owner();
    $service->updateAccountOwnerExcept($row);
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_DELETE, 'Type_Billing_Bill_Account_Operation', function($row) {
    $service = new \Etton\Service\Billing\Home\Owner();
    $service->updateAccountOwnerExcept($row);
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_DELETE, null, function($id, $entity_name) {
	if(strtolower(substr($entity_name, 0, 5)) == 'type_') {
		$user = new User;
		$description = null;
		Service::Log()->add($user->getSessionId(), Constant_Base::LOG_ENTITY_DELETE, $entity_name, (int)$id, null, $description);
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_UPDATE, null, function($row, $entity_name) {
	if(strtolower(substr($entity_name, 0, 5)) == 'type_') {
		$user = new User;
		$description = null;
		Service::Log()->add($user->getSessionId(), Constant_Base::LOG_ENTITY_EDIT, $entity_name, $row['id'], $row, $description);
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_CREATE, 'Type_Billing_Bill_Operation', function($row) {
    // проверка существования счёта по номеру
    if(!is_numeric($row['credit']) || $row['credit'] == 0) {
		throw new Exception('Не указана сумма списания', Constant_Base::DEF_USER_ERROR_CODE);
    }
    if(!array_key_exists('contractor_id', $row) || !is_numeric($row['contractor_id'])) {
		throw new Exception('Не выбрана подрядная организация', Constant_Base::DEF_USER_ERROR_CODE);
    }
    if(!array_key_exists('home_id', $row) || !is_numeric($row['home_id'])) {
		throw new Exception('Не выбран дом', Constant_Base::DEF_USER_ERROR_CODE);
    }
});


Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_UPDATE, 'Type_Billing_Bill_Cofinancing_Operation', function($row) {
	if(array_key_exists('debit', $row)) {
		$item = new Type_Billing_Bill_Cofinancing_Operation($row['id']);
		$summa = $item->debit ? $item->debit : $item->credit;
		if(!is_numeric($row['debit']) || $row['debit'] == 0) {
			throw new Exception('Не указана сумма поступления', Constant_Base::DEF_USER_ERROR_CODE);
		}
		if($summa < $row['debit']) {
			throw new Exception('Сумма поступления должна быть меньше суммы распределения', Constant_Base::DEF_USER_ERROR_CODE);
		}
		$item->debit -= $row['debit'];
		if($item->debit > 0) {
			Mikron_Entity_Model::create('Type_Billing_Bill_Cofinancing_Operation', $item);
		}
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_CREATE, 'Type_Billing_Bill_Operation', function($row) {
    Service::Billing()->raiseNewBillingOperation($row['id']);
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_UPDATE, 'Type_Billing_Bill_Spec', function($row) {
	if(Constant::VAR_REGION_CODE == 'lipetsk') {
		// Проверка номера ШКИ
		$shki = $row['shki'];
	    $spec_list = Service::Billing()->getSpecByShki($shki);
	    if(count($spec_list) == 1) {
			$spec = array_shift($spec_list);
			if($spec->id != $row['id']) {
				throw new Exception('Данный номер ШКИ занят', Constant_Base::DEF_USER_ERROR_CODE);
			}
	    } elseif(count($spec_list) > 1) {
			throw new Exception('Данный номер ШКИ занят', Constant_Base::DEF_USER_ERROR_CODE);
	    }
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_CREATE, 'Type_Billing_Bill_Spec', function($row) {
    // проверка лиц.счета для дома из реестра счетов РО
    $rs = db_general::$db->billing_bill_ro_account()->where('home_id = ?', $row['home_id'])->fetch();
    if($rs) {
        throw new Exception('Для данного дома уже заведен счёт РО', Constant_Base::DEF_USER_ERROR_CODE);
    }
    // проверка существования счёта по данному дому
    $spec = Service::Billing()->getSpecByHomeId($row['home_id']);
    if($spec) {
	    throw new Exception('Для данного дома уже существует специальный счёт', Constant_Base::DEF_USER_ERROR_CODE);
    }
    $rf = db_general::$db->mjf_repair_failure_index()->where('home_id = ?', $row['home_id'])->fetch();
    $home = db_general::$db->home()->where('id = ?', $row['home_id'])->fetch();
    if($rf) {
        $rf = (object)$rf->jsonSerialize();
        if($rf->destroy === 1) {
            throw new Exception("Ошибка: дом является аварийным!<br>Невозможно создавать счета для аварийных домов.<br>{$home['address_string']} ");
        }
    }
    if($home['hidden'] === 1) {
        throw new Exception("Ошибка: дом является скрытым!<br>Невозможно создавать счета для скрытых домов.<br>{$home['address_string']} ");
    }
	$program = db_general::$db->program()->where('parent_id is null')->where('closed = 1')->order('id desc')->limit('1')->fetch();
	$program_home = false;
	if($program) {
		$program = (object)$program->jsonSerialize();
		$program_home = db_general::$db->program_home()->where('home_id = ?', $row['home_id'])->where('program_id = ?', $program->id)->fetch();
	}
	/*if(!$program_home || !$program) {
		throw new Exception("Ошибка: дом должен находится в действующей (закрытой) долгосрочной программе.<br>{$home['address_string']} ");
	}*/
	if(Constant::VAR_REGION_CODE == 'lipetsk') {
		if(!array_key_exists('shki', $row) || strlen($row['shki'] < 1)) {
			throw new Exception('Не указан номер ШКИ', Constant_Base::DEF_USER_ERROR_CODE);
		}
		// Проверка номера ШКИ
		$shki = $row['shki'];
	    $spec_list = Service::Billing()->getSpecByShki($shki);
	    if(count($spec_list) == 1) {
			throw new Exception('Данный номер ШКИ занят', Constant_Base::DEF_USER_ERROR_CODE);
	    }
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_CREATE, 'Type_Billing_Bill_Spec_Account', function($row) {
    if(!$row) {
        throw new Exception('Заполните поля ', Constant_Base::DEF_USER_ERROR_CODE);
    }
	// Проверка на закрытый период
	if(!isset($row['date_open'])){
		$dt=new DateTime();
	}
	else{
		$dt = new DateTime($row['date_open']);
	}
	$user = new User();	
	$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt->format('Y-m-d'));
	if(!$allowFlag) {
		throw new Exception('Поле "Дата открытия" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
	}
	if(Constant::VAR_REGION_CODE == 'omsk') {
		if(empty($row['credit_dt_start'])) {
        	throw new Exception('Укажите с какого месяца производить начисления', Constant_Base::DEF_USER_ERROR_CODE);
		}else{
			$dt = new DateTime($row['credit_dt_start']);
			$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt->format('Y-m-d'));
			if(!$allowFlag) {
				throw new Exception('Поле "Начисления производить с" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
    }
	if(Constant::VAR_REGION_CODE == 'udmurt') {
		if(empty($row['cadastr_number'])) {
			throw new Exception('Укажите кадастровый номер', Constant_Base::DEF_USER_ERROR_CODE);
		}
		if(empty($row['total_flat_area'])) {
			throw new Exception('Укажите общую площадь', Constant_Base::DEF_USER_ERROR_CODE);
		}
	}
});

/*
* Создание ЛС в спец-счёте
*/
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_CREATE, 'Type_Billing_Bill_Spec_Account', function($row) {
	$user = new User();
	Service::Billing()->raiseAccountManualCreated($user->getSessionId(), Constant_Base::ACCOUNT_TYPE_SPEC_ID, $row['id']);
	Service::Billing()->setDebt($user->getSessionId(), Constant_Base::ACCOUNT_TYPE_SPEC_ID, $row['id'], true);
});

/**
* Перед сохранением
*/
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_UPDATE, 'Type_Billing_Bill_Spec_Account', function($row) {
    if(in_array(Constant::VAR_REGION_CODE, array('irkutsk', 'tver')) && isset($_POST['form']['kaprem_min_payment'])) {
        $tariff = Service::Billing()->getSpecAccountTariff($row['id']);
        if($tariff != $_POST['form']['kaprem_min_payment']) {
            $params = array('kaprem_min_payment' => $_POST['form']['kaprem_min_payment'], 'id' => $row['id']);
            Service::Billing()->saveAccountTariff($params, 2);
        }
    }
    if($row['penny_dt_end']) {
        if(strtotime($row['penny_dt_start']) >= strtotime($row['penny_dt_end'])) {
            throw new Exception('Значение поля: "Расчет пени производить по" должно быть больше чем значение поля "Расчет пени производить с"');
        }
    }
    if($row['credit_dt_end']) {
        if(strtotime($row['credit_dt_start']) >= strtotime($row['credit_dt_end'])) {
            throw new Exception('Значение поля: "Начисления производить по" должно быть больше чем значение поля "Начисления производить с"');
        }
    }
    if(empty($row['credit_dt_start'])) {
        if(Constant::VAR_REGION_CODE == 'omsk') {
        	throw new Exception('Укажите с какого месяца производить начисления', Constant_Base::DEF_USER_ERROR_CODE);
		}
    }

	/* Проверка на закрытый период.
	 В переменную $row могут легально приходить даты, находящиеся в закрытом периоде. Это происходит в том случае,
	 когда они уже проставлены в счетах (мы не даем их менять). Но в запросе на update они все-равно учавствуют.
	 Для того, чтобы отличить такие даты от дат, только что введенных неправильно, получим текущее состояние строки
	 с данными. Если даты текущей строки и новой различаются- проверим дату на закрытый период.*/	
	$user = new User();
	$currentRow = Service::Billing()->getSpeccAccountById($user->getSessionId(), $row['id']);
	if(isset($row['date_open'])) {
		$dt_old=new DateTime($currentRow->date_open);
		$dt_new = new DateTime($row['date_open']);
		if ($dt_old->format('Y-m-d')!=$dt_new->format('Y-m-d')){
			$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt_new->format('Y-m-d'));
			if(!$allowFlag) {
				throw new Exception('Поле "Дата открытия" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
	}
	if(isset($row['penny_dt_start'])) {
		$dt_old=new DateTime($currentRow->penny_dt_start);
		$dt_new = new DateTime($row['penny_dt_start']);
		if ($dt_old->format('Y-m-d')!=$dt_new->format('Y-m-d')){
			$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt_new->format('Y-m-d'));
			if(!$allowFlag) {
				throw new Exception('Поле "Расчет пени производить с" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
	}
	if(isset($row['penny_dt_end'])) {
		$dt_old=new DateTime($currentRow->penny_dt_end);
		$dt_new = new DateTime($row['penny_dt_end']);
		if ($dt_old->format('Y-m-d')!=$dt_new->format('Y-m-d')){
			$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt_new->format('Y-m-d'));
			if(!$allowFlag) {
				throw new Exception('Поле "Расчет пени производить по" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
	}
	if(isset($row['credit_dt_start'])) {
		$dt_old=new DateTime($currentRow->credit_dt_start);
		$dt_new = new DateTime($row['credit_dt_start']);
		if ($dt_old->format('Y-m')!=$dt_new->format('Y-m')){
			
			$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt_new->format('Y-m-d'));
			if(!$allowFlag) {
				throw new Exception('Поле "Начисления производить с" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
	}
	if(isset($row['credit_dt_end'])) {
		$dt_old=new DateTime($currentRow->credit_dt_end);
		$dt_new = new DateTime($row['credit_dt_end']);
		if ($dt_old->format('Y-m')!=$dt_new->format('Y-m')){
			$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt_new->format('Y-m-d'));
			if(!$allowFlag) {
				throw new Exception('Поле "Начисления производить по" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
	}

    if(in_array(Constant::VAR_REGION_CODE, array('udmurt'))) {
        $updateOwner = array();
        if ($currentRow->flat != $row['flat']) {
            $updateOwner['flat'] = $row['flat'];
        }
        if ($currentRow->cadastr_number != $row['cadastr_number']) {
            $updateOwner['cadastr_number'] = $row['cadastr_number'];
        }
        if (!empty($updateOwner)) {
            \Service::Home()->updateOwner($updateOwner, $currentRow, 1);
        }
    }
});

/**
* После сохранения Л/С
*/
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_UPDATE, 'Type_Billing_Bill_Spec_Account', function($row) {
    Service::Billing()->raiseCreateSpecAccountNumber($row['id']);
	if(in_array(Constant::VAR_REGION_CODE, array('perm'))) {
		Service::Billing_Account()->raiseAccountBuildArea($row['id'], Constant_Base::ACCOUNT_TYPE_SPEC_ID);
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_CREATE, 'Type_Billing_Bill_Ro_Account', function($row) {
    $rf = db_general::$db->mjf_repair_failure_index()->where('home_id = ?', $row['home_id'])->fetch();
    $home = db_general::$db->home()->where('id = ?', $row['home_id'])->fetch();
    // проверка лиц.счета для дома из реестра спец счетов
    $rs = db_general::$db
    	->billing_bill_spec()
    	->where('home_id = ?', $row['home_id'])
    	->where('billing_bill_status_id', Constant_Base::BILLING_BILL_STATUS_OPEN)
    	->fetch();
    if($rs) {
        throw new Exception('Для данного дома уже заведен специальный счёт', Constant_Base::DEF_USER_ERROR_CODE);
    }
    if($rf) {
        $rf = (object)$rf->jsonSerialize();
        if($rf->destroy === 1) {
            throw new Exception("Ошибка: дом является аварийным!<br>Невозможно создавать счета для аварийных домов.<br>{$home['address_string']} ");
        }
    }
    if($home['hidden'] === 1) {
        throw new Exception("Ошибка: дом является скрытым!<br>Невозможно создавать счета для скрытых домов.<br>{$home['address_string']} ");
    }
    /*
	$program = db_general::$db->program()->where('parent_id is null')->where('closed = 1')->order('id desc')->limit('1')->fetch();
	$program_home = false;
	if($program) {
		$program = (object)$program->jsonSerialize();
		$program_home = db_general::$db->program_home()->where('home_id = ?', $row['home_id'])->where('program_id = ?', $program->id)->fetch();
	}
	if(!$program_home || !$program) {
		throw new Exception("Ошибка: дом должен находится в действующей (закрытой) долгосрочной программе.<br>{$home['address_string']} ");
	}
	*/
    if(!$row) {
		throw new Exception('Заполните поля ', Constant_Base::DEF_USER_ERROR_CODE);
	}
	$user = new User();
	if(!isset($row['date_open'])) {
		$dt = new DateTime();
	} else {
		$dt = new DateTime($row['date_open']);
	}
	$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt->format('Y-m-d'));
	if(!$allowFlag) {
		throw new Exception('Поле "Дата открытия" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
	}
	if(Constant::VAR_REGION_CODE == 'omsk') {
		if(empty($row['credit_dt_start'])) {
			throw new Exception('Укажите с какого месяца производить начисления', Constant_Base::DEF_USER_ERROR_CODE);
		}
		$dt = new DateTime($row['credit_dt_start']);
		$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt->format('Y-m-d'));
		if(!$allowFlag) {
			throw new Exception('Поле "Начисления производить с" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
		}
	}
	if(Constant::VAR_REGION_CODE == 'udmurt') {
		if(empty($row['cadastr_number'])) {
			throw new Exception('Укажите кадастровый номер', Constant_Base::DEF_USER_ERROR_CODE);
		}
		if(empty($row['total_flat_area'])) {
			throw new Exception('Укажите общую площадь', Constant_Base::DEF_USER_ERROR_CODE);
		}
	}
});

/*
* Создание ЛС в РО-счёте
*/
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_CREATE, 'Type_Billing_Bill_Ro_Account', function($row) {
	$user = new User();
    Service::Billing()->raiseAccountManualCreated($user->getSessionId(), Constant_Base::ACCOUNT_TYPE_RO_ID, $row['id']);
	Service::Billing()->setDebt($user->getSessionId(), Constant_Base::ACCOUNT_TYPE_RO_ID, $row['id'], true);
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_UPDATE, 'Type_Billing_Bill_Ro_Account', function($row) {
	if(!$row) {
		throw new Exception('Заполните поля ', Constant_Base::DEF_USER_ERROR_CODE);
	}
	if(in_array(Constant::VAR_REGION_CODE, array('irkutsk', 'tver')) && $row['kaprem_min_payment']) {
		$tariff = Service::Billing()->getRoAccountTariff($row['id']);
		if($tariff != $row['kaprem_min_payment']) {
			Service::Billing()->saveAccountTariff($row, 1);
		}
	}
	if(empty($row['credit_dt_start'])) {
		if(Constant::VAR_REGION_CODE == 'omsk') {
			throw new Exception('Укажите с какого месяца производить начисления', Constant_Base::DEF_USER_ERROR_CODE);
		}
	}
	/* Проверка на закрытый период.
	 В переменную $row могут легально приходить даты, находящиеся в закрытом периоде. Это происходит в том случае,
	 когда они уже проставлены в счетах (мы не даем их менять). Но в запросе на update они все-равно учавствуют.
	 Для того, чтобы отличить такие даты от дат, только что введенных неправильно, получим текущее состояние строки
	 с данными. Если даты текущей строки и новой различаются- проверим дату на закрытый период.*/
	$user = new User();
	$currentRow=Service::Billing()->getRoAccountById($user->getSessionId(), $row['id']);
	if(isset($row['date_open'])) {
		$dt_old=new DateTime($currentRow->date_open);
		$dt_new = new DateTime($row['date_open']);
		if ($dt_old->format('Y-m-d')!=$dt_new->format('Y-m-d')){
			$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt_new->format('Y-m-d'));
			if(!$allowFlag) {
				throw new Exception('Поле "Дата открытия" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
	}
	if(isset($row['penny_dt_start'])) {
		$dt_old = new DateTime($currentRow->penny_dt_start);
		$dt_new = new DateTime($row['penny_dt_start']);
		if ($dt_old->format('Y-m-d')!=$dt_new->format('Y-m-d')){
			$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt_new->format('Y-m-d'));
			if(!$allowFlag) {
				throw new Exception('Поле "Расчет пени производить с" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
	}
	if(isset($row['penny_dt_end'])) {
		$dt_old=new DateTime($currentRow->penny_dt_end);
		$dt_new = new DateTime($row['penny_dt_end']);
		if ($dt_old->format('Y-m-d')!=$dt_new->format('Y-m-d')){
			$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt_new->format('Y-m-d'));
			if(!$allowFlag) {
				throw new Exception('Поле "Расчет пени производить по" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
	}
	if(isset($row['credit_dt_start'])) {
		$dt_old=new DateTime($currentRow->credit_dt_start);
		$dt_new = new DateTime($row['credit_dt_start']);
		if ($dt_old->format('Y-m')!=$dt_new->format('Y-m')){
			$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt_new->format('Y-m-d'));
			if(!$allowFlag) {
				throw new Exception('Поле "Начисления производить с" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
	}
	if(isset($row['credit_dt_end'])) {
		$dt_old=new DateTime($currentRow->credit_dt_end);
		$dt_new = new DateTime($row['credit_dt_end']);
		if ($dt_old->format('Y-m')!=$dt_new->format('Y-m')){
			$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt_new->format('Y-m-d'));
			if(!$allowFlag) {
				throw new Exception('Поле "Начисления производить по" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
	}

    if(in_array(Constant::VAR_REGION_CODE, array('udmurt'))) {
        $updateOwner = array();
        if ($currentRow->flat != $row['flat']) {
            $updateOwner['flat'] = $row['flat'];
        }
        if ($currentRow->cadastr_number != $row['cadastr_number']) {
            $updateOwner['cadastr_number'] = $row['cadastr_number'];
        }
        if (!empty($updateOwner)) {
            \Service::Home()->updateOwner($updateOwner, $currentRow);
        }
    }
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_UPDATE, 'Type_Billing_Bill_Ro_Account', function($row) {
    Service::Billing()->raiseCreateRoAccountNumber($row['id']);
	if(in_array(Constant::VAR_REGION_CODE, array('perm'))) {
		Service::Billing_Account()->raiseAccountBuildArea($row['id'], Constant_Base::ACCOUNT_TYPE_RO_ID);
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_UPDATE, 'Type_Rosreestr_Flat', function($row) {
    Service::Custom()->saveRosreestrFlat($row);
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_CREATE, 'Type_Billing_Bill_Account', function($row) {
	if(isset($row['home_id'])) {
		if(!isset($row['credit_dt_start'])){
			$dt=new DateTime();
		}else{
			$dt=new DateTime($row['credit_dt_start']);
		}
		// Проверка на закрытый период
		$user = new User();
		$allowFlag = Service::Billing_Period()->allowAct($user->getSessionId(), $dt->format('Y-m-d'));
		if(!$allowFlag) {
			throw new Exception('Поле "Начисления производить с" попадает в закрытый период!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
		}
		$bill_home_filter = new Type_Billing_Home(array('home_id' => $row['home_id']));
		$bill_home = new Type_Billing_Home(null, $bill_home_filter);
		if($bill_home->billing_bill_ro_id) {
			$item = Mikron_Functions::cast($row, 'Type_Billing_Bill_Ro_Account');
			$item->billing_bill_ro_id = $bill_home->billing_bill_ro_id;
			$tableName = 'billing_bill_ro_account';
			$class_name = 'Type_Billing_Bill_Ro_Account';
		} elseif($bill_home->billing_bill_spec_id) {
			$item = Mikron_Functions::cast($row, 'Type_Billing_Bill_Spec_Account');
			$item->billing_bill_spec_id = $bill_home->billing_bill_spec_id;
			$tableName = 'billing_bill_spec_account';
			$class_name = 'Type_Billing_Bill_Spec_Account';
		} else {
			/* У данного дома нет ни одного лицевого счета, по которому можно было-бы определить
			 * расчетный счет (даже РО или СПЕЦ мы не можем определить). 
			 * В этом случае создаем лицевой счет РО. А вот расчетный счет попробуем найти. */
			$tableName = 'billing_bill_ro_account';
			$class_name = 'Type_Billing_Bill_Ro_Account';
			$item = Mikron_Functions::cast($row, 'Type_Billing_Bill_Ro_Account');
			$item->home_id = $row['home_id'];
			$filter = new Type_Billing_Bill_Ro_Filter();
			$all_ro_accounts = Service::Billing_Bill_Ro()->getList(new Paginator('pg', 10000000), null, $filter);
			if(count($all_ro_accounts->items) == 1) {
				// Расчетный счет РО только 1. Привяжемся к нему.
				$billingBillRo = (object) $all_ro_accounts->items[0];
				$item->billing_bill_ro_id = $billingBillRo->id;
			} elseif(count($all_ro_accounts->items) > 1) {
				// Расчетных счетов много. Поищем счет требуемого региона.
				$user = new User();
				$home = Service::Home()->getHomeForEdit($user->getSessionId(), $row['home_id']);
				foreach($all_ro_accounts->items as $node) {
					if($node->locality_id == $home->district_id) {
						$item->billing_bill_ro_id = $node->id;
					};
				}
				if(!isset($item)) {
					throw new Exception('Не могу определить расчетный счет для данного дома.', Constant_Base::DEF_USER_ERROR_CODE);
				}
			} else {
				throw new Exception('У данного дома не заведён лицевой счёт', Constant_Base::DEF_USER_ERROR_CODE);
			}
		}
		if($item->billing_organization_id) {
			$billing_organization = new Type_Billing_Organization($row['billing_organization_id']);
			/*if($billing_organization->billing_organization_type_id != Constant::BILLING_ORG_TYPE_LEGAL) {
				if(Constant::VAR_REGION_CODE != 'lipetsk') {
					$select = Service::Billing()->getCustomerAccounts($row, $tableName);
					if($select != false) {
						throw new Exception('Помещение уже имеется в доме', Constant_Base::DEF_USER_ERROR_CODE);
					}
				}
			}*/
			if(!array_key_exists('flat', $row)) {
				throw new Exception('Недопустимое значение "Номер помещения"', Constant_Base::DEF_USER_ERROR_CODE);
			}
			if(empty($row['credit_dt_start'])) {
				throw new Exception('Укажите с какого месяца производить начисления', Constant_Base::DEF_USER_ERROR_CODE);
			}
			$item->owner = $billing_organization->title;
			$item->credit_dt_start = $row['credit_dt_start'];
		} else {
			$item->billing_organization_id = null;
			$item->credit_dt_start = $dt->format('Y-m-d H:i:s');
		}
		$item->date_open =  date('Y-m-d H:i:s');
		$item->penny_dt_start =  $dt->format('Y-m-d H:i:s');
		$id = Mikron_Entity_Model::create($class_name, $item);
		return $id;
	} else {
		throw new Exception('Выберите дом', Constant_Base::DEF_USER_ERROR_CODE);
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, 'Type_Billing_Bill_Cofinancing_Operation', function($row) {
	$item = new Type_Billing_Bill_Cofinancing_Operation($row);
	if(in_array($item->billing_bill_cofinancing_type_id, array(Constant::BILLING_BILL_COFINANCING_RETURN, Constant::BILLING_BILL_COFINANCING_CONTRACTOR))) {
		$result = Service::Billing()->deleteCofinancingOperation($row);
		if(!$result) {
			throw new Exception('При удаленни операции произошла ошибка', Constant_Base::DEF_USER_ERROR_CODE);
		}
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, 'Type_Billing_Bill_Account', function($row) {
	$ids = explode('.', $row);
	if($ids[0] == Constant::ACCOUNT_TYPE_RO_ID) {
		$entity_name = 'Type_Billing_Bill_Ro_Account';
	} elseif($ids[0] == Constant::ACCOUNT_TYPE_SPEC_ID) {
		$entity_name = 'Type_Billing_Bill_Spec_Account';
	} else {
		throw new Exception('Непредвиденная ошибка', Constant_Base::DEF_USER_ERROR_CODE);
	}
	Mikron_Entity_Model::delete($entity_name, $ids[1]);
	echo json_encode(array('status' => 'success', 'message' => 'Элемент успешно удален'));
	exit;
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, 'Type_Billing_Bill_Ro', function($row) {
	$user = new User();
	if($user->getInfo()->arm_id != Constant::ARM_ID_ADMINISTRATOR) {
		throw new Exception('Недостаточно прав для удаления', Constant_Base::DEF_USER_ERROR_CODE);
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, 'Type_Billing_Bill_Spec', function($row) {
	$user = new User();
	if($user->getInfo()->arm_id != Constant::ARM_ID_ADMINISTRATOR) {
		throw new Exception('Недостаточно прав для удаления', Constant_Base::DEF_USER_ERROR_CODE);
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, 'Type_Billing_Bill_Ro_Account', function($row) {
	$user = new User();
	if($user->getInfo()->arm_id != Constant::ARM_ID_ADMINISTRATOR) {
		throw new Exception('Недостаточно прав для удаления', Constant_Base::DEF_USER_ERROR_CODE);
	}
	// проверка того, то лицевой счет открыт
	$account_filter = new Type_Billing_Bill_Account(
		array('billing_bill_ro_account_id' => $row,	'billing_bill_account_status_id' => Constant_Base::BILLING_BILL_ACCOUNT_STATUS_OPEN,)
	);
	$account = Service::Billing_Account()->getList($account_filter);
	if(empty($account)) {
		throw new Exception('Невозможно удалить лицевой счет. Данный лицевой счет находится в статусе "Закрыт".', Constant_Base::DEF_USER_ERROR_CODE);
	}
	if(!in_array(Constant::VAR_REGION_CODE, array('irkutsk'))) {
		$result = Service::Billing()->checkAccountOperationByRoAccountId($row);
		if($result) {
			throw new Exception('Невозможно удалить лицевой счет. По данному лицевому счету существуют операции.', Constant_Base::DEF_USER_ERROR_CODE);
		}
	}
	$history=Service::Log()->getCriticalLogOperation($user->getSessionId(), 'Type_Billing_Bill_Ro_Account', $row);
	if(!empty($history)){
		throw new Exception('Невозможно удалить лицевой счет. По данному лицевому счету существуют записи в журнале изменений.', Constant_Base::DEF_USER_ERROR_CODE);
	}
	if(in_array(Constant::VAR_REGION_CODE, array('udmurt'))) {
        Service::Billing()->deleteOwner($row, 'ro');
    }
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_DELETE, 'Type_Billing_Bill_Ro_Account', function($row) {
	Model_Apartment::garbageApartment();
});
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_DELETE, 'Type_Billing_Bill_Spec_Account', function($row) {
	Model_Apartment::garbageApartment();
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, 'Type_Billing_Bill_Spec_Account', function($row) {
	$user = new User();
	if($user->getInfo()->arm_id != Constant::ARM_ID_ADMINISTRATOR) {
		throw new Exception('Недостаточно прав для удаления', Constant_Base::DEF_USER_ERROR_CODE);
	}
	// проверка того, то лицевой счет открыт
	$account_filter = new Type_Billing_Bill_Account(
		array('billing_bill_spec_account_id' => $row,	'billing_bill_account_status_id' => Constant_Base::BILLING_BILL_ACCOUNT_STATUS_OPEN,)
	);
	$account = Service::Billing_Account()->getList($account_filter);
	if(empty($account)) {
		throw new Exception('Невозможно удалить лицевой счет. Данный лицевой счет находится в статусе "Закрыт".', Constant_Base::DEF_USER_ERROR_CODE);
	}
	if(!in_array(Constant::VAR_REGION_CODE, array('irkutsk'))) {
		$result = Service::Billing()->checkAccountOperationBySpecAccountId($row);
		if($result) {
			throw new Exception('Невозможно удалить лицевой счет. По данному лицевому счету существуют операции.', Constant_Base::DEF_USER_ERROR_CODE);
		}
	}
	$history = Service::Log()->getCriticalLogOperation($user->getSessionId(), 'Type_Billing_Bill_Spec_Account', $row);
	if(count($history)>0){
		throw new Exception('Невозможно удалить лицевой счет. По данному лицевому счету существуют записи в журнале изменений.', Constant_Base::DEF_USER_ERROR_CODE);
	}    
	if(in_array(Constant::VAR_REGION_CODE, array('udmurt'))) {
        Service::Billing()->deleteOwner($row, 'spec');
    }
});

/**
* @author rail-rz
*/
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_CREATE, 'Type_Billing_Organization', function($row) {
    if(array_key_exists('inn', $row)) {
	    $billing_organization = new Type_Billing_Organization(null, Mikron_Functions::cast(array('inn' => $row['inn']), 'Type_Billing_Organization'));
	    if($billing_organization->id) {
	        throw new Exception('В системе уже существует организация с таким номером ИНН ('.$billing_organization->title_short.')', Constant_Base::DEF_USER_ERROR_CODE);
	    }
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_UPDATE, 'Type_Billing_Organization', function($row) {
    Service::Billing()->updateAccountByOrganiztion($row['id']);
});

/**
* @author rail-rz
*/
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_CREATE, 'Type_Billing_Bill_Ro', function($row) {
    if(empty($row['number'])){
        throw new Exception('Укажите номер счета РО', Constant_Base::DEF_USER_ERROR_CODE);
    } elseif(empty($row['open_date']) || $row['open_date'] == '1970-01-01 03:00:00') {
        throw new Exception('Укажите дату открытия', Constant_Base::DEF_USER_ERROR_CODE);
    } elseif(empty($row['billing_bank_id'])) {
        throw new Exception('Укажите банк', Constant_Base::DEF_USER_ERROR_CODE);
    };
	if(Constant::VAR_REGION_CODE == 'lipetsk') {
		// Проверка номера ШКИ
        if(!isset($row['shki'])) {
            throw new Exception('Поле "Код ШКИ" не должно быть пустым', Constant_Base::DEF_USER_ERROR_CODE);
        }
        $shki = $row['shki'];
		$check_shki = Service::Billing()->getRoByShki($shki);
		if($check_shki) {
			throw new Exception('Данный номер ШКИ занят', Constant_Base::DEF_USER_ERROR_CODE);
		}
	}
});

/**
* @author rail-rz
*/
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_UPDATE, 'Type_Billing_Bill_Ro', function($row) {
    if(empty($row['number'])) {
        throw new Exception('Укажите номер счета РО', Constant_Base::DEF_USER_ERROR_CODE);
    } elseif(empty($row['open_date']) || $row['open_date'] == '1970-01-01 03:00:00') {
        throw new Exception('Укажите дату открытия', Constant_Base::DEF_USER_ERROR_CODE);
    } elseif(empty($row['billing_bank_id'])) {
        throw new Exception('Укажите банк', Constant_Base::DEF_USER_ERROR_CODE);
    };
	if(Constant::VAR_REGION_CODE == 'lipetsk') {
		// Проверка номера ШКИ
		$shki = $row['shki'];
		$list = Service::Billing()->getRoByShki($shki);
		if(count($list) == 1) {
			$account = array_shift($list);
			if($account->id != $row['id']) {
				throw new Exception('Данный номер ШКИ занят', Constant_Base::DEF_USER_ERROR_CODE);
			}
		} elseif(count($list) > 1) {
			throw new Exception('Данный номер ШКИ занят', Constant_Base::DEF_USER_ERROR_CODE);
		}
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, 'Type_Billing_Bill_Ticket', function($row) {
	$user = new User();
	$filter=new Type_Billing_Bill_Ticket();
	$filter->id= intval($row);
	$ticket = Service::Billing_Ticket()->getTicketById($filter);
	if(isset($ticket->apartment_invoice_id) && $ticket->apartment_invoice_id!=null){
		throw new Exception ('Эта квитанция сформирована на помещение. Удаление из лицевого счета запрещено!', 950);
	}
	$dt=new DateTime($ticket->date);
	$allowFlag=Service::Billing_Period()->allowAct($user->getSessionId(),$dt->format('Y-m-d'));
	if(!$allowFlag){
		throw new Exception ('Период закрыт!&nbsp;Операция отменена.', Constant_Base::DEF_USER_ERROR_CODE);
	}
    $service = new \Etton\Service\Queue\Handler\Ticket\Generator();
    $statusDone = $service->getStatusByName(\Etton\Model\Queue\Status::STATUS_DONE);
    $statusErr  = $service->getStatusByName(\Etton\Model\Queue\Status::STATUS_ERROR);
	$ticket_packet_part = new Type_Billing_Bill_Ticket_Packet_Part($ticket->billing_bill_ticket_packet_part_id);
	if($ticket_packet_part && !in_array($ticket_packet_part->status, array($statusDone['id'], $statusErr['id']))) {
		throw new Exception ('Формирование пачки не завершено. Удаление невозможно!.', Constant_Base::DEF_USER_ERROR_CODE);
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_DELETE, 'Type_Billing_Bill_Ticket', function($row) {
	Service::Billing()->deleteNullTicketPart();
	//После удаления тикета приводим в соответствие помещения.	
	Model_Apartment::garbageApartmentInvoice();
});

/**
* Перед сохранением помещений
*/
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_UPDATE, 'Type_Apartment', function($row) {
	$apartment = Functions::cast($row, 'Type_Apartment');
	if(!$apartment->area) {
		throw new Exception('Заполните площадь помещения!', Constant_Base::DEF_USER_ERROR_CODE);
	}
	/*if(!$apartment->cadastr) {
		throw new Exception('Заполните кадастровый номер помещения!', Constant_Base::DEF_USER_ERROR_CODE);
	}*/
	if(!$apartment->flat) {
		throw new Exception('Заполните номер помещения!', Constant_Base::DEF_USER_ERROR_CODE);
	}
	return true;
});

/**
* После сохранения помещений
*/
Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_UPDATE, 'Type_Apartment', function($row) {
	$user = new User();
	$apartment = Functions::cast($row, 'Type_Apartment');
	Service::Apartment()->raiseUpdateAccountsByApartmentUpdating($user->getSessionId(), $apartment);
	return true;
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, 'Type_Home_File', function($row) {
	$file = Service::Billing()->getFileByID($row);
	$file = array_shift($file);
	if($file == NULL) {
		throw new Exception("Файл не найден", 950);
	}
	// Удаляем сам файл
	$filepath = dirname(__FILE__) . "/../../project/billing/public/upload/homefiles/{$file->file_path}";
	if(file_exists($filepath)) {
		unlink($filepath);
	}
});

Mikron_Crud::addTrigger(Mikron_Crud::TRIGGER_PRE_DELETE, 'Type_Home_File_Type', function($row) {
	$files = Service::Billing()->getFileListByTypeId($row);
	if(!empty($files)){
		throw new Exception ('Данный тип содержит документы. Операция отменена!', 950);
	}
});
