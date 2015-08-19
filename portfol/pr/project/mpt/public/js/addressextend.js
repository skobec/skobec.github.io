/**
 * Файл предназначен для отображения формы ввода данных
 * с целью заполнения полей объекта Type_Address_Extend
 * и работает в паре с его методами.
 * Форма генерируется классом Type_Address_Extend.
 * Тут идет обработка нажатия кнопки "Сохранить"
 */
$(document).ready(function () {

    $(".typeDeliveryFill").change(function () {
        var id = $(this).attr("id");
        var unicID = id.substr(17, id.length);
        var typeFill = $("#typeDeliveryFill_" + unicID).val();
        switch (typeFill) {
            case "1":
                fill_by(unicID, "fillByAddress");
                $(".inputfields input").prop('disabled', true);
                break;
            case "2":
                fill_by(unicID, "fillByRegistration");
                $(".inputfields input").prop('disabled', true);
                break;
            case "3":
                $(".inputfields input").prop('disabled', false);
                break;
        }
    });

    /**
     * В выбираторе способов заполнения выбран пункт "из адреса дома" или "из адреса прописки"
     * Получим объект адреса соответствующим способом  и заполним форму его значениями
     * 
     * @param {string} unicID ID класса
     * @param {string} action идентификатор того, что надо сделать ('fillByAddress' или 'fillByRegistration') 
     * @returns {Boolean}
     */
    var fill_by = function (unicID, action) {
        var formID = "form-addressextend_" + unicID;
        // Собирать json строку будет класс Type_Address_Extend
        $.ajax({
            type: 'post',
            url: '/autocomplete/getaddressextend',
            dataType: "json",
            data: {
                account_action: action,
                entity_id: $("#id_entity_" + unicID).val(),
                entity_type: $("#type_entity_" + unicID).val(),
            },
            beforeSend: function () {
                $('#rs_info_area_' + unicID).html('<span style="padding-right: 20px ;">Сборка строки</span><img src="/img/autocomplete_indicator.gif" />');
            },
            success: function (respond) {
                // Передаем результаты в html
                var ext = respond.extObject;
                for (var propertyName in ext) {
                    $("#" + propertyName + "_" + unicID).val(ext[propertyName]);
                }
                $('#rs_info_area_' + unicID).empty();
                return false;
            },
            error: function () {
                $('#rs_info_area_' + unicID).empty();
                return false;
            }
        });
        return false;
    };

    /**
     * Нажата кнопка "Сохранить"
     * Из множества полей формы соберем json строку и
     * передадим ее в html.
     */
    $(".btn-addressextend").click(function () {
        var id = $(this).attr("id");
        var unicID = id.substr(4, id.length);
        var formID = "form-addressextend_" + unicID;
        // Получаем все значения с формы
        var arr = {};
        $("#" + formID + " :input").each(function (index) {
            var fieldName = $(this).attr('class');
            var fieldValue = $(this).val();
            arr[fieldName] = fieldValue;
        });
        // Добавим к ним значение dropdown списка
        arr['selected_by'] = $("#typeDeliveryFill_" + unicID).val();
        // Собирать json строку будет класс Type_Address_Extend
        $.ajax({
            type: 'post',
            url: '/autocomplete/getaddressextend',
            dataType: "json",
            data: {
                account_action: 'save',
                formparams: JSON.stringify(arr),
				entity_id: $("#id_entity_" + unicID).val(),
                entity_type: $("#type_entity_" + unicID).val(),
            },
            beforeSend: function () {
                $('#rs_info_area_' + unicID).html('<span style="padding-right: 20px ;">Сборка строки</span><img src="/img/autocomplete_indicator.gif" />');
            },
            success: function (respond) {
                // Передаем результаты в html
                $("#txt_flid_" + unicID).html(respond.valueStr);
                $("#hidden_flid_" + unicID).val(respond.jsonStr);
				//$("#dropdownlist_"+ unicID).val(respond.jsonStr);
				dropdownListLogic("dropdownlist_"+ unicID, respond);
                $('#rs_info_area_' + unicID).empty();
                CustomUI.hideForm(formID);
                return false;
            },
            error: function () {
                $('#rs_info_area_' + unicID).empty();
                CustomUI.hideForm(formID);
                return false;
            }
        });
        return false;
    });

	var dropdownListLogic= function (id, respond){
		if(respond!=undefined){
			$("#"+id+" .temprow").remove();
			var flag=true;
			$("#"+id+" option" ).each(function(){
				if(this.text==respond.valueStr){
					$("#"+id).val($(this).val());
					flag=false;
				}
			});
			if(flag==true){
				$("#"+id).append('<option selected="selected" class="temprow" value='+respond.jsonStr+'>'+respond.valueStr+'</option>');
			}
			$("#"+id).trigger('refresh');
		}
	}
});
