<?php

/*
if(VAR_REGION_CODE == 'irkutsk') {
	State::$state_list
}*/

/**
* @author sciner
* @since 2014-08-06
*/
class State {

	const STATE_ID_MJF_LIST = 'mjf_list111'; // Список домов в МЖФ

	static $state_list = array(
		// Список домов в МЖФ
		array(
			'id' => self::STATE_ID_MJF_LIST,
			'field_list' => array(
    			// array('code' => 'id', 					'title' => 'Уникальный ID дома', 					'visible' => false),
    			array('code' => 'number',					'title' => 'Уникальный номер дома', 				'visible' => false),
    			array('code' => 'region_title', 			'title' => 'Муниципальный район', 					'visible' => true),
    			array('code' => 'municipal_title', 			'title' => 'Муниципальное образование.', 			'visible' => false),
    			// array('code' => 'adm_okrug', 				'title' => 'Муниципальный район', 					'visible' => false),
    			// array('code' => 'munic_obr', 				'title' => 'Муниципальное образование', 			'visible' => false),
    			array('code' => 'locality_prefix_short', 	'title' => 'Тип населённого пункта', 				'visible' => false),
    			array('code' => 'locality_title', 			'title' => 'Населённый пункт', 						'visible' => true),
    			array('code' => 'street_prefix_short',		'title' => 'Тип улицы', 							'visible' => false),
    			array('code' => 'street_title', 			'title' => 'Наименование улицы', 					'visible' => false),
    			array('code' => 'address_string', 			'title' => 'Адрес', 								'visible' => true),
    			array('code' => 'street_cladr_code',		'title' => 'Код улицы КЛАДР', 						'visible' => false),
    			array('code' => 'house_number',				'title' => 'Номер дома', 							'visible' => false),
    			// -- array('code' => '', 					'title' => 'Район города', 							'visible' => false),
    			// -- array('code' => '', 					'title' => 'Способ управления', 					'visible' => false),
    			array('code' => 'commis_year', 				'title' => 'Год ввода в эксплуатацию', 				'visible' => true),
    			array('code' => 'stages', 					'title' => 'Этажность',								'visible' => true),
    			array('code' => 'porches', 					'title' => 'Кол-во подъездов', 						'visible' => true),
    			array('code' => 'account_count',			'title' => 'Кол-во лицевых счетов', 				'visible' => false),
    			array('code' => 'area', 					'title' => 'Общая площадь', 						'visible' => true),
    			array('code' => 'condition', 				'title' => 'Состояние дома', 						'visible' => true),
    			array('code' => 'wearout', 					'title' => 'Процент износа', 						'visible' => true),
    			array('code' => 'fill', 					'title' => 'Процент заполненности паспорта МКД', 	'visible' => true),
    			array('code' => 'management_organization', 	'title' => 'Наименование УК', 						'visible' => true),
    			array('code' => 'cadastr_number', 			'title' => 'Кадастровый номер', 					'visible' => false),
                array('code' => 'oktmo_munic_district', 	'title' => 'ОКТМО муниципального района', 		    'visible' => false),
                array('code' => 'oktmo_munic_obr', 	        'title' => 'ОКТМО муниципального образования', 		'visible' => false),
			)
		)
	);

