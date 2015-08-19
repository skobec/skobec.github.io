<?php

class Mikron_List_Specaccount {
	/**
	 * Получение меню для отображение спец.счетов
	 * @author Rinat_M
	 * @since 2015-04-16
	 * 
	 * @param string $session_id
	 * @return array
	 */
	public static function getList($session_id) {
		$field_list = array();
		switch (Constant::VAR_REGION_CODE) {
			case "irkutsk":
				$field_list = array(
					'number',
					'billing_bill_status_id',
					'home_id',
					'open_date',
					new Mikron_Entity_Field(array(
						'name' => 'owner_organization',
						'path' => 'owner_organization/title',
						'description' => 'Владелец специального счёта',
						'link' => 'Type_Owner_Organization',
						'filter' => array(
							'arm/id' => array(Constant::ARM_ID_REGIONALOPERATOR, Constant::ARM_ID_MANAGEMENT, Constant::ARD_ID_BOOKKEEPER),
						),
					)),
					new Mikron_Entity_Field(array(
						'name' => 'name_bank',
						'path' => 'billing_bank/title',
						'description' => 'Наименование банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'inn_bank',
						'path' => 'billing_bank/inn',
						'description' => 'ИНН банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'kpp',
						'path' => 'billing_bank/kpp',
						'description' => 'КПП банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'kor_bank',
						'path' => 'billing_bank/kor',
						'description' => 'Кор. счет банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'summa_on_bill',
						'description' => 'Сумма на счёте',
						'type' => 'float',
					)),
				);
				break;
			case "omsk":
				$field_list = array(
					'number',
					'billing_bill_status_id',
					'home_id',					
					new Mikron_Entity_Field(array(
						'name' => 'owner_organization',
						'path' => 'owner_organization/title',
						'description' => 'Владелец специального счёта',
						'link' => 'Type_Owner_Organization',
						'filter' => array(
							'arm/id' => array(Constant::ARM_ID_REGIONALOPERATOR, Constant::ARM_ID_MANAGEMENT, Constant::ARD_ID_BOOKKEEPER),
						),
					)),
					'open_date',
					'close_date',
					new Mikron_Entity_Field(array(
						'name' => 'name_bank',
						'path' => 'billing_bank/title',
						'description' => 'Наименование банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'inn_bank',
						'path' => 'billing_bank/inn',
						'description' => 'ИНН банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'kpp',
						'path' => 'billing_bank/kpp',
						'description' => 'КПП банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'kor_bank',
						'path' => 'billing_bank/kor',
						'description' => 'Кор. счет банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'summa_on_bill',
						'description' => 'Сумма на счёте',
						'type' => 'float',
					)),					
				);
				break;
			case "kaliningrad":
				$field_list = array(
					'number',
					'billing_bill_status_id',
					new Mikron_Entity_Field(array(
						'name' => 'locality_id',
						'description' => 'Муниципальный район',
						'type' => 'virtual',
						'format' => function($row) use($session_id) {
							$filter = new Type_Home_Filter(array('id' => $row['home_id']));
							$paginator = new Paginator('p', Constant::DEF_ITEMS_PER_PAGE);
							$home = Service::Admin_Home()->getList($session_id, $paginator, null, $filter);
							if($home) {
								$home = $home->items;
								$home = current($home);
								return ($home->region_prefix_short && $home->region_title) ? $home->region_prefix_short . ". " . $home->region_title : '';
							}
							return;
						},
					)),
					new Mikron_Entity_Field(array(
						'name' => 'district_name',
						'type' => 'string',
						'path' => 'home/house/locality/title',
						'description' => 'Город',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'street_name',
						'type' => 'string',
						'path' => 'home/house/street/title',
						'description' => 'Улица',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'house_number',
						'type' => 'string',
						'path' => 'home/house/number',
						'description' => 'Дом',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'block_number',
						'type' => 'string',
						'path' => 'home/house/block',
						'description' => 'Корпус',
					)),
					'open_date',
					new Mikron_Entity_Field(array(
						'name' => 'owner_organization',
						'path' => 'owner_organization/title',
						'description' => 'Владелец специального счёта',
						'link' => 'Type_Owner_Organization',
						'filter' => array(
							'arm/id' => array(Constant::ARM_ID_REGIONALOPERATOR, Constant::ARM_ID_MANAGEMENT, Constant::ARD_ID_BOOKKEEPER),
						),
					)),
					new Mikron_Entity_Field(array(
						'name' => 'name_bank',
						'path' => 'billing_bank/title',
						'description' => 'Наименование банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'summa_on_bill',
						'description' => 'Сумма на счёте',
						'type' => 'float',
					)),
				);
				break;
			default :
				$field_list = array(
					'number',
					'billing_bill_status_id',
					new Mikron_Entity_Field(array(
						'name' => 'district_name',
						'type' => 'string',
						'path' => 'home/house/locality/title',
						'description' => 'Город',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'street_name',
						'type' => 'string',
						'path' => 'home/house/street/title',
						'description' => 'Улица',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'house_number',
						'type' => 'string',
						'path' => 'home/house/number',
						'description' => 'Дом',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'block_number',
						'type' => 'string',
						'path' => 'home/house/block',
						'description' => 'Корпус',
					)),
					'open_date',
					new Mikron_Entity_Field(array(
						'name' => 'owner_organization',
						'path' => 'owner_organization/title',
						'description' => 'Владелец специального счёта',
						'link' => 'Type_Owner_Organization',
						'filter' => array(
							'arm/id' => array(Constant::ARM_ID_REGIONALOPERATOR, Constant::ARM_ID_MANAGEMENT, Constant::ARD_ID_BOOKKEEPER),
						),
					)),
					new Mikron_Entity_Field(array(
						'name' => 'name_bank',
						'path' => 'billing_bank/title',
						'description' => 'Наименование банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'inn_bank',
						'path' => 'billing_bank/inn',
						'description' => 'ИНН банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'kpp',
						'path' => 'billing_bank/kpp',
						'description' => 'КПП банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'kor_bank',
						'path' => 'billing_bank/kor',
						'description' => 'Кор. счет банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'summa_on_bill',
						'description' => 'Сумма на счёте',
						'type' => 'float',
					)),
					'open_date',
					new Mikron_Entity_Field(array(
						'name' => 'owner_organization',
						'path' => 'owner_organization/title',
						'description' => 'Владелец специального счёта',
						'link' => 'Type_Owner_Organization',
						'filter' => array(
							'arm/id' => array(Constant::ARM_ID_REGIONALOPERATOR, Constant::ARM_ID_MANAGEMENT, Constant::ARD_ID_BOOKKEEPER),
						),
					)),
					new Mikron_Entity_Field(array(
						'name' => 'name_bank',
						'path' => 'billing_bank/title',
						'description' => 'Наименование банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'inn_bank',
						'path' => 'billing_bank/inn',
						'description' => 'ИНН банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'kpp',
						'path' => 'billing_bank/kpp',
						'description' => 'КПП банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'kor_bank',
						'path' => 'billing_bank/kor',
						'description' => 'Кор. счет банка',
						'link' => 'Type_Billing_Bank',
					)),
					new Mikron_Entity_Field(array(
						'name' => 'summa_on_bill',
						'description' => 'Сумма на счёте',
						'type' => 'float',
					)),
				);
				break;
		}
		return $field_list;
	}
}

