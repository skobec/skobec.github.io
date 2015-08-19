<?php

class Mikron_App_Helper {

    /**
    * Вывод кнопки добавить и списка сущности с пагинатором, фильтром и сортировкой
    * 
    * @param string $type
    * @param mixed $edit_link
    * @param string $text_btn_add
    * @param Mikron_Entity_Designer_List_Params $list_params
    * @param mixed $extra_fields
    * @param mixed $add_form
    * @param mixed $field_list
    * @param mixed $form_field_list
    * @param mixed $filter
    * @param mixed[] $read_only_values Поля только для чтения в форме создания или редактирования объекта
    * @param Mikron_Entity_Designer_Form_Params $form_params
    * @param bool $can_delete
    * @param bool $pg_toolbar_numerator
    */
    static function showList(
        $type,
        $edit_link,
        $text_btn_add = null,
        $list_params = null,
        $extra_fields = null,
        $add_form = true,
        $field_list = null,
        $form_field_list = null,
        $filter = null,
        $read_only_values = null,
        $form_params = null,
        $can_delete = null,
        $pg_toolbar_numerator = null
    )
    {
		$field_list = is_array($field_list) ? array_filter($field_list) : null;
		$form_field_list = is_array($form_field_list) ? array_filter($form_field_list) : null;
        if($add_form) {
            $form_params_def = new Mikron_Entity_Designer_Form_Params(
                array(
                    'popup' => true,
                    'text_btn_add' => $text_btn_add,
                    'field_list' => $form_field_list,
                    'read_only_values' => $read_only_values,
                )
            );
            if($form_params) {
                $form_params = (array)$form_params;
                foreach($form_params as $key => $value) {
                    if($value !== null) {
                        if($value !== null) {
                            if(is_array($value)) {
                                if(count($value)) {
                                    $form_params_def->$key = $value;
                                }
                            } else {
                                $form_params_def->$key = $value;
                            }
                        }
                    }
                }
            }
            Mikron_Entity_Designer::drawForm(new $type, null, $form_params_def);
        }
        $list_params_def = new Mikron_Entity_Designer_List_Params(
            array(
                'edit_link' => $edit_link,
                'field_list' => $field_list,
                'extra_fields' => $extra_fields,
                'filter' => $filter,
                'can_edit' => !is_null($edit_link),
                'can_delete' => isset($can_delete) ? (bool)$can_delete : !is_null($edit_link),
                'edit_popup' => !is_null($edit_link),
                'toolbar_numerator' => $pg_toolbar_numerator,
            )
        );
        if($list_params) {
            $list_params = (array)$list_params;
            foreach($list_params as $key => $value) {
                if($value !== null) {
                    if(is_array($value)) {
                        if(count($value)) {
                            $list_params_def->$key = $value;
                        }
                    } else {
                        $list_params_def->$key = $value;
                    }
                }
            }
        }
        return Mikron_Entity_Designer::drawList($type, md5($type.$edit_link.strtok($_SERVER['REQUEST_URI'], '/?')), null, $list_params_def);
    }

    static function showForm($type) {
        Mikron_Entity_Designer::drawForm(new $type, null, new Mikron_Entity_Designer_Form_Params(array('popup' => true)));
    }

    static function showFormPopup($type, $field_list, $read_only_values, $text_btn_add = 'Добавить...', $text_caption = null) {
        $arr = array(
            'popup' => true,
            'field_list' => $field_list,
            'text_btn_add' => $text_btn_add,
            'read_only_values' => $read_only_values);
        if($text_caption) {
            $arr['text_caption'] = $text_caption;
        }
        return Mikron_Entity_Designer::drawForm(new $type, null, new Mikron_Entity_Designer_Form_Params($arr));
    }