	static $state_list_yanao = array(
		// Список домов в МЖФ
		array(
			'id' => self::STATE_ID_MJF_LIST,
			'field_list' => array(
				// array('code' => 'id', 					'title' => 'Уникальный ID дома', 					'visible' => false),
				array('code' => 'number',					'title' => 'Уникальный номер дома', 				'visible' => false),
				array('code' => 'region_title', 			'title' => 'Муниципальный район', 					'visible' => true),
				array('code' => 'municipal_title', 			'title' => 'Муниципальное образование.', 			'visible' => false),
				// array('code' => 'adm_okrug', 				'title' => 'Муниципальный район', 					'visible' => false),
				// array('code' => 'munic_obr', 				'title' => 'Муниципальное образование', 			'visible' => false),
				array('code' => 'locality_prefix_short', 	'title' => 'Тип населённого пункта', 				'visible' => false),
				array('code' => 'locality_title', 			'title' => 'Населённый пункт', 						'visible' => true),
				array('code' => 'street_prefix_short',		'title' => 'Тип улицы', 							'visible' => false),
				array('code' => 'street_title', 			'title' => 'Наименование улицы', 					'visible' => false),
				array('code' => 'address_string', 			'title' => 'Адрес', 								'visible' => true),
				array('code' => 'street_cladr_code',		'title' => 'Код улицы КЛАДР', 						'visible' => false),
				array('code' => 'house_number',				'title' => 'Номер дома', 							'visible' => false),
				// -- array('code' => '', 					'title' => 'Район города', 							'visible' => false),
				// -- array('code' => '', 					'title' => 'Способ управления', 					'visible' => false),
				array('code' => 'commis_year', 				'title' => 'Год ввода в эксплуатацию', 				'visible' => true),
				array('code' => 'stages', 					'title' => 'Этажность',								'visible' => true),
				array('code' => 'porches', 					'title' => 'Кол-во подъездов', 						'visible' => true),
				array('code' => 'account_count',			'title' => 'Кол-во лицевых счетов', 				'visible' => false),
				array('code' => 'area', 					'title' => 'Общая площадь', 						'visible' => true),
				array('code' => 'condition', 				'title' => 'Состояние дома', 						'visible' => true),
				array('code' => 'wearout', 					'title' => 'Процент износа', 						'visible' => true),
				array('code' => 'fill', 					'title' => 'Процент заполненности паспорта МКД', 	'visible' => true),
				array('code' => 'management_organization', 	'title' => 'Наименование УК', 						'visible' => true),
				array('code' => 'cadastr_number', 			'title' => 'Кадастровый номер', 					'visible' => false),
				array('code' => 'oktmo_munic_district', 	'title' => 'ОКТМО муниципального района', 		    'visible' => false),
				array('code' => 'oktmo_munic_obr', 	        'title' => 'ОКТМО муниципального образования', 		'visible' => false),

				array('code' => 'address_number', 			'title' => 'Код МКД (уникальный код дома)', 		'visible' => false),
				array('code' => 'info_year', 'title' => ' Год ввода в эксплуатацию', 'visible' => false),
				array('code' => 'info_full_area', 'title' => ' Общая площадь МКД, всего', 'visible' => false),
				array('code' => 'roomsinfo_count_rooms', 'title' => ' Количество помещений МКД, всего', 'visible' => false),
				array('code' => 'rooms_count_full', 'title' => ' Количество жилых помещений МКД (квартир)', 'visible' => false),
				array('code' => 'roomsinfo_area_rooms', 'title' => ' Площадь помещений МКД', 'visible' => false),
				array('code' => 'rooms_common', 'title' => ' Площадь жилых помещений МКД', 'visible' => false),
				array('code' => 'roomsinfo_count_gosrooms', 'title' => ' Количество помещений МКД в Государственной собственности ', 'visible' => false),
				array('code' => 'roomsinfo_count_munrooms', 'title' => ' Количество помещений МКД в Муниципальной собственности ', 'visible' => false),
				array('code' => 'roomsinfo_count_privrooms', 'title' => ' Количество помещений МКД в собственности физ.лица, юр.лица - за искл. Бюджетных ', 'visible' => false),
				array('code' => 'roomsinfo_count_vedrooms', 'title' => ' Количество помещений МКД в Ведомственной собственности ', 'visible' => false),
				array('code' => 'roomsinfo_area_gosrooms', 'title' => ' Площадь помещений МКД в Государственной собственности ', 'visible' => false),
				array('code' => 'roomsinfo_area_munrooms', 'title' => ' Площадь помещений МКД в Муниципальной собственности ', 'visible' => false),
				array('code' => 'roomsinfo_area_privrooms', 'title' => ' Площадь помещений МКД в собственности физ.лица, юр.лица - за искл. Бюджетных', 'visible' => false),
				array('code' => 'roomsinfo_area_vedrooms', 'title' => ' Площадь помещений МКД в Ведомственной собственности ', 'visible' => false),
				array('code' => 'info_floor_count', 'title' => ' Кол-во этажей', 'visible' => false),
				array('code' => 'info_porch_count', 'title' => ' Кол-во подъездов', 'visible' => false),
				array('code' => 'info_tenants_count', 'title' => ' Количество проживающих', 'visible' => false),
				array('code' => 'ri_destroy', 'title' => ' Техническое состояние МКД', 'visible' => false),
				array('code' => 'info_wearout', 'title' => ' Общий износ здания (по данным технической инвентаризации) ', 'visible' => false),
				array('code' => 'info_sgtr', 'title' => ' Степень износа крышы здания', 'visible' => false),
				array('code' => 'info_dfgeyey', 'title' => ' Степень износа несущих стен здания ', 'visible' => false),
				array('code' => 'info_dfgd', 'title' => ' Степень износа фундамента здания', 'visible' => false),
				array('code' => 'info_runout', 'title' => ' Степень физического износа внутридомовых инженерных систем', 'visible' => false),
				array('code' => 'info_wwt', 'title' => ' Наличие угрозы безопасности жизни или здоровью граждан', 'visible' => false),
				array('code' => 'info_data_privat', 'title' => ' Дата приватизации первого жилого помещения', 'visible' => false),
				array('code' => 'reforma_lifts', 'title' => ' Количество лифтов', 'visible' => false),
				array('code' => 'reforma_year_lifts', 'title' => ' Год последнего капитального ремонта лифтов', 'visible' => false),
				array('code' => 'reforma_konstr_bilding', 'title' => ' Конструктив здания (деревянное исполнение, капитальное исполнение)', 'visible' => false),
				array('code' => 'reforma_woodarea', 'title' => ' Площадь стен деревянных', 'visible' => false),
				array('code' => 'reforma_shlackarea', 'title' => ' Площадь стен из легких шлакоблоков', 'visible' => false),
				array('code' => 'reforma_bricksarea', 'title' => ' Площадь стен кирпичных', 'visible' => false),
				array('code' => 'reforma_area_bigblock', 'title' => ' Площадь стен крупноблочных', 'visible' => false),
				array('code' => 'reforma_paneldo5area', 'title' => ' Площадь стен крупнопанельных (до 5 этажей)', 'visible' => false),
				array('code' => 'reforma_panelbolee5area', 'title' => ' Площадь стен крупнопанельных (более 5 этажей)', 'visible' => false),
				array('code' => 'reforma_monolit_area', 'title' => ' Площадь стен монолитных', 'visible' => false),
				array('code' => 'reforma_mix_area', 'title' => ' Площадь стен смешанных', 'visible' => false),
				array('code' => 'reforma_year', 'title' => ' Год последнего капитального ремонта фасада', 'visible' => false),
				array('code' => 'reforma_area_krov', 'title' => ' Площадь кровли общая', 'visible' => false),
				array('code' => 'reforma_area_shif', 'title' => ' Площадь кровли шиферная скатная', 'visible' => false),
				array('code' => 'reforma_area_metallk', 'title' => ' Площадь кровли металлическая скатная', 'visible' => false),
				array('code' => 'reforma_area_skat', 'title' => ' Площадь кровли иная скатная', 'visible' => false),
				array('code' => 'reforma_area_plos', 'title' => ' Площадь кровли плоская', 'visible' => false),
				array('code' => 'reforma_yaer2', 'title' => ' Год последнего капитального ремонта кровли', 'visible' => false),
				array('code' => 'reforma_type_fnd', 'title' => ' Тип фундамента', 'visible' => false),
				array('code' => 'reforma_fund_year', 'title' => ' Год последнего капитального ремонта фундамента', 'visible' => false),
				array('code' => 'reforma_area_podval', 'title' => ' Площадь подвала', 'visible' => false),
				array('code' => 'reforma_year3', 'title' => ' Год последнего капитального ремонта подвала', 'visible' => false),
				array('code' => 'reforma_type', 'title' => ' Тип системы теплоснабжения', 'visible' => false),
				array('code' => 'reforma_light', 'title' => ' Длина трубопроводов системы теплоснабжения', 'visible' => false),
				array('code' => 'reforma_year6', 'title' => ' Год последнего капитального ремонта системы теплоснабжения', 'visible' => false),
				array('code' => 'reforma_type2', 'title' => ' Тип системы ГВС', 'visible' => false),
				array('code' => 'reforma_light_gor', 'title' => ' Длина трубопроводов системы теплоснабжения системы ГВС', 'visible' => false),
				array('code' => 'reforma_year_gor', 'title' => ' Год последнего капитального ремонта системы ГВС', 'visible' => false),
				array('code' => 'reforma_typecold', 'title' => ' Тип системы ХВС', 'visible' => false),
				array('code' => 'reforma_leight_cold', 'title' => ' Длина трубопроводов системы теплоснабжения системы ХВС', 'visible' => false),
				array('code' => 'reforma_yearcold', 'title' => ' Год последнего капитального ремонта системы ХВС', 'visible' => false),
				array('code' => 'reforma_type_vent', 'title' => ' Тип сиситемы вентиляции', 'visible' => false),
				array('code' => 'reforma_leight_vent', 'title' => ' Длина сиситемы вентиляции', 'visible' => false),
				array('code' => 'reforma_year_vent', 'title' => ' Год последнего капитального ремонта системы вентиляции', 'visible' => false),
				array('code' => 'reforma_type_el', 'title' => ' Тип системы электроснабжения', 'visible' => false),
				array('code' => 'reforma_light_el', 'title' => ' Длина электросетей в местах общего пользования', 'visible' => false),
				array('code' => 'reforma_year_el', 'title' => ' Год последнего капитального ремонта системы электроснабжения', 'visible' => false),
				array('code' => 'reforma_type_gaz', 'title' => ' Тип системы газоснабжения', 'visible' => false),
				array('code' => 'reforma_leight_nett', 'title' => ' Длина трубопроводов системы газоснабжения', 'visible' => false),
				array('code' => 'reforma_year_gas', 'title' => ' Год последнего капитального ремонта системы газоснабжения', 'visible' => false),
				array('code' => 'reforma_typ_water', 'title' => ' Тип системы водоотведения', 'visible' => false),
				array('code' => 'reforma_number_p', 'title' => ' Количество общедомовых прибор учета тепловой энергии', 'visible' => false),
				array('code' => 'reforma_pribor_teplo_year', 'title' => ' Год установки (замены) общедомовых прибор учета тепловой энергии', 'visible' => false),
				array('code' => 'reforma_number_prel', 'title' => ' Количество общедомовых приборов учета электрической энергии', 'visible' => false),
				array('code' => 'reforma_pribor_el_year', 'title' => ' Год установки (замены) общедомовых прибор учета электрической энергии', 'visible' => false),
				array('code' => 'reforma_number_pcold', 'title' => ' Количество общедомовых прибор учета ХВС', 'visible' => false),
				array('code' => 'reforma_pribor_hvs_year', 'title' => ' Год установки (замены) общедомовых прибор учета ХВС', 'visible' => false),
				array('code' => 'reforma_number_phot', 'title' => ' Количество общедомовых прибор учета ГВС', 'visible' => false),
				array('code' => 'reforma_pribor_gvs_year', 'title' => ' Год установки (замены) общедомовых прибор учета ГВС', 'visible' => false),
				array('code' => 'reforma_number_pgas', 'title' => ' Количество общедомовых прибор учета газоснабжения', 'visible' => false),
				array('code' => 'reforma_pribor_gaz_year', 'title' => ' Год установки (замены) общедомовых приборов учета газоснабжения', 'visible' => false),
				array('code' => 'reforma_pribor_kv_teplo', 'title' => ' Количество поквартирных приборов учета тепло-вой энергии', 'visible' => false),
				array('code' => 'reforma_pribor_kv_el', 'title' => ' Количество поквартирных приборов учета электрической энергии', 'visible' => false),
				array('code' => 'reforma_pribor_kv_hvs', 'title' => ' Количество поквартирных приборов учета ХВС', 'visible' => false),
				array('code' => 'reforma_pribor_kv_gvs', 'title' => ' Количество поквартирных приборов учета ГВС', 'visible' => false),
				array('code' => 'reforma_pribor_kv_gas', 'title' => ' Количество поквартирных приборов учета газоснабжения', 'visible' => false),
				array('code' => 'di_title', 'title' => 'Способ управления (наименование УК, ТСЖ)', 'visible' => false),
				array('code' => 'di_number', 'title' => '№. дата договора обслуживания', 'visible' => false),
				array('code' => 'reforma_leight_water', 'title' => 'Длина трубопроводов системы водоотведения', 'visible' => false),
				array('code' => 'reforma_year_water', 'title' => 'Год последнего капитального ремонта системы водоотведения', 'visible' => false),
			)
		)
	);

