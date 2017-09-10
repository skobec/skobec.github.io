function create_booked_modal(){var e=jQuery(window).height();if(jQuery(window).width()>720)var t=e-295;else var t=e;jQuery("body input, body textarea, body select").blur(),jQuery("body").addClass("booked-noScroll"),jQuery('<div class="booked-modal bm-loading"><div class="bm-overlay"></div><div class="bm-window"><div style="height:100px"></div></div></div>').appendTo("body"),jQuery(".booked-modal .bm-overlay").spin("booked_white"),jQuery(".booked-modal .bm-window").css({"max-height":t+"px"})}function resize_booked_modal(){var e=jQuery(window).height(),t=jQuery(window).width();if(jQuery(".booked-modal .bm-window .booked-scrollable").length){var o=jQuery(".booked-modal .bm-window .booked-scrollable")[0].scrollHeight;o<100?o=previousRealModalHeight:previousRealModalHeight=o}else var o=0;var a,n,i=o+43+43,r=o-43;if(r=e<i?e-43-43:o,t>720){a=r-25,n=a-15;var s=(a+78)/2}else{a=e-43,n=a-60-43;var s=a/2}jQuery(".booked-modal").css({"margin-top":"-"+s+"px"}),jQuery(".booked-modal .bm-window").css({"max-height":a+"px"}),jQuery(".booked-modal .bm-window .booked-scrollable").css({"max-height":n+"px"})}function close_booked_modal(){var e=jQuery(".booked-modal");e.fadeOut(200),e.addClass("bm-closing"),jQuery("body").removeClass("booked-noScroll"),setTimeout(function(){e.remove()},300)}function init_tooltips(e){jQuery(".tooltipster").tooltipster({theme:"tooltipster-light",animation:"grow",speed:200,delay:100,offsetY:-13})}function adjust_calendar_boxes(){jQuery(".booked-calendar").each(function(){var e=jQuery(window).width(),t=jQuery(this).parents(".booked-calendar-wrap").hasClass("small"),o=jQuery(this).find("tbody tr.week td").width(),a=jQuery(this).height();boxesHeight=1*o,jQuery(this).find("tbody tr.week td").height(boxesHeight),jQuery(this).find("tbody tr.week td .date").css("line-height",boxesHeight+"px"),jQuery(this).find("tbody tr.week td .date .number").css("line-height",boxesHeight+"px"),t||e<720?jQuery(this).find("tbody tr.week td .date .number").css("line-height",boxesHeight+"px"):jQuery(this).find("tbody tr.week td .date .number").css("line-height","");var a=jQuery(this).height();jQuery(this).parent().height(a)})}var booked_load_calendar_date_booking_options,booked_appt_form_options,bookedNewAppointment;!function(e,t,o,a){function n(){e("table.booked-calendar").find("tr.week").each(function(){0==e(this).children().length&&e(this).remove()})}function i(t,o){if(t=void 0===t||t,o=void 0!==o&&o)var a=o.find("li.active .savingState, .topSavingState.savingState, .calendarSavingState"),n=o.find(".monthName"),i=o.find("table.booked-calendar tbody");else var a=e("li.active .savingState, .topSavingState.savingState, .calendarSavingState"),n=e(".monthName"),i=e("table.booked-calendar tbody");t?(a.fadeIn(200),n.hide(),i.animate({opacity:.2},100)):(a.hide(),n.show(),i.animate({opacity:1},0))}function r(){e(".booked_list_date_picker").each(function(){var t=e(this),a=t.parents(".booked-appt-list").attr("data-min-date"),n=t.parents(".booked-appt-list").attr("data-max-date");if(void 0===a)var a=t.attr("data-min-date");t.datepicker({dateFormat:"yy-mm-dd",minDate:a,maxDate:n,showAnim:!1,beforeShow:function(t,o){e("#ui-datepicker-div").removeClass(),e("#ui-datepicker-div").addClass("booked_custom_date_picker")},onClose:function(t){e(".booked_list_date_picker_trigger").removeClass("booked-dp-active")},onSelect:function(t){var a=e(this),n=t,i=a.parents(".booked-list-view"),s=i.attr("data-default"),d=a.parents(".booked-list-view-nav").attr("data-calendar-id");void 0===s&&(s=!1),d||(d=0),i.addClass("booked-loading");var l={action:"booked_appointment_list_date",date:n,calendar_id:d,force_default:s};return e(o).trigger("booked-before-loading-appointment-list-booking-options"),i.spin("booked_top"),e.ajax({url:booked_js_vars.ajax_url,type:"post",data:l,success:function(e){i.html(e),r(),setTimeout(function(){i.removeClass("booked-loading")},1)}}),!1}})}),e("body").on("click",".booked_list_date_picker_trigger",function(t){t.preventDefault(),e(this).hasClass("booked-dp-active")||(e(this).addClass("booked-dp-active"),e(this).parents(".booked-appt-list").find(".booked_list_date_picker").datepicker("show"))})}var s=e(t);e.fn.spin.presets.booked={lines:10,length:7,width:5,radius:11,corners:1,rotate:0,direction:1,color:"#555",speed:1,trail:60,shadow:!1,hwaccel:!1,className:"booked-spinner",zIndex:2e9,top:"50%",left:"50%"},e.fn.spin.presets.booked_top={lines:11,length:10,width:6,radius:15,corners:1,rotate:0,scale:.5,direction:1,color:"#aaaaaa",speed:1,trail:60,shadow:!1,hwaccel:!1,className:"booked-spinner booked-spinner-top",zIndex:2e9,top:"15px",left:"50%"},e.fn.spin.presets.booked_white={lines:13,length:11,width:5,radius:18,scale:1,corners:1,rotate:0,direction:1,color:"#fff",speed:1,trail:60,shadow:!1,hwaccel:!1,className:"booked-spinner booked-white",zIndex:2e9,top:"50%",left:"50%"},s.on("resize",function(){adjust_calendar_boxes(),resize_booked_modal()}),s.on("load",function(){d.Init();adjust_calendar_boxes(),e(".booked-calendar-wrap").each(function(){var t=e(this),o=t.find("table.booked-calendar").attr("data-calendar-date");t.attr("data-default",o),init_tooltips(t)}),e(".booked-list-view").each(function(){var t=e(this),o=t.find(".booked-appt-list").attr("data-list-date");t.attr("data-default",o)}),n(),r(),e(".booked_calendar_chooser").change(function(a){a.preventDefault();var s=e(this);if(s.parents(".booked-calendarSwitcher").hasClass("calendar")){var d=s.parents(".booked-calendar-shortcode-wrap").find(".booked-calendar-wrap"),l=d.attr("data-default");void 0===l&&(l=!1);var p={action:"booked_calendar_month",gotoMonth:l,calendar_id:s.val()};i(!0,d),e.ajax({url:booked_js_vars.ajax_url,type:"post",data:p,success:function(o){d.html(o),adjust_calendar_boxes(),n(),init_tooltips(d),e(t).trigger("booked-load-calendar",p,s)}})}else{var d=s.parents(".booked-calendar-shortcode-wrap").find(".booked-list-view"),l=d.attr("data-default");s.parents(".booked-calendarSwitcher").hasClass("calendar"),void 0===l&&(l=!1),d.addClass("booked-loading");var p={action:"booked_appointment_list_date",date:l,calendar_id:s.val()};e(o).trigger("booked-before-loading-appointment-list-booking-options"),d.spin("booked_top"),e.ajax({url:booked_js_vars.ajax_url,type:"post",data:p,success:function(e){d.html(e),r(),setTimeout(function(){d.removeClass("booked-loading")},1)}})}return!1}),e(".booked-calendar-wrap").on("click",".page-right, .page-left, .monthName a",function(o){o.preventDefault();var a=e(this),r=a.attr("data-goto"),s=a.parents(".booked-calendar-wrap"),d=s.attr("data-default"),l=a.parents("table.booked-calendar").attr("data-calendar-id");void 0===d&&(d=!1);var p={action:"booked_calendar_month",gotoMonth:r,calendar_id:l,force_default:d};return i(!0,s),e.ajax({url:booked_js_vars.ajax_url,type:"post",data:p,success:function(o){s.html(o),adjust_calendar_boxes(),n(),init_tooltips(s),e(t).trigger("booked-load-calendar",p,a)}}),!1}),e(".booked-calendar-wrap").on("click","tr.week td",function(t){t.preventDefault();var a=e(this),n=a.parents("table.booked-calendar"),i=a.parent(),r=a.attr("data-date"),s=n.attr("data-calendar-id"),d=i.find("td").length;if(s||(s=0),a.hasClass("blur")||a.hasClass("booked")&&!booked_js_vars.publicAppointments||a.hasClass("prev-date"));else if(a.hasClass("active")){a.removeClass("active"),e("tr.entryBlock").remove();var l=n.height();n.parent().height(l)}else{e("tr.week td").removeClass("active"),a.addClass("active"),e("tr.entryBlock").remove(),i.after('<tr class="entryBlock loading"><td colspan="'+d+'"></td></tr>'),e("tr.entryBlock").find("td").spin("booked"),booked_load_calendar_date_booking_options={action:"booked_calendar_date",date:r,calendar_id:s},e(o).trigger("booked-before-loading-calendar-booking-options");var l=n.height();n.parent().height(l),e.ajax({url:booked_js_vars.ajax_url,type:"post",data:booked_load_calendar_date_booking_options,success:function(t){e("tr.entryBlock").find("td").html(t),e("tr.entryBlock").removeClass("loading"),e("tr.entryBlock").find(".booked-appt-list").fadeIn(300),e("tr.entryBlock").find(".booked-appt-list").addClass("shown"),adjust_calendar_boxes()}})}}),e(".booked-list-view").on("click",".booked-list-view-date-prev,.booked-list-view-date-next",function(t){t.preventDefault();var a=e(this),n=a.attr("data-date"),i=a.parents(".booked-list-view"),s=i.attr("data-default"),d=a.parents(".booked-list-view-nav").attr("data-calendar-id");void 0===s&&(s=!1),d||(d=0),i.addClass("booked-loading");var l={action:"booked_appointment_list_date",date:n,calendar_id:d,force_default:s};return e(o).trigger("booked-before-loading-appointment-list-booking-options"),i.spin("booked_top"),e.ajax({url:booked_js_vars.ajax_url,type:"post",data:l,success:function(e){i.html(e),r(),setTimeout(function(){i.removeClass("booked-loading")},1)}}),!1}),bookedNewAppointment=function(t){t.preventDefault();var a=e(this),n=a.attr("data-title"),i=a.attr("data-timeslot"),r=a.attr("data-date"),s=a.attr("data-calendar-id"),d=(a.parents(".timeslot"),a.parents(".booked-calendar-wrap").hasClass("booked-list-view"));if(void 0!==d&&d)var l=a.parents(".booked-list-view").find(".booked-list-view-nav").attr("data-calendar-id");else var l=a.parents("table.booked-calendar").attr("data-calendar-id");return s=l||s,booked_appt_form_options={action:"booked_new_appointment_form",date:r,timeslot:i,calendar_id:s,title:n},e(o).trigger("booked-before-loading-booking-form"),create_booked_modal(),setTimeout(function(){e.ajax({url:booked_js_vars.ajax_url,type:"post",data:booked_appt_form_options,success:function(t){e(".bm-window").html(t);var a=e(".booked-modal"),n=a.find(".bm-window");n.css({visibility:"hidden"}),a.removeClass("bm-loading"),e(o).trigger("booked-on-new-app"),resize_booked_modal(),n.hide(),e(".booked-modal .bm-overlay").find(".booked-spinner").remove(),setTimeout(function(){n.css({visibility:"visible"}),n.show()},50)}})},100),!1},e(".booked-calendar-wrap").on("click","button.new-appt",bookedNewAppointment);var a=e(".booked-tabs");if(a.find("li.active").length||a.find("li:first-child").addClass("active"),a.length){e(".booked-tab-content").hide();var s=a.find(".active > a").attr("href");s=s.split("#"),s=s[1],e("#profile-"+s).show(),a.find("li > a").on("click",function(t){t.preventDefault(),e(".booked-tab-content").hide(),a.find("li").removeClass("active"),e(this).parent().addClass("active");var o=e(this).attr("href");return o=o.split("#"),o=o[1],e("#profile-"+o).show(),!1})}e(".booked-profile-appt-list").on("click",".booked-show-cf",function(t){t.preventDefault();var o=e(this).parent().find(".cf-meta-values-hidden");return o.is(":visible")?(o.hide(),e(this).removeClass("booked-cf-active")):(o.show(),e(this).addClass("booked-cf-active")),!1}),e("#loginform").length&&e('#loginform input[type="submit"]').on("click",function(t){e('#loginform input[name="log"]').val()&&e('#loginform input[name="pwd"]').val()?e("#loginform .booked-custom-error").hide():(t.preventDefault(),e("#loginform").parents(".booked-form-wrap").find(".booked-custom-error").fadeOut(200).fadeIn(200))}),e("#profile-forgot").length&&e('#profile-forgot input[type="submit"]').on("click",function(t){e('#profile-forgot input[name="user_login"]').val()?e("#profile-forgot .booked-custom-error").hide():(t.preventDefault(),e("#profile-forgot").find(".booked-custom-error").fadeOut(200).fadeIn(200))}),e(".booked-upload-wrap").length&&e(".booked-upload-wrap input[type=file]").on("change",function(){var t=e(this).val();e(this).parent().find("span").html(t),e(this).parent().addClass("hasFile")}),e(".booked-profile-appt-list").on("click",".appt-block .cancel",function(t){t.preventDefault();var o=e(this),a=o.parents(".appt-block"),n=a.attr("data-appt-id");if(confirm_delete=confirm(booked_js_vars.i18n_confirm_appt_delete),1==confirm_delete){var i=parseInt(e(".booked-profile-appt-list").find("h4").find("span.count").html());i=parseInt(i-1),i<1?(e(".booked-profile-appt-list").find("h4").find("span.count").html("0"),e(".no-appts-message").slideDown("fast")):e(".booked-profile-appt-list").find("h4").find("span.count").html(i),e(".appt-block").animate({opacity:.4},0),a.slideUp("fast",function(){e(this).remove()}),e.ajax({url:booked_js_vars.ajax_url,method:"post",data:{action:"booked_cancel_appt",appt_id:n},success:function(t){e(".appt-block").animate({opacity:1},150)}})}return!1}),e("body").on("touchstart click",".bm-overlay, .bm-window .close, .booked-form .cancel",function(e){return e.preventDefault(),close_booked_modal(),!1}),e("body").on("focusin",".booked-form input",function(){this.title==this.value&&(e(this).addClass("hasContent"),this.value="")}).on("focusout",".booked-form input",function(){""===this.value&&(e(this).removeClass("hasContent"),this.value=this.title)}),e("body").on("change",".booked-form input",function(){var t=e(this).attr("data-condition"),o=e(this).val();t&&e(".condition-block").length&&(e(".condition-block."+t).hide(),e("#condition-"+o).fadeIn(200),resize_booked_modal())}),e("body").on("submit","form#ajaxlogin",function(o){o.preventDefault(),e("form#ajaxlogin p.status").show().html('<i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;&nbsp;'+booked_js_vars.i18n_please_wait),resize_booked_modal();var a=e(this),n=a.data("date"),i=a.data("title"),r=a.data("timeslot"),s=a.data("calendar-id");e.ajax({type:"post",url:booked_js_vars.ajax_url,data:e("form#ajaxlogin").serialize(),success:function(o){if("success"==o){close_booked_modal();var a=e('<button data-title="'+i+'" data-timeslot="'+r+'" data-date="'+n+'" data-calendar-id="'+s+'"></button>');a.on("click",t.bookedNewAppointment),a.triggerHandler("click"),a.unbind("click",t.bookedNewAppointment),a.detach()}else e("form#ajaxlogin p.status").show().html('<i class="fa fa-warning" style="color:#E35656"></i>&nbsp;&nbsp;&nbsp;'+booked_js_vars.i18n_wrong_username_pass),resize_booked_modal()}}),o.preventDefault()}),e("body").on("click",".booked-forgot-password",function(t){t.preventDefault(),e("#ajaxlogin").hide(),e("#ajaxforgot").show(),resize_booked_modal()}),e("body").on("click",".booked-forgot-goback",function(t){t.preventDefault(),e("#ajaxlogin").show(),e("#ajaxforgot").hide(),resize_booked_modal()}),e("body").on("submit","form#ajaxforgot",function(t){t.preventDefault(),e("form#ajaxforgot p.status").show().html('<i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;&nbsp;'+booked_js_vars.i18n_please_wait),resize_booked_modal();e(this);e.ajax({type:"post",url:booked_js_vars.ajax_url,data:e("form#ajaxforgot").serialize(),success:function(o){"success"==o?(t.preventDefault(),e("#ajaxlogin").show(),e("#ajaxforgot").hide(),e("form#ajaxlogin p.status").show().html('<i class="fa fa-check" style="color:#56c477"></i>&nbsp;&nbsp;&nbsp;'+booked_js_vars.i18n_password_reset),resize_booked_modal()):(e("form#ajaxforgot p.status").show().html('<i class="fa fa-warning" style="color:#E35656"></i>&nbsp;&nbsp;&nbsp;'+booked_js_vars.i18n_password_reset_error),resize_booked_modal())}}),t.preventDefault()}),e("body").on("click",".booked-form input#submit-request-appointment",function(t){e("form#newAppointmentForm p.status").show().html('<i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;&nbsp;'+booked_js_vars.i18n_please_wait),resize_booked_modal(),t.preventDefault();var o=e("#newAppointmentForm input[name=customer_type]").val(),a=e("#newAppointmentForm input[name=user_id]").val(),n=e("#newAppointmentForm input[name=booked_appt_name]").val(),i=e("#newAppointmentForm input[name=booked_appt_surname]").val(),r=e("#newAppointmentForm input[name=booked_appt_surname]").length,s=e("#newAppointmentForm input[name=guest_name]").val(),d=e("#newAppointmentForm input[name=guest_surname]").val(),p=e("#newAppointmentForm input[name=guest_surname]").length,c=e("#newAppointmentForm input[name=guest_email]").val(),u=e("#newAppointmentForm input[name=guest_email]").length,b=e("#newAppointmentForm input[name=booked_appt_email]").val(),f=e("#newAppointmentForm input[name=booked_appt_password]").val(),_=!1;return e(this).parents(".booked-form").find("input,textarea,select").each(function(t,o){var a=e(this).attr("required");if(a&&"hidden"==e(o).attr("type")){var n=e(o).attr("name");if(n=n.split("---"),fieldName=n[0],fieldNumber=n[1].split("___"),fieldNumber=fieldNumber[0],"radio-buttons-label"==fieldName){var i=!1;e('input:radio[name="single-radio-button---'+fieldNumber+'[]"]:checked').each(function(){e(this).val()&&(i=e(this).val())}),i||(_=!0)}else if("checkboxes-label"==fieldName){var r=!1;e('input:checkbox[name="single-checkbox---'+fieldNumber+'[]"]:checked').each(function(){e(this).val()&&(r=e(this).val())}),r||(_=!0)}}else a&&"hidden"!=e(o).attr("type")&&""==e(o).val()&&(_=!0)}),_?(e("form#newAppointmentForm p.status").show().html('<i class="fa fa-warning" style="color:#E35656"></i>&nbsp;&nbsp;&nbsp;'+booked_js_vars.i18n_fill_out_required_fields),resize_booked_modal(),!1):"new"==o&&!n||"new"==o&&r&&!i||"new"==o&&!b||"new"==o&&!f?(e("form#newAppointmentForm p.status").show().html('<i class="fa fa-warning" style="color:#E35656"></i>&nbsp;&nbsp;&nbsp;'+booked_js_vars.i18n_appt_required_fields),resize_booked_modal(),!1):"guest"==o&&!s||"guest"==o&&u&&!c||"guest"==o&&p&&!d?(e("form#newAppointmentForm p.status").show().html('<i class="fa fa-warning" style="color:#E35656"></i>&nbsp;&nbsp;&nbsp;'+booked_js_vars.i18n_appt_required_fields_guest),resize_booked_modal(),!1):(("current"==o&&a||"guest"==o&&s&&!p&&!u||"guest"==o&&s&&p&&d&&!u||"guest"==o&&s&&u&&c&&!p||"guest"==o&&s&&u&&c&&p&&d)&&l.currentUserOrGuest(),void("new"==o&&n&&b&&f&&(!r||r&&i)&&l.newUser()))});var l={formSelector:"#newAppointmentForm",formBtnRequestSelector:".booked-form input#submit-request-appointment",formStatusSelector:"p.status",formSubmitBtnSelector:"#submit-request-appointment",apptContainerSelector:".booked-appointment-details",baseFields:["guest_name","guest_surname","guest_email","action","customer_type","user_id"],apptFields:["appoinment","calendar_id","title","date","timestamp","timeslot"],userFields:["booked_appt_name","booked_appt_surname","booked_appt_email","booked_appt_password"],captchaFields:["captcha_word","captcha_code"],currentApptIndex:!1,currentApptCounter:!1,hasAnyErrors:!1,currentUserOrGuest:function(){var e=l._totalAppts();if(e){l._showLoadingMessage(),l._resetDefaultValues();var t=l._getBaseData();l.currentApptIndex=0,l.currentApptCounter=1,l._doRequestAppointment(t,e)}},newUser:function(){var t=l._totalAppts();if(t){l._showLoadingMessage(),l._resetDefaultValues();var o=l._getBaseData();if(t>1){var a=null;a=e.extend(!0,{},o),a=l._addUserRegistrationData(a),l._requestUserRegistration(a),o.customer_type="current"}else o=l._addUserRegistrationData(o);l.currentApptIndex=0,l._doRequestAppointment(o,t)}},_doRequestAppointment:function(t,o){var a=l.apptFields;0===l.currentApptIndex&&(l._hideCancelBtn(),l._disableSubmitBtn(),l.hasAnyErrors=!1);for(var n=e.extend(!0,{},t),i=0;i<a.length;i++)n[a[i]]=l._getFieldVal(a[i],l.currentApptIndex);var r=l._getFieldVal("calendar_id",l.currentApptIndex);n=l._addCustomFieldsData(n,r),l._getApptElement(l.currentApptIndex).hasClass("skip")?(l.currentApptIndex++,l.currentApptCounter++,l._doRequestAppointment(t,o,l.currentApptIndex)):e.ajax({type:"post",url:booked_js_vars.ajax_url,data:n,success:function(e){l._requestAppointmentResponseHandler(e),l.currentApptIndex++,setTimeout(function(){l.currentApptCounter===o?l.hasAnyErrors?(l._enableSubmitBtn(),l._showCancelBtn()):l._onAfterRequestAppointment():(l.currentApptCounter++,l._doRequestAppointment(t,o))},100)}})},_totalAppts:function(){return e(l.formSelector+' input[name="appoinment[]"]').length},_getBaseData:function(){for(var e={},t=l.baseFields,o=0;o<t.length;o++)e[t[o]]=l._getFieldVal(t[o]);return e.is_fe_form=!0,e.total_appts=l._totalAppts(),e},_getFieldVal:function(t,o){var t=void 0===t?"":t,o=void 0!==o&&o,a=l.formSelector+" ";return!1===o?(a+=" [name="+t+"]",e(a).val()):(a+=' [name="'+t+'[]"]',e(a).eq(o).val())},_resetDefaultValues:function(){e(".booked-form input").each(function(){var t=e(this).val();e(this).attr("title")==t&&e(this).val("")})},_resetToDefaultValues:function(){e(".booked-form input").each(function(){var t=e(this).val(),o=e(this).attr("title");t||e(this).val(o)})},_addUserRegistrationData:function(t){return e.each(l.userFields,function(e,o){t[o]=l._getFieldVal(o)}),e.each(l.captchaFields,function(e,o){var a=l._getFieldVal(o);a&&(t[o]=a)}),t},_addCustomFieldsData:function(t,o){e(".cf-block [name]").filter(function(t){var a=e(this);return parseInt(a.data("calendar-id"))===parseInt(o)&&a.attr("name").match(/---\d+/g)}).each(function(o){var a=e(this),n=a.attr("name"),i=a.val();a.attr("type");if(i){if(!n.match(/checkbox|radio+/g))return void(t[n]=i);if(n.match(/radio+/g)&&a.is(":checked"))return void(t[n]=i);(!n.match(/radio+/g)&&void 0===t[n]||!n.match(/radio+/g)&&t[n].constructor!==Array)&&(t[n]=[]),a.is(":checked")&&t[n].push(i)}});return t},_requestUserRegistration:function(t,o){e.ajax({type:"post",url:booked_js_vars.ajax_url,data:t,async:!1,success:function(e){l._requestUserRegistrationResponseHandler(e)}})},_requestUserRegistrationResponseHandler:function(e){var t=e.split("###");t[0].substr(t[0].length-5)},_requestAppointment:function(e){l._requestAppointmentResponseHandler(e)},_requestAppointmentResponseHandler:function(e){var t=e.split("###");if("error"===t[0].substr(t[0].length-5))return void l._requestAppointmentOnError(t);l._requestAppointmentOnSuccess(t)},_requestAppointmentOnError:function(t){var a=l._getApptElement();e(o).trigger("booked-on-requested-appt-error",[a]),l._highlightAppt(),l._setStatusMsg(t[1]),l.hasAnyErrors=!0,resize_booked_modal()},_requestAppointmentOnSuccess:function(t){var a=l._getApptElement();e(o).trigger("booked-on-requested-appt-success",[a]),l._unhighlightAppt()},_onAfterRequestAppointment:function(){var a={redirect:!1};e(o).trigger("booked-on-requested-appointment",[a]);if(!a.redirect){if(booked_js_vars.profilePage)return void(t.location=booked_js_vars.profilePage);l._reloadApptsList(),l._reloadCalendarTable()}},_setStatusMsg:function(t){var o=l.formSelector+" "+l.formStatusSelector;e(o).show().html('<i class="fa fa-warning" style="color:#E35656"></i>&nbsp;&nbsp;&nbsp;'+t)},_getApptElement:function(t){var t=void 0===t?l.currentApptIndex:t,o=l.formSelector+" "+l.apptContainerSelector;return e(o).eq(t)},_highlightAppt:function(e){var t=l._getApptElement();t.length&&t.addClass("has-error")},_unhighlightAppt:function(e){var t=l._getApptElement();t.length&&t.removeClass("has-error").addClass("skip")},_enableSubmitBtn:function(){var t=l.formSelector+" "+l.formSubmitBtnSelector;e(t).attr("disabled",!1)},_disableSubmitBtn:function(){var t=l.formSelector+" "+l.formSubmitBtnSelector;e(t).attr("disabled",!0)},_showCancelBtn:function(){e(l.formSelector).find("button.cancel").show()},_hideCancelBtn:function(){e(l.formSelector).find("button.cancel").hide()},_showLoadingMessage:function(){e("form#newAppointmentForm p.status").show().html('<i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;&nbsp;'+booked_js_vars.i18n_please_wait)},_reloadApptsList:function(){e(".booked-appt-list").length&&e(".booked-appt-list").each(function(){var t=e(this),a=t.attr("data-list-date"),n=t.parents(".booked-list-view"),i=n.attr("data-default"),s=parseInt(t.find(".booked-list-view-nav").attr("data-calendar-id"))||0;i=void 0!==i&&i,s=s||0,n.addClass("booked-loading");var d={action:"booked_appointment_list_date",date:a,calendar_id:s,force_default:i};e(o).trigger("booked-before-loading-appointment-list-booking-options"),n.spin("booked_top"),e.ajax({url:booked_js_vars.ajax_url,type:"post",data:d,success:function(e){n.html(e),close_booked_modal(),r(),setTimeout(function(){n.removeClass("booked-loading")},1)}})})},_reloadCalendarTable:function(){if(e("td.active").length){var t=e("td.active"),a=t.attr("data-date"),n=parseInt(t.parents("table").data("calendar-id"))||0;booked_load_calendar_date_booking_options={action:"booked_calendar_date",date:a,calendar_id:n},e(o).trigger("booked-before-loading-calendar-booking-options"),e.ajax({url:booked_js_vars.ajax_url,type:"post",data:booked_load_calendar_date_booking_options,success:function(t){e("tr.entryBlock").find("td").html(t),close_booked_modal(),e("tr.entryBlock").removeClass("loading"),e("tr.entryBlock").find(".booked-appt-list").hide().fadeIn(300),e("tr.entryBlock").find(".booked-appt-list").addClass("shown"),adjust_calendar_boxes()}})}}}}),e(o).ajaxStop(function(){i(!1)});var d={bookingModalSelector:".booked-modal",tabSelector:".booked-tabs",tabNavSelector:".booked-tabs-nav span",tabCntSelector:".booked-tabs-cnt",Init:function(){e(o).on("click",this.tabNavSelector,this.tabsNav)},tabsNav:function(t){t.preventDefault(),d.switchToTab(e(this)),d.maybeResizeBookingModal()},switchToTab:function(e){var t=e,o="."+t.data("tab-cnt"),a=t.parents(d.tabSelector);t.addClass("active").siblings().removeClass("active"),a.find(d.tabCntSelector+" "+o).addClass("active").siblings().removeClass("active")},maybeResizeBookingModal:function(){e(d.bookingModalSelector).length&&resize_booked_modal()}}}(jQuery,window,document);var previousRealModalHeight=100;			return;
				}

				SubmitRequestAppointment._showLoadingMessage();
				SubmitRequestAppointment._resetDefaultValues();

				var data = SubmitRequestAppointment._getBaseData();

				// when there are more than one appointment, we need to make the registration request first and then loop the appointments
				if ( total_appts > 1 ) {
					var data_obj_with_no_reference = null;
					data_obj_with_no_reference = $.extend( true, {}, data );
					data_obj_with_no_reference = SubmitRequestAppointment._addUserRegistrationData( data_obj_with_no_reference );
					SubmitRequestAppointment._requestUserRegistration( data_obj_with_no_reference );

					data.customer_type = 'current';
				} else {
					// add user registration fields values
					data = SubmitRequestAppointment._addUserRegistrationData( data );
				}

				SubmitRequestAppointment.currentApptIndex = 0;
				SubmitRequestAppointment._doRequestAppointment( data, total_appts );
			},

			_doRequestAppointment: function( data, total_appts ) {
				
				var appt_fields = SubmitRequestAppointment.apptFields;

				// for the first item only
				if ( SubmitRequestAppointment.currentApptIndex === 0 ) {
					SubmitRequestAppointment._hideCancelBtn();
					SubmitRequestAppointment._disableSubmitBtn();
					SubmitRequestAppointment.hasAnyErrors = false;
				}
				// <------end

				var data_obj_with_no_reference = $.extend( true, {}, data );

				// add the appointment fields to the data
				for (var i = 0; i < appt_fields.length; i++) {
					data_obj_with_no_reference[ appt_fields[i] ] = SubmitRequestAppointment._getFieldVal( appt_fields[i], SubmitRequestAppointment.currentApptIndex );
				}

				var calendar_id = SubmitRequestAppointment._getFieldVal( 'calendar_id', SubmitRequestAppointment.currentApptIndex );
				data_obj_with_no_reference = SubmitRequestAppointment._addCustomFieldsData( data_obj_with_no_reference, calendar_id );

				var $appt = SubmitRequestAppointment._getApptElement( SubmitRequestAppointment.currentApptIndex );

				if ( ! $appt.hasClass('skip') ) {
					$.ajax({
						type    : 'post',
						url     : booked_js_vars.ajax_url,
						data    : data_obj_with_no_reference,
						success	: function( response ) {

							//SubmitRequestAppointment._enableSubmitBtn();
							//SubmitRequestAppointment._showCancelBtn();

							//console.log(response);
							//return;

							SubmitRequestAppointment._requestAppointmentResponseHandler( response );
							SubmitRequestAppointment.currentApptIndex++;

							setTimeout( function() {
								if ( SubmitRequestAppointment.currentApptCounter === total_appts ) {
									// for the last item only
									if ( ! SubmitRequestAppointment.hasAnyErrors ) {
										SubmitRequestAppointment._onAfterRequestAppointment();
									} else {
										SubmitRequestAppointment._enableSubmitBtn();
										SubmitRequestAppointment._showCancelBtn();
									}
									// <------end
								} else {
									SubmitRequestAppointment.currentApptCounter++;
									SubmitRequestAppointment._doRequestAppointment( data, total_appts );
								}
							}, 100 );
						}
					});
				} else {
					SubmitRequestAppointment.currentApptIndex++;
					SubmitRequestAppointment.currentApptCounter++;
					SubmitRequestAppointment._doRequestAppointment( data, total_appts, SubmitRequestAppointment.currentApptIndex );
				}
			},

			_totalAppts: function() {
				return $(SubmitRequestAppointment.formSelector + ' input[name="appoinment[]"]').length;
			},

			_getBaseData: function() {
				var data = {},
					fields = SubmitRequestAppointment.baseFields;

				// set up the base form data
				for ( var i = 0; i < fields.length; i++ ) {
					data[ fields[i] ] = SubmitRequestAppointment._getFieldVal( fields[i] );
				}

				data['is_fe_form'] = true;
				data['total_appts'] = SubmitRequestAppointment._totalAppts();

				return data;
			},

			_getFieldVal: function( field_name, field_index ) {
				var field_name = typeof field_name === 'undefined' ? '' : field_name,
					field_index = typeof field_index === 'undefined' ? false : field_index,
					selector = SubmitRequestAppointment.formSelector + ' ';
					
				if ( field_index === false ) {
					selector += ' [name=' + field_name + ']';
					return $( selector ).val();
				}

				selector += ' [name="' + field_name + '[]"]';
				return $( selector ).eq( field_index ).val();
			},

			_resetDefaultValues: function() {
				 $('.booked-form input').each(function(){
					var thisVal = $(this).val(),
						thisDefault = $(this).attr('title');

					if ( thisDefault == thisVal ){ 
						$(this).val(''); 
					}
				});
			},

			_resetToDefaultValues: function() {
				$('.booked-form input').each(function(){
					var thisVal = $(this).val(),
						thisDefault = $(this).attr('title');

					if ( ! thisVal ){ 
						$(this).val( thisDefault ); 
					}
				});
			},

			_addUserRegistrationData: function( data ) {
				// populate the user data
				$.each( SubmitRequestAppointment.userFields, function( index, field_name ) {
					data[ field_name ] = SubmitRequestAppointment._getFieldVal( field_name );
				} );

				// populate captcha data if available
				$.each( SubmitRequestAppointment.captchaFields, function( index, field_name ) {
					var field_value = SubmitRequestAppointment._getFieldVal( field_name );

					if ( ! field_value ) {
						return;
					}

					data[ field_name ] = field_value;
				} );

				return data;
			},

			_addCustomFieldsData: function( data, calendar_id ) {
				var custom_fields_data = $('.cf-block [name]')
					.filter( function( index ) {
						var $this = $(this);
						return parseInt($this.data('calendar-id')) === parseInt(calendar_id) && $this.attr('name').match(/---\d+/g);
					} )
					.each( function( index ) {
						var $this = $(this),
							name = $this.attr('name'),
							value = $this.val(),
							type = $this.attr('type');

						if ( ! value ) {
							return;
						}

						if ( ! name.match(/checkbox|radio+/g) ) {
							data[ name ] = value;
							return;
						}

						if ( name.match(/radio+/g) && $this.is(':checked') ) {
							data[ name ] = value;
							return;
						}

						if ( ! name.match(/radio+/g) && typeof data[ name ] === 'undefined' || ! name.match(/radio+/g) && data[ name ].constructor !== Array ) {
							data[ name ] = [];
						}

						if ( ! $this.is(':checked') ) {
							return;
						}

						data[ name ].push( value );
					} );

				return data;
			},

			_requestUserRegistration: function( base_data, appt_index ) {
				$.ajax({
					type    : 'post',
					url     : booked_js_vars.ajax_url,
					data    : base_data,
					async   : false,
					success	: function( response ) {
						SubmitRequestAppointment._requestUserRegistrationResponseHandler( response );
					}
				});
			},

			_requestUserRegistrationResponseHandler: function( response ) {
				var response_parts = response.split('###'),
					data_result = response_parts[0].substr( response_parts[0].length - 5 );

				if ( data_result === 'error' ) {
					// do something on registration failure
					return;
				}

				// do something on successful registration
			},

			_requestAppointment: function( response ) {
				SubmitRequestAppointment._requestAppointmentResponseHandler( response );
			},

			_requestAppointmentResponseHandler: function( response ) {
				var response_parts = response.split('###'),
					data_result = response_parts[0].substr( response_parts[0].length - 5 );

				if ( data_result === 'error' ) {
					SubmitRequestAppointment._requestAppointmentOnError( response_parts );
					return;
				}

				SubmitRequestAppointment._requestAppointmentOnSuccess( response_parts );
			},

			_requestAppointmentOnError: function( response_parts ) {
				var $apptEl = SubmitRequestAppointment._getApptElement();

				$(document).trigger("booked-on-requested-appt-error",[$apptEl]);

				SubmitRequestAppointment._highlightAppt();

				SubmitRequestAppointment._setStatusMsg( response_parts[1] );

				SubmitRequestAppointment.hasAnyErrors = true;

				resize_booked_modal();
			},

			_requestAppointmentOnSuccess: function( response_parts ) {
				var $apptEl = SubmitRequestAppointment._getApptElement();

				$(document).trigger("booked-on-requested-appt-success",[$apptEl]);

				SubmitRequestAppointment._unhighlightAppt();
			},

			_onAfterRequestAppointment: function() {
				var redirectObj = { redirect : false };
				var redirect = $(document).trigger("booked-on-requested-appointment",[redirectObj]);

				if ( redirectObj.redirect ) {
					return;
				}

				if ( booked_js_vars.profilePage ) {
					window.location = booked_js_vars.profilePage;
					return;
				}

				SubmitRequestAppointment._reloadApptsList();
				SubmitRequestAppointment._reloadCalendarTable();
			},

			_setStatusMsg: function( msg ) {
				var form_status_selector = SubmitRequestAppointment.formSelector + ' ' + SubmitRequestAppointment.formStatusSelector;
				$( form_status_selector ).show().html( '<i class="fa fa-warning" style="color:#E35656"></i>&nbsp;&nbsp;&nbsp;' + msg );
			},

			_getApptElement: function( appt_index ) {
				var appt_index = typeof appt_index === 'undefined' ? SubmitRequestAppointment.currentApptIndex : appt_index,
					appt_cnt_selector = SubmitRequestAppointment.formSelector + ' ' + SubmitRequestAppointment.apptContainerSelector;

				return $( appt_cnt_selector ).eq( appt_index );
			},

			_highlightAppt: function( msg ) {
				var $apptEl = SubmitRequestAppointment._getApptElement();

				if ( ! $apptEl.length ) {
					return;
				}

				$apptEl.addClass('has-error');
			},

			_unhighlightAppt: function( msg ) {
				var $apptEl = SubmitRequestAppointment._getApptElement();

				if ( ! $apptEl.length ) {
					return;
				}

				$apptEl.removeClass('has-error').addClass('skip');
			},

			_enableSubmitBtn: function() {
				var btn_selector = SubmitRequestAppointment.formSelector + ' ' + SubmitRequestAppointment.formSubmitBtnSelector;
				$( btn_selector ).attr( 'disabled', false );
			},

			_disableSubmitBtn: function() {
				var btn_selector = SubmitRequestAppointment.formSelector + ' ' + SubmitRequestAppointment.formSubmitBtnSelector;
				$( btn_selector ).attr( 'disabled', true );
			},

			_showCancelBtn: function() {
				$( SubmitRequestAppointment.formSelector ).find('button.cancel').show();
			},

			_hideCancelBtn: function() {
				$( SubmitRequestAppointment.formSelector ).find('button.cancel').hide();
			},

			_showLoadingMessage: function() {
				$('form#newAppointmentForm p.status').show().html('<i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;&nbsp;' + booked_js_vars.i18n_please_wait);
			},

			_reloadApptsList: function() {
				if ( ! $('.booked-appt-list').length ){
					return;
				}

				$('.booked-appt-list').each( function() {
					var $thisApptList  = $(this),
						date          = $thisApptList.attr('data-list-date'),
						thisList      = $thisApptList.parents('.booked-list-view'),
						defaultDate   = thisList.attr('data-default'),
						calendar_id   = parseInt($thisApptList.find('.booked-list-view-nav').attr('data-calendar-id')) || 0;
					
					defaultDate = typeof defaultDate === 'undefined' ? false : defaultDate;
					calendar_id = calendar_id ? calendar_id : 0;

					thisList.addClass('booked-loading');

					var booked_load_list_view_date_booking_options = {
						'action'		: 'booked_appointment_list_date',
						'date'			: date,
						'calendar_id'	: calendar_id,
						'force_default'	: defaultDate
					};
					
					$(document).trigger("booked-before-loading-appointment-list-booking-options");
					thisList.spin('booked_top');
				
					$.ajax({
						url: booked_js_vars.ajax_url,
						type: 'post',
						data: booked_load_list_view_date_booking_options,
						success: function( html ) {
							thisList.html( html );
							
							close_booked_modal();
							init_appt_list_date_picker();
							setTimeout(function(){
								thisList.removeClass('booked-loading');
							},1);
						}
					});
				});
			},

			_reloadCalendarTable: function() {
				if ( ! $('td.active').length ) {
					return;
				}

				var $activeTD = $('td.active'),
					activeDate = $activeTD.attr('data-date'),
					calendar_id = parseInt( $activeTD.parents('table').data('calendar-id') ) || 0;

				booked_load_calendar_date_booking_options = { 'action':'booked_calendar_date', 'date':activeDate, 'calendar_id':calendar_id };
				$(document).trigger("booked-before-loading-calendar-booking-options");
				
				$.ajax({
					url: booked_js_vars.ajax_url,
					type: 'post',
					data: booked_load_calendar_date_booking_options,
					success: function( html ) {
						
						$('tr.entryBlock').find('td').html( html );
						
						close_booked_modal();
						$('tr.entryBlock').removeClass('loading');
						$('tr.entryBlock').find('.booked-appt-list').hide().fadeIn(300);
						$('tr.entryBlock').find('.booked-appt-list').addClass('shown');
						adjust_calendar_boxes();
					}
				});
			}
		}
	});
	
	function bookedRemoveEmptyTRs(){
		$('table.booked-calendar').find('tr.week').each(function(){
			if ($(this).children().length == 0){
				$(this).remove();
			}
		});
	}

	// Saving state updater
	function savingState(show,limit_to){

		show = typeof show !== 'undefined' ? show : true;
		limit_to = typeof limit_to !== 'undefined' ? limit_to : false;

		if (limit_to){

			var $savingStateDIV = limit_to.find('li.active .savingState, .topSavingState.savingState, .calendarSavingState');
			var $stuffToHide = limit_to.find('.monthName');
			var $stuffToTransparent = limit_to.find('table.booked-calendar tbody');

		} else {

			var $savingStateDIV = $('li.active .savingState, .topSavingState.savingState, .calendarSavingState');
			var $stuffToHide = $('.monthName');
			var $stuffToTransparent = $('table.booked-calendar tbody');

		}

		if (show){
			$savingStateDIV.fadeIn(200);
			$stuffToHide.hide();
			$stuffToTransparent.animate({'opacity':0.2},100);
		} else {
			$savingStateDIV.hide();
			$stuffToHide.show();
			$stuffToTransparent.animate({'opacity':1},0);
		}

	}

	$(document).ajaxStop(function() {
		savingState(false);
	});
	
	function init_appt_list_date_picker(){
		
		$('.booked_list_date_picker').each(function(){
			var thisDatePicker = $(this);
			var minDateVal = thisDatePicker.parents('.booked-appt-list').attr('data-min-date');
			var maxDateVal = thisDatePicker.parents('.booked-appt-list').attr('data-max-date');
			if (typeof minDateVal == 'undefined'){ var minDateVal = thisDatePicker.attr('data-min-date'); }
		
			thisDatePicker.datepicker({
		        dateFormat: 'yy-mm-dd',
		        minDate: minDateVal,
		        maxDate: maxDateVal,
		        showAnim: false,
		        beforeShow: function(input, inst) {
					$('#ui-datepicker-div').removeClass();
					$('#ui-datepicker-div').addClass('booked_custom_date_picker');
			    },
			    onClose: function(dateText){
					$('.booked_list_date_picker_trigger').removeClass('booked-dp-active'); 
			    },
			    onSelect: function(dateText){
				   
				   	var thisInput 			= $(this),
						date				= dateText,
						thisList			= thisInput.parents('.booked-list-view'),
						defaultDate			= thisList.attr('data-default'),
						calendar_id			= thisInput.parents('.booked-list-view-nav').attr('data-calendar-id');
						
					if (typeof defaultDate == 'undefined'){ defaultDate = false; }
						
					if (!calendar_id){ calendar_id = 0; }
					thisList.addClass('booked-loading');
					
					var booked_load_list_view_date_booking_options = {
						'action'		: 'booked_appointment_list_date',
						'date'			: date,
						'calendar_id'	: calendar_id,
						'force_default'	: defaultDate
					};
					
					$(document).trigger("booked-before-loading-appointment-list-booking-options");
					thisList.spin('booked_top');
				
					$.ajax({
						url: booked_js_vars.ajax_url,
						type: 'post',
						data: booked_load_list_view_date_booking_options,
						success: function( html ) {
							
							thisList.html( html );
							
							init_appt_list_date_picker();
							setTimeout(function(){
								thisList.removeClass('booked-loading');
							},1);
							
						}
					});
					
					return false;
			    }
		    });
		    
		});
		
		$('body').on('click','.booked_list_date_picker_trigger',function(e){
			e.preventDefault();
			if (!$(this).hasClass('booked-dp-active')){
				$(this).addClass('booked-dp-active');
				$(this).parents('.booked-appt-list').find('.booked_list_date_picker').datepicker('show');
			}
			
	    }); 
	    
	}

	var BookedTabs = {
		bookingModalSelector: '.booked-modal',
		tabSelector: '.booked-tabs',
		tabNavSelector: '.booked-tabs-nav span',
		tabCntSelector: '.booked-tabs-cnt',

		Init: function() {
			$(document).on( 'click', this.tabNavSelector, this.tabsNav );
		},

		tabsNav: function( event ) {
			event.preventDefault();

			BookedTabs.switchToTab( $(this) );
			BookedTabs.maybeResizeBookingModal();
		},

		switchToTab: function( tab_nav_item ) {
			var $nav_item = tab_nav_item,
				tab_cnt_class = '.' + $nav_item.data('tab-cnt'),
				$tabs_container = $nav_item.parents( BookedTabs.tabSelector );

			$nav_item
				.addClass( 'active' )
				.siblings()
				.removeClass( 'active' )

			$tabs_container
				.find( BookedTabs.tabCntSelector + ' ' + tab_cnt_class )
				.addClass( 'active' )
				.siblings()
				.removeClass( 'active' );
		},

		maybeResizeBookingModal: function() {
			if ( ! $(BookedTabs.bookingModalSelector).length ) {
				return;
			}
			
			resize_booked_modal();
		}
	}

})(jQuery, window, document);

