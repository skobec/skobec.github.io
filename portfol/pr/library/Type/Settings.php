<?php

class Type_Settings extends Mikron_Type {
	
	/**
	 * Печатать квитанции для ФЛ
	 * 
	 * @var bool
	 */
	public $individual_person_ticket_printing;
	
	/**
	 * Печатать квитанции для ЮЛ
	 * 
	 * @var bool
	 */
	public $legal_person_ticket_printing;

	/**
	 * Логика группировки домов по коду ОКТМО для модуля ФРП
	 * 
	 * @var bool
	 */
	public $program_oktmo_logic;

	/**
	 * Тестовый проект
	 * 
	 * @var bool
	 */
	public $is_test;

	/**
	 * Формирование начисления по дням
	 *
	 * @var bool
	 */
	public $formation_accrual_per_date;

	/**
	 * Формирование списка помещений
	 *
	 * @var bool
	 */
	public $need_apartment;

	/**
	 * Число в месяце, до которого собственники должны оплатить квитанции
	 * @var int
	 */
	public $peni_start_date;

}