    static function editFormPopup($type, $id, $field_list, $read_only_values, $text_caption = null) {
        $arr = array(
            'popup' => true,
            'field_list' => $field_list,
            'read_only_values' => $read_only_values,);
        if($text_caption) {
            $arr['text_caption'] = $text_caption;
        }
        Mikron_Entity_Designer::drawForm(new $type($id), null, new Mikron_Entity_Designer_Form_Params($arr));
    }

    /**
     * Форма редактирвоания сущности в попапе
     * @author sciner
     */
    static function editForm($type, $id, $form_field_list = null, $readonly = null, $read_only_values = null, $hide_fielder = null) {
		$form_field_list = is_array($form_field_list) ? array_filter($form_field_list) : null;
		?><table><tr><td style="vertical-align: top;"><?
        if(Functions::isReadOnlyMode()) {
        	$readonly = true;
		}
        $form_id = Mikron_Entity_Designer::drawForm(new $type($id), null, new Mikron_Entity_Designer_Form_Params(array(
            'popup' => false, 
            'field_list' => $form_field_list, 
            'not_save' => $readonly, 
            'readonly' => $readonly, 
            'read_only_values' => $read_only_values
            )),                
            $hide_fielder);
        ?></td><td style="vertical-align: top; padding: 1em 0 0 2em;">
        	<?php
        	if(true) { // !defined('READ_ONLY_MODE')) {
        		// История операций
        		$user = new User();
        		$history = Service::Log()->getList($user->getSessionId(), $type, $id);
        		$history = $history->items;
        		if(count($history)) {
        			$button_id = 'mikron-history-button-'.mt_rand(10000, 9999999);
        			$content_id = 'mikron-history-table-'.mt_rand(10000, 9999999);
					?>
					<div id="<?=$content_id?>" style="display: none;">
						<div style="max-height: 400px; overflow: auto;">
							<table class="table table-condensed table-striped table-bordered table-bordered-glow" style="margin: 0em;"><thead><tr>
								<th>ФИО</th>
								<th>Дата</th>
								<th>Операция</th>
								<th>Изменено</th>
								<th>Причина</th>
								<th>Основание</th>
							</tr></thead><tbody><?php
        					foreach($history as $i => $item) {
								?>
									<tr>
										<td><?=htmlspecialchars($item->fio)?></td>
										<td><?=Format::date($item->dt, true, true, true)?></td>
										<td><?= $item->operation_title; ?></td>
										<td style="padding: 0px;"><?=$item->change_text ? $item->change_text : '<div style="padding: 4px 5px;">'.Constant_Base::NO_VALUE.'</div>' ?></td>
										<td><?=Constant_Base::NO_VALUE?></td>
										<td><?=Constant_Base::NO_VALUE?></td>
									</tr>
								<?php
        					}
							?></tbody></table>
						</div>
					</div>

					<? Mikron_Entity_Designer_Toolbar::addTag('a', array('class' => 'btn',
						'id' => $button_id,
						'rel' => 'popover',
						'data-toogle' => 'popover',
						'data-original-title' => 'Журнал изменений',
						), 'Журнал изменений') ?>

      				<script src="/bootstrap/bootstrap-popover.js"></script>
      				<style>
      					.popover {
							max-width: 900px !important;
      					}
      					.popover-title {
							display: none;
      					}
      					.popover-content, .popover-content p {
							padding: 0px;
							margin: 0px;
      					}
      					.table-bordered-glow {
							border: none;
      					}
      					.table-bordered-glow thead th, .table-bordered-glow tbody td {
      						font-size: 8pt;
						}
      					.table-bordered-glow tbody td {
      						background: #fff;
						}
      					.table-bordered-glow tbody td:first-child, .table-bordered-glow thead th:first-child {
      						border-left: none;
						}
      				</style>

					<script>
						$(function(){
							$('#<?=$button_id?>').popover({placement: 'bottom', trigger: 'click', content: function(){
								return $('#<?=$content_id?>').html();
							}});
						});
					</script>

					<?php
        		}
			}
        ?></td></tr></table><?php
        return $form_id;
    }

}