// Create Booked Modal
function create_booked_modal(){
	var windowHeight = jQuery(window).height();
	var windowWidth = jQuery(window).width();
	if (windowWidth > 720){
		var maxModalHeight = windowHeight - 295;
	} else {
		var maxModalHeight = windowHeight;
	}
	
	jQuery('body input, body textarea, body select').blur();
	jQuery('body').addClass('booked-noScroll');
	jQuery('<div class="booked-modal bm-loading"><div class="bm-overlay"></div><div class="bm-window"><div style="height:100px"></div></div></div>').appendTo('body');
	jQuery('.booked-modal .bm-overlay').spin('booked_white');
	jQuery('.booked-modal .bm-window').css({'max-height':maxModalHeight+'px'});
}

var previousRealModalHeight = 100;

function resize_booked_modal(){
	
	var windowHeight = jQuery(window).height();
	var windowWidth = jQuery(window).width();
	
	var common43 = 43;
	
	if (jQuery('.booked-modal .bm-window .booked-scrollable').length){
		var realModalHeight = jQuery('.booked-modal .bm-window .booked-scrollable')[0].scrollHeight;
		
		if (realModalHeight < 100){
			realModalHeight = previousRealModalHeight;
		} else {
			previousRealModalHeight = realModalHeight;
		}
		
	} else {
		var realModalHeight = 0;
	}
	var minimumWindowHeight = realModalHeight + common43 + common43;
	var modalScrollableHeight = realModalHeight - common43;
	var maxModalHeight;
	var maxFormHeight;
	
	if (windowHeight < minimumWindowHeight){
		modalScrollableHeight = windowHeight - common43 - common43;
	} else {
		modalScrollableHeight = realModalHeight;
	}
	
	if (windowWidth > 720){
		maxModalHeight = modalScrollableHeight - 25;
		maxFormHeight = maxModalHeight - 15;
		var modalNegMargin = (maxModalHeight + 78) / 2;
	} else {
		maxModalHeight = windowHeight - common43;
		maxFormHeight = maxModalHeight - 60 - common43;
		var modalNegMargin = (maxModalHeight) / 2;
	}
	
	jQuery('.booked-modal').css({'margin-top':'-'+modalNegMargin+'px'});
	jQuery('.booked-modal .bm-window').css({'max-height':maxModalHeight+'px'});
	jQuery('.booked-modal .bm-window .booked-scrollable').css({'max-height':maxFormHeight+'px'});
	
}

