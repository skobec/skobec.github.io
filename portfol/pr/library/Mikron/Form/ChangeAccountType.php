<?php

class Mikron_Form_ChangeAccountType {

	function __construct($session_id) {
		$this->max_shki = null;
		if(Constant::VAR_REGION_CODE == 'lipetsk') {
            $maxShki = Service::Billing()->getMaxShki();
            $this->max_shki = $maxShki->max_shki;
        }
		$this->owner_organization = Service::Billing()->getOwnerList();
		$this->billing_bank_list = Mikron_Entity_Model::getList('Type_Billing_Bank');
		$this->billing_bill_ro_list = Service::Billing()->getRoList($session_id);
	}

	public function draw() {		
		?>
	<style>
		#form-changeAccountType .popup-form-content .control-group:nth-child(n+5) {
			display: none;
		}
	</style>
		<script>
			$(document).ready(function(){
				$('#new_account_type_id').change(function () {
					var v = $(this).val();
					$('[class*="field-"]').hide();
					$('.field-' + v).show();
					return CustomUI.centerForms();
				});
			});
			function changeAccountType(account_type_id, account_id, home_id) {
				var form = $('#form-changeAccountType');
				form.find("input[name='form[account_type_id]']").val(account_type_id);
				form.find("input[name='form[account_id]']").val(account_id);
				form.find("input[name='form[home_id]']").val(home_id);
				CustomUI.showForm('form-changeAccountType');
			}
		</script>
		<form method="post" action="/home/changeaccounttype/" class="popup-ajax popup-form form-horizontal" title="Смена типа формирования фонда КР" id="form-changeAccountType">
			<div class="popup-form-content">
				<input type="hidden" name="form[account_type_id]" value="" />
				<input type="hidden" name="form[account_id]" value="" />
				<input type="hidden" name="form[home_id]" value="" />
				<div class="control-group">
					<label class="control-label" for="input_number_2">Способ формирования фонда:</label>
					<div class="controls">
						<select name="form[new_account_type_id]" class="select" id="new_account_type_id">
							<option value="0">Выберите...</option>
							<option value="<?=Constant::ACCOUNT_TYPE_RO_ID?>">На счёте регионального оператора</option>
							<option value="<?=Constant::ACCOUNT_TYPE_SPEC_ID?>">На специальном счёте</option>
						</select>
					</div>
				</div>
				<div class="control-group field-<?=Constant::ACCOUNT_TYPE_SPEC_ID?>">
					<label class="control-label" for="input_number_2">Номер специального счёта:</label>
					<div class="controls">
						<input type="text" name="form[number]" class="form-control span4" />
					</div>
				</div>
				<div class="control-group field-<?=Constant::ACCOUNT_TYPE_RO_ID?>">
					<label class="control-label" for="input_number_2">Действующий счёт:</label>
					<div class="controls">
						<select name="form[billing_bill_ro_id]" class="select">
							<? foreach($this->billing_bill_ro_list->items as $bill) { ?>
								<option value="<?=$bill->id?>"><?=htmlspecialchars($bill->number)?><?=$bill->organization_title ? " ({$bill->organization_title})" : null?></option>
							<? } ?>
						</select>
					</div>
				</div>
				<div class="control-group field-<?=Constant::ACCOUNT_TYPE_SPEC_ID?>">
					<label class="control-label" for="input_number_2">Дата открытия счёта:</label>
					<div class="controls">
						<input type="text" name="form[open_date]" class="datepicker2 span2" />
					</div>
				</div>
				<? if($this->max_shki) { ?>
				<div class="control-group field-<?=Constant::ACCOUNT_TYPE_SPEC_ID?>">
					<label class="control-label" for="input_number_2">Код ШКИ:</label>
					<div class="controls">
						<input type="text" name="form[shki]" class="form-control span4" value="<?= $this->max_shki; ?>" />
					</div>
				</div>
				<? } ?>
				<div class="control-group field-<?=Constant::ACCOUNT_TYPE_SPEC_ID?>">
					<label class="control-label" for="input_number_2">Владелец счёта:</label>
					<div class="controls">
						<select name="form[owner_organization_id]" class="select">
							<? foreach($this->owner_organization as $item) { ?>
								<option value="<?=$item->id?>"><?=htmlspecialchars($item->title)?></option>
							<? } ?>
						</select>
					</div>
				</div>
				<div class="control-group field-<?=Constant::ACCOUNT_TYPE_SPEC_ID?>">
					<label class="control-label" for="input_number_2">Банк:</label>
					<div class="controls">
						<select name="form[billing_bank_id]" class="select">
							<? foreach($this->billing_bank_list->items as $item) { ?>
								<option value="<?=$item['id']?>"><?=htmlspecialchars($item['title'])?><?=$item['inn'] ? " (ИНН: {$item['inn']})" : null?></option>
							<? } ?>
						</select>
					</div>
				</div>
				<div class="control-group field-<?=Constant::ACCOUNT_TYPE_SPEC_ID?>">
					<label class="control-label" for="input_number_2">ФИО лица, уполномоченного на открытие счёта:</label>
					<div class="controls">
						<input type="text" name="form[fio]" class="span4" />
					</div>
				</div>
				<div class="control-group field-<?=Constant::ACCOUNT_TYPE_SPEC_ID?> field-<?=Constant::ACCOUNT_TYPE_RO_ID?>">
					<label class="control-label" for="input_number_2">Протокол собрания жильцов:</label>
					<div class="controls">
						<input type="file" name="form[protocol][]" />
					</div>
				</div>
				<div class="control-group field-<?=Constant::ACCOUNT_TYPE_SPEC_ID?> field-<?=Constant::ACCOUNT_TYPE_RO_ID?>">
					<label class="control-label" for="input_number_2">Договор:</label>
					<div class="controls">
						<input type="file" name="form[dogovor][]" />
					</div>
				</div>
			</div>
			<div class="popup-form-footer">
				<a href="#" class="btn btn-primary button-submit">Сохранить</a>
			</div>
		</form>
	<?}
}