	private static function getDefaultState($id) {
		$states = (Constant::VAR_REGION_CODE == 'yanao') ? self::$state_list_yanao : self::$state_list;
		foreach($states as $state) {
			if($state['id'] == $id) {
				return json_decode(json_encode($state));
			}
		}
		return null;
	}
	
	static function saveViewState($session_id, $state) {
		$state = (object)$state;
		$id = $state->id;
		$current_state = self::getViewState($session_id, $id);
		$current_state->field_order = $state->field_order;
		// $_SESSION['view_state_'.$id] = $current_state;
		// dumpre($current_state->field_order);
		Service::User()->setVariable($session_id, 'view_state_'.$id, $current_state);
	}

	static function getId($state) {
		return md5(json_encode($state, JSON_UNESCAPED_UNICODE));
	}

	static function getViewState($session_id, $id) {
		$default_state = self::getDefaultState($id);
		$ss = Service::User()->getVariable($session_id, 'view_state_'.$id);
		// $ss = isset($_SESSION['view_state_'.$id]) ? $_SESSION['view_state_'.$id] : null;
		// dumpr($ss);
		$state = $ss ?: $default_state;
			$new_field_list = array();
			if(isset($state->field_order) && is_object($state->field_order)) {
				// перестроение списка полей
				foreach($state->field_order as $field_code => $visible) {
					foreach($state->field_list as $index => $field) {
						if($field->code == $field_code) {
							$field->visible = $visible;
							$new_field_list[] = $field;
							unset($state->field_list[$index]);
							continue;
						}
					}
				}
				$state->field_list = $new_field_list ?: $state->field_list;
				foreach($default_state->field_list as $field) {
					$field = (object)$field;
					$found = false;
					foreach($state->field_list as $new_field) {
						if($new_field->code == $field->code) {
							$found = true;
							break;
						}
					}
					if(!$found) {
						$state->field_list[] = $field;
					}
				}
			}
		return $state;
	}