function close_booked_modal(){
	var modal = jQuery('.booked-modal');
	modal.fadeOut(200);
	modal.addClass('bm-closing');
	jQuery('body').removeClass('booked-noScroll');
	setTimeout(function(){
		modal.remove();
	},300);
}

function init_tooltips(container){
	jQuery('.tooltipster').tooltipster({
		theme: 		'tooltipster-light',
		animation:	'grow',
		speed:		200,
		delay: 		100,
		offsetY:	-13
	});
}

// Function to adjust calendar sizing
function adjust_calendar_boxes(){
	jQuery('.booked-calendar').each(function(){
		
		var windowWidth = jQuery(window).width();
		var smallCalendar = jQuery(this).parents('.booked-calendar-wrap').hasClass('small');
		var boxesWidth = jQuery(this).find('tbody tr.week td').width();
		var calendarHeight = jQuery(this).height();
		boxesHeight = boxesWidth * 1;
		jQuery(this).find('tbody tr.week td').height(boxesHeight);
		jQuery(this).find('tbody tr.week td .date').css('line-height',boxesHeight+'px');
		jQuery(this).find('tbody tr.week td .date .number').css('line-height',boxesHeight+'px');
		if (smallCalendar || windowWidth < 720){
			jQuery(this).find('tbody tr.week td .date .number').css('line-height',boxesHeight+'px');
		} else {
			jQuery(this).find('tbody tr.week td .date .number').css('line-height','');
		}

		var calendarHeight = jQuery(this).height();
		jQuery(this).parent().height(calendarHeight);

	});
}