	static function drawForm($state) {
		?>
			<script>
		        $(function() {
			        $('.select-field-list').sortable({
						handle: '.icon-move'
			        });
			        $('#form-view-state-<?=$state->id?>').disableSelection();
			        $('#form-view-state-<?=$state->id?>').data('success', function(data){
						location.reload();
						return false;
			        })
		        });
		    </script>
		    <style>
		    	#form-view-state-<?=$state->id?> {
					cursor: default;
		    	}
    			.select-field-list {
					list-style-type: none;
					padding: 0;
					margin: 0;
    			}
    			.select-field-list li label {
    				border: 3px solid transparent;
    				display: inline-block;
				}
    			.select-field-list li {
    				margin: 0;
    				display: block;
				}
    			.select-field-list li:hover {
    				background-color: rgba(128, 128, 128, .3);
				}
    			.select-field-list li label input {
					margin-right: 1em !important;
    			}
    			.select-field-list li .icon-move {
					background-color: #3586D0;
					border: 3px solid #3586D0;
					cursor: move;
					margin-right: 10px;
    			}
		    </style>
		    <form class="popup-form popup-ajax" method="POST" action="/list/saveviewstate/" id="form-view-state-<?=$state->id?>" title="Выбор отображаемых столбцов">
    			<input type="hidden" name="form[id]" value="<?=$state->id?>">
    			<div class="popup-form-content" style="overflow: scroll">
    				<ul class="select-field-list">
    					<? foreach($state->field_list as $field) {
                            //if((in_array($field->code, array('adm_okrug', 'munic_obr')) && Constant::VAR_REGION_CODE != 'irkutsk') || (Constant::VAR_REGION_CODE == 'irkutsk' && in_array($field->code, array('municipal_title', 'region_title')))) {
                            //    continue;
                            //}
                			 ?><li><i class="icon icon-move icon-white"></i><label>
                				<input type="hidden" name="form[field_order][<?=htmlspecialchars($field->code)?>]" value="0" />
                				<? if($field->visible) { ?>
                					<input type="checkbox" name="form[field_order][<?=htmlspecialchars($field->code)?>]" value="1" checked="checked" />
                				<? } else { ?>
                					<input type="checkbox" name="form[field_order][<?=htmlspecialchars($field->code)?>]" value="1" />
                				<? } ?>
                				<?=htmlspecialchars($field->title)?>
                			</label></li><? 
		                } ?>
    				</ul>
    			</div>
    			<div class="popup-form-footer">
    				<a class="btn btn-primary button-submit">Ok</a>
    				<a class="btn button-cancel" onclick="return CustomUI.hideForm();">Отмена</a>
    			</div>
		    </form>
    	<?
	}
}