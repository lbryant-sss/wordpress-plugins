( function ( wp, $ ) {
	'use strict';

	if ( ! wp ) {
		return;
	}

	$( function () {
		$( document ).on( 'click', '.th-plugin-action.install-now', function ( event ) {
			const $button = $( event.target );

			if ( $button.hasClass( 'activate-now' ) ) {
				return true;
			}

			event.preventDefault();

			if (
				$button.hasClass( 'updating-message' ) ||
				$button.hasClass( 'button-disabled' )
			) {
				return;
			}

			if (
				wp.updates.shouldRequestFilesystemCredentials &&
				! wp.updates.ajaxLocked
			) {
				wp.updates.requestFilesystemCredentials( event );

				$( document ).on( 'credential-modal-cancel', function () {
					const $message = $( '.install-now.updating-message' );

					$message
						.removeClass( 'updating-message' )
						.text( wp.updates.l10n.installNow );

					wp.a11y.speak( wp.updates.l10n.updateCancel, 'polite' );
				} );
			}

			wp.updates.installPlugin( {
				slug: $button.data( 'slug' ),
			} );
		} );
	} );
} )( window.wp, jQuery );



var thwcfd_plugins_list = (function($, window, document) {
	'use strict';

	$( function () {
		$( document ).on( 'click', '.th-plugin-action.activate-now', function ( event ) {

			const $button = $( event.target );

			event.preventDefault();

			if (
				$button.hasClass( 'updating-message' ) ||
				$button.hasClass( 'button-disabled' )
			) {
				return;
			}

			var url_string = $button.attr('href');
			var url = new URL(url_string);
			var file = url.searchParams.get("plugin");
			var nonce = url.searchParams.get("_wpnonce");
			var action = url.searchParams.get("action");

			if(action == 'activate'){
				action = 'th_activate_plugin';
			}

			if(file == null || nonce == null || action == null){
			     return;
			}

			var data = {
				'action': action,
				'file': file,
				'_wpnonce': nonce,
			};

			jQuery.ajax({
			    type: "post",
			    dataType: "json",
			    url: ajaxurl,
			    data: data,
			    beforeSend: function(){
			        $button.addClass('updating-message');
			        $button.text('Activating');
			    },
			    success: function(data){
			    	$button.removeClass('updating-message');
			    	if(data == true){
			    		$button.text('Activated');
			    		$button.addClass('disabled');
			    	}else{
			    		$button.text('Failed');
			    		$button.addClass('disabled');
			    	}
			    },
			    error: function(xhr){
			    		$button.text('Failed');
			    		$button.addClass('disabled');
			    },
			});
		} );
	} );

}(window.jQuery, window, document));
var thwcfd_base = (function($, window, document) {
	'use strict';

	var _wp$i18n = wp.i18n;
	var __ = _wp$i18n.__;
	var _x = _wp$i18n._x;
	var _n = _wp$i18n._n;
	var _nx = _wp$i18n._nx;

	function escapeHTML(html) {
	   var fn = function(tag) {
		   var charsToReplace = {
			   '&': '&amp;',
			   '<': '&lt;',
			   '>': '&gt;',
			   '"': '&#34;'
		   };
		   return charsToReplace[tag] || tag;
	   }
	   return html.replace(/[&<>"]/g, fn);
	}

	function decodeHtml(str) {
		if(str && typeof(str) === 'string'){
		   	var map = {
	        	'&amp;': '&',
	        	'&lt;': '<',
	        	'&gt;': '>',
	        	'&quot;': '"',
	        	'&#039;': "'"
	    	};
	    	return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
	    }
	    return str;
	}

	function isHtmlIdValid(id) {
		//var re = /^[a-z]+[a-z0-9\_]*$/;
		var re = /^[a-z\_]+[a-z0-9\_]*$/;
		// if(wcfe_var.sanitize_names == false){
		// 	re = /^[a-zA-Z\_]+[a-zA-Z0-9\_]*$/;
		// }
		return re.test(id.trim());
	}

	function isValidHexColor(value) {
		if ( preg_match( '/^#[a-f0-9]{6}$/i', value ) ) { // if user insert a HEX color with #
			return true;
		}
		return false;
	}

	function is_option_field(type){
		var result = false;
		if(type == 'select' || type == 'multiselect' || type == 'radio' || type == 'checkboxgroup'){
			result = true;
		}
		return result;
	}

	function setup_tiptip_tooltips(){
		var tiptip_args = {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		};

		$('.tips').tipTip( tiptip_args );
	}

	function setup_enhanced_multi_select(parent){
		parent.find('select.thwcfd-enhanced-multi-select').each(function(){
			if(!$(this).hasClass('enhanced')){
				$(this).selectWoo({
					//minimumResultsForSearch: 10,
					allowClear : true,
					placeholder: $(this).data('placeholder')
				}).addClass('enhanced');
			}
		});
	}

	function setup_enhanced_multi_select_with_value(parent){
		parent.find('select.thwcfd-enhanced-multi-select').each(function(){
			if(!$(this).hasClass('enhanced')){
				$(this).selectWoo({
					//minimumResultsForSearch: 10,
					allowClear : true,
					placeholder: $(this).data('placeholder')
				}).addClass('enhanced');

				var value = $(this).data('value');
				value = value.split(",");

				$(this).val(value);
				$(this).trigger('change');
			}
		});
	}

	function setup_color_picker(form){
		form.find('.thpladmin-colorpick').iris({
			change: function( event, ui ) {
				$( this ).parent().find( '.thpladmin-colorpickpreview' ).css({ backgroundColor: ui.color.toString() });
			},
			hide: true,
			border: true
		}).click( function() {
			$('.iris-picker').hide();
			$(this ).closest('td').find('.iris-picker').show();
		});

		$('body').click( function() {
			$('.iris-picker').hide();
		});

		$('.thpladmin-colorpick').click( function( event ) {
			event.stopPropagation();
		});
	}

	function setup_color_pick_preview(form){
		form.find('.thpladmin-colorpick').each(function(){
			$(this).parent().find('.thpladmin-colorpickpreview').css({ backgroundColor: this.value });
		});
	}

	function prepare_field_order_indexes(elm) {
		$(elm+" tbody tr").each(function(index, el){
			$('input.f_order', el).val( parseInt( $(el).index(elm+" tbody tr") ) );
		});
	}

	function setup_sortable_table(parent, elm, left){
		parent.find(elm+" tbody").sortable({
			items:'tr',
			cursor:'move',
			axis:'y',
			handle: '.sort',
			scrollSensitivity:40,
			helper:function(e,ui){
				ui.children().each(function(){
					$(this).width($(this).width());
				});
				ui.css('left', left);
				return ui;
			}
		});

		$(elm+" tbody").on("sortstart", function( event, ui ){
			ui.item.css('background-color','#f6f6f6');
		});
		$(elm+" tbody").on("sortstop", function( event, ui ){
			ui.item.removeAttr('style');
			prepare_field_order_indexes(elm);
		});
	}

	function get_property_field_value(form, type, name){
		var value = '';

		switch(type) {
			case 'select':
				value = form.find("select[name=i_"+name+"]").val();
				value = value == null ? '' : value;
				break;

			case 'checkbox':
				value = form.find("input[name=i_"+name+"]").prop('checked');
				value = value ? 1 : 0;
				break;

			case 'textarea':
				value = form.find("textarea[name=i_"+name+"]").val();
				value = value == null ? '' : value;
				break;

			default:
				value = form.find("input[name=i_"+name+"]").val();
				value = value == null ? '' : value;
		}

		return value;
	}

	function set_property_field_value(form, type, name, value, multiple){
		switch(type) {
			case 'select':
				if(multiple == 1){
					value = typeof(value) === 'string' ? value.split(",") : value;
					name = name+"[]";
					form.find('select[name="i_'+name+'"]').val(value).trigger("change");
				}else{
					form.find('select[name="i_'+name+'"]').val(value);
				}
				break;

			case 'checkbox':
				value = value == 1 || value == 'yes' ? true : false;
				form.find("input[name=i_"+name+"]").prop('checked', value);
				break;

			case 'textarea':
				value = value ? decodeHtml(value) : value;
				form.find("textarea[name=i_"+name+"]").val(value);
				break;

			case 'colorpicker':
				var bg_color = value ? { backgroundColor: value } : {};
				form.find("input[name=i_"+name+"]").val(value);
				form.find("."+name+"_preview").css(bg_color);
				break;

			default:
				value = value ? decodeHtml(value) : value;
				form.find("input[name=i_"+name+"]").val(value);
		}
	}

   /*-------------------------------------------
	*---- POPUP WIZARD FUNCTIONS - SATRT -------
	*------------------------------------------*/
	var active_tab = 0;

	function setup_form_wizard(){
		$('.pp_nav_links > li').click(function(){
			var index = $(this).data('index');
			var popup = $(this).closest('.thpladmin-modal-mask');

			open_tab(popup, $(this), index);
			active_tab = index;
		});
	}

	function get_popup(elm){
		return $(elm).closest('.thpladmin-modal-mask');
	}

	function get_active_tab(popup){
		return popup.find('ul.pp_nav_links').find('li.active')
	}

	function get_next_tab_index(elm){
		var popup  = get_popup(elm);
		var active = get_active_tab(popup);

		var link = active.nextAll("li").not(".disabled").first();
		var index = link.length ? link.data('index') : active_tab;
		return index;
	}

	function get_prev_tab_index(elm){
		var popup  = get_popup(elm);
		var active = get_active_tab(popup);

		var link = active.prevAll("li").not(".disabled").first();
		var index = link.length ? link.data('index') : active_tab;
		return index;
	}

	function form_wizard_open(popup){
		active_tab = 0;
		popup.find('ul.pp_nav_links li').first().click();
		popup.css("display", "block");
	}

	function form_wizard_close(elm) {
		var popup = get_popup(elm);
		popup.css("display", "none");
		active_tab = 0;
	}

	function form_wizard_next(elm){
		active_tab = get_next_tab_index(elm);
		move_to(elm, active_tab);
	}

	function form_wizard_previous(elm){
		active_tab = get_prev_tab_index(elm);
		move_to(elm, active_tab);
	}

	function form_wizard_start(elm){
		active_tab = 0;
		move_to(elm, active_tab);
	}

	function move_to(elm, index){
		var popup = get_popup(elm);
		var link = popup.find('*[data-index="'+index+'"]');
		open_tab(popup, link, index);
	}

	function open_tab(popup, link, index){
		var panel = popup.find('.data_panel_'+index);

		close_all_data_panel(popup);
		link.addClass('active');
		panel.css("display", "block");

		enable_disable_btns(popup, link);
	}

	function close_all_data_panel(popup){
		popup.find('.pp_nav_links > li').removeClass('active');
		popup.find('.data-panel').css("display", "none");
	}

	function enable_disable_tab(popup, index, disable){
		var link = popup.find('*[data-index="'+index+'"]');
		var panel = popup.find('.data_panel_'+index);

		if(disable){
			link.addClass('disabled');
			panel.find(":input").attr("disabled", true);
			//panel.css("display", "none");
		}else{
			link.removeClass('disabled');
			panel.find(":input").attr("disabled", false);
			//panel.css("display", "block");
		}
	}

	function form_wizard_enable_tab(popup, index){
		enable_disable_tab(popup, index, 0);
	}
	function form_wizard_disable_tab(popup, index){
		enable_disable_tab(popup, index, 1);
	}
	function form_wizard_enable_all_tabs(popup){
		popup.find('.pp_nav_links > li').removeClass('disabled');
	}

	function enable_disable_btns(popup, link){
		var nextBtn = popup.find('.next-btn');
		var prevBtn = popup.find('.prev-btn');
		var nextBtnTxt = 'Save & Next';

		if(link.hasClass('first')){
			nextBtn.prop( "disabled", false );
			prevBtn.prop( "disabled", true );
		}else if(link.hasClass('last')){
			nextBtn.prop( "disabled", true );
			prevBtn.prop( "disabled", false );
			nextBtnTxt = 'Save & Close';
		}else{
			nextBtn.prop( "disabled", false );
			prevBtn.prop( "disabled", false );
		}
	}

   /*-------------------------------------------
	*---- POPUP WIZARD FUNCTIONS - END ---------
	*------------------------------------------*/

	return {
		escapeHTML : escapeHTML,
		decodeHtml : decodeHtml,
		isHtmlIdValid : isHtmlIdValid,
		isValidHexColor : isValidHexColor,
		is_option_field : is_option_field,
		setup_tiptip_tooltips : setup_tiptip_tooltips,
		setupEnhancedMultiSelect : setup_enhanced_multi_select,
		setupEnhancedMultiSelectWithValue : setup_enhanced_multi_select_with_value,
		setupColorPicker : setup_color_picker,
		setup_color_pick_preview : setup_color_pick_preview,
		setupSortableTable : setup_sortable_table,
		get_property_field_value : get_property_field_value,
		set_property_field_value : set_property_field_value,

		setup_form_wizard : setup_form_wizard,
		form_wizard_open : form_wizard_open,
		form_wizard_close : form_wizard_close,
		form_wizard_next : form_wizard_next,
		form_wizard_previous : form_wizard_previous,
		form_wizard_start : form_wizard_start,
		form_wizard_enable_tab : form_wizard_enable_tab,
		form_wizard_disable_tab : form_wizard_disable_tab,
		form_wizard_enable_all_tabs : form_wizard_enable_all_tabs,
   	};
}(window.jQuery, window, document));

function thwcfdSetupEnhancedMultiSelectWithValue(elm){
	thwcfd_base.setupEnhancedMultiSelectWithValue(elm);
}

function thwcfdSetupSortableTable(parent, elm, left){
	thwcfd_base.setupSortableTable(parent, elm, left);
}

function thwcfdCloseModal(elm){
	thwcfd_base.form_wizard_close(elm);
}
function thwcfdWizardNext(elm){
	thwcfd_base.form_wizard_next(elm);
}
function thwcfdWizardPrevious(elm){
	thwcfd_base.form_wizard_previous(elm);
}
var thwcfd_settings_field = (function($, window, document) {
	'use strict';

	var _wp$i18n = wp.i18n;
	var __ = _wp$i18n.__;
	var _x = _wp$i18n._x;
	var _n = _wp$i18n._n;
	var _nx = _wp$i18n._nx;	

	var MSG_INVALID_NAME = __('NAME/ID must begin with a lowercase letter ([a-z]) or underscores ("_") and may be followed by any number of lowercase letters, digits ([0-9]) and underscores ("_")', 'woo-checkout-field-editor-pro');
	var SPECIAL_FIELD_TYPES = ["country", "state", "city", "tel"];

	var FIELD_FORM_PROPS = {
		name  : {name : 'name', type : 'text'},
		type  : {name : 'type', type : 'select'},
		title          : {name : 'title', type : 'text'},
		label       : {name : 'label', type : 'text'},
		default     : {name : 'default', type : 'text'},
		placeholder : {name : 'placeholder', type : 'text'},
		class       : {name : 'class', type : 'text'},
		validate    : {name : 'validate', type : 'select', multiple : 1 },
		
		title_type  : {name : 'title_type', type : 'select'},
		
		checked : {name : 'checked', type : 'checkbox'},

		required : {name : 'required', type : 'checkbox'},
		enabled  : {name : 'enabled', type : 'checkbox'},

		show_in_email : {name : 'show_in_email', type : 'checkbox'},
		show_in_order : {name : 'show_in_order', type : 'checkbox'},
	};

	var BLOCK_FIELD_FORM_PROPS = {
		name  : {name : 'name', type : 'text'},
		type  : {name : 'type', type : 'select'},

		value : {name : 'value', type : 'text'},
		placeholder : {name : 'placeholder', type : 'text'},
		description : {name : 'description', type : 'text'},
		validate    : {name : 'validate', type : 'select' },
		cssclass    : {name : 'cssclass', type : 'text'},

		title          : {name : 'title', type : 'text'},

		order_meta : {name : 'order_meta', type : 'checkbox'},
		user_meta  : {name : 'user_meta', type : 'checkbox'},

		checked  : {name : 'checked', type : 'checkbox'},
		required : {name : 'required', type : 'checkbox'},
		clear 	 : {name : 'clear', type : 'checkbox'},
		enabled  : {name : 'enabled', type : 'checkbox'},

		show_in_email : {name : 'show_in_email', type : 'checkbox'},
		show_in_email_customer : {name : 'show_in_email_customer', type : 'checkbox'},
		show_in_order : {name : 'show_in_order', type : 'checkbox'},
		show_in_thank_you_page : {name : 'show_in_thank_you_page', type : 'checkbox'},
		show_in_my_account_page : {name : 'show_in_my_account_page', type : 'checkbox'},

	};

	var FIELDS_TO_HIDE = {
		radio : ['placeholder', 'validate'],
		select : ['validate'],
		password: ['default'],
	};	

	function open_new_field_form(sname, checkout_type){
		open_field_form('new', false, sname, checkout_type);
	}

	function open_edit_field_form(elm, rowId, checkout_type){
		open_field_form('edit', elm, false, checkout_type);
	}

	function open_copy_field_form(elm, rowId, checkout_type){
		open_field_form('copy', elm, false, checkout_type);
	}

	function open_field_form(type, elm, sname, checkout_type){
		var popup = $("#thwcfd_field_form_pp");
		var form  = $("#thwcfd_field_form");

		populate_field_form(popup, form, type, elm, sname, checkout_type);

		thwcfd_base.form_wizard_open(popup);
		//thwcfd_base.setup_color_pick_preview(form);
	}

	function populate_field_form(popup, form, action, elm, sname, checkout_type){

		var title = action === 'edit' ? __('Edit Field', 'woo-checkout-field-editor-pro') : __('New Field', 'woo-checkout-field-editor-pro');
		popup.find('.wizard-title').text(title);

		form.find('.err_msgs').html('');
		form.find("input[name=f_action]").val(action);

		if(action === 'new'){
			if(sname == 'billing' || sname == 'shipping' || sname == 'additional'){
				sname = sname+'_';
			}else{
				sname = '';
			}

			clear_field_form_general(form);
			//clear_field_form_display(form);
			form.find("select[name=i_type]").change();

			thwcfd_base.set_property_field_value(form, 'text', 'name', sname, 0);
			thwcfd_base.set_property_field_value(form, 'text', 'class', 'form-row-wide', 0);

		}else{
			var row = $(elm).closest('tr');
			var props_json = row.find(".f_props").val();
			var props = JSON.parse(props_json);

			populate_field_form_general(action, form, props);
			form.find("select[name=i_type]").change();
			populate_field_form_props(form, row, props, checkout_type);
		}
	}

	function clear_field_form_general(form){
		thwcfd_base.set_property_field_value(form, 'hidden', 'autocomplete', '', 0);
		thwcfd_base.set_property_field_value(form, 'hidden', 'priority', '', 0);
		thwcfd_base.set_property_field_value(form, 'hidden', 'custom', '', 0);
		thwcfd_base.set_property_field_value(form, 'hidden', 'oname', '', 0);
		thwcfd_base.set_property_field_value(form, 'hidden', 'otype', '', 0);

		thwcfd_base.set_property_field_value(form, 'select', 'type', 'text', 0);
		thwcfd_base.set_property_field_value(form, 'text', 'name', '', 0);
		/*
		thwcfd_base.set_property_field_value(form, 'text', 'label', '', 0);
		thwcfd_base.set_property_field_value(form, 'text', 'placeholder', '', 0);
		thwcfd_base.set_property_field_value(form, 'text', 'default', '', 0);
		thwcfd_base.set_property_field_value(form, 'text', 'class', '', 0);
		thwcfd_base.set_property_field_value(form, 'select', 'validate', '', 1);

		thwcfd_base.set_property_field_value(form, 'checkbox', 'required', 1, 0);
		thwcfd_base.set_property_field_value(form, 'checkbox', 'enabled', 1, 0);
		thwcfd_base.set_property_field_value(form, 'checkbox', 'show_in_email', 1, 0);
		thwcfd_base.set_property_field_value(form, 'checkbox', 'show_in_order', 1, 0);
		*/
	}

	/*
	function clear_field_form_display(form){
		thwcfd_base.set_property_field_value(form, 'text', 'class', '', 0);
		thwcfd_base.set_property_field_value(form, 'checkbox', 'show_in_email', 1, 0);
		thwcfd_base.set_property_field_value(form, 'checkbox', 'show_in_order', 1, 0);
	}
	*/

	function populate_field_form_general(action, form, props){
		var autocomplete = props['autocomplete'] ? props['autocomplete'] : '';
		var priority = props['priority'] ? props['priority'] : '';
		var custom = props['custom'] ? props['custom'] : '';

		var type = props['type'] ? props['type'] : 'text';
		var name = props['name'] ? props['name'] : '';

		if(action === 'copy'){
			name = '';
		}

		thwcfd_base.set_property_field_value(form, 'hidden', 'autocomplete', autocomplete, 0);
		thwcfd_base.set_property_field_value(form, 'hidden', 'priority', priority, 0);
		thwcfd_base.set_property_field_value(form, 'hidden', 'custom', custom, 0);
		thwcfd_base.set_property_field_value(form, 'hidden', 'oname', name, 0);
		thwcfd_base.set_property_field_value(form, 'hidden', 'otype', type, 0);

		thwcfd_base.set_property_field_value(form, 'select', 'type', type, 0);
		thwcfd_base.set_property_field_value(form, 'text', 'name', name, 0);
		thwcfd_base.set_property_field_value(form, 'hidden', 'name_old', name, 0);

		// if(type == "country" || type == "state" || type == "city"){
		// 	form.find("select[name=i_type]").prop('disabled', true);
		// }else{
		// 	form.find("select[name=i_type]").prop('disabled', false);
		// }
	}

	function populate_field_form_props(form, row, props, checkout_type){
		var ftype  = props.type;
		var custom = props['custom'] ? props['custom'] : '';
		var current_section_form_fields = checkout_type === 'block' ? BLOCK_FIELD_FORM_PROPS : FIELD_FORM_PROPS;
		$.each( current_section_form_fields, function( name, field ) {
			if(name == 'name' || name == 'type') {
				return true;
			}

			var type   = field['type'];
			var value  = props && props[name] ? props[name] : '';

			if(ftype == 'textarea' && name == 'default'){
				type = "textarea";
			}

			thwcfd_base.set_property_field_value(form, type, name, value, field['multiple']);

			if(type == 'select'){
				name = field['multiple'] == 1 ? name+"[]" : name;

				if(field['multiple'] == 1 || field['change'] == 1){
					form.find('select[name="i_'+name+'"]').trigger("change");
				}
			}else if(type == 'checkbox'){
				if(field['change'] == 1){
					form.find('input[name="i_'+name+'"]').trigger("change");
				}
			}
		});

		var optionsJson = row.find(".f_options").val();
		populate_options_list(form, optionsJson);

		if(custom == 1){
			form.find("input[name=i_name]").prop('disabled', false);
			form.find("select[name=i_type]").prop('disabled', false);
			form.find("input[name=i_show_in_email]").prop('disabled', false);
			form.find("input[name=i_show_in_order]").prop('disabled', false);
		}else{
			thwcfd_base.set_property_field_value(form, 'checkbox', 'show_in_email', true, 0);
			thwcfd_base.set_property_field_value(form, 'checkbox', 'show_in_order', true, 0);
			thwcfd_base.set_property_field_value(form, 'checkbox', 'order_meta', true, 0);
			thwcfd_base.set_property_field_value(form, 'checkbox', 'user_meta', true, 0);

			form.find("input[name=i_name]").prop('disabled', true);
			form.find("select[name=i_type]").prop('disabled', true);
			form.find("input[name=i_show_in_email]").prop('disabled', true);
			form.find("input[name=i_show_in_order]").prop('disabled', true);
			form.find("input[name=i_label]").focus();
		}
	}

	function field_type_change_listner(elm){
		var popup = $("#thwcfd_field_form_pp");
		var form = $(elm).closest('form');
		var type = $(elm).val();
		type = type == null ? 'text' : type;
		form.find('.thwcfd_field_form_tab_general_placeholder').html($('#thwcfd_field_form_id_'+type).html());

		enable_all_tabs_and_fields(popup, form);

		if(type in FIELDS_TO_HIDE){
			$.each(FIELDS_TO_HIDE[type], function(index, name) {

				if(FIELD_FORM_PROPS[name]){
					var f_props = FIELD_FORM_PROPS[name];
					disable_hide_field(form, f_props['type'], name);
				}
			});
		}		

		thwcfd_base.setupEnhancedMultiSelect(form);
		thwcfd_base.setupColorPicker(form);
		thwcfd_base.setupSortableTable(form, '.thwcfd-option-list', '100');
	}

	function enable_all_tabs_and_fields(popup, form){
		thwcfd_base.form_wizard_enable_all_tabs(popup);
		form.find(':input').attr("disabled", false);
		form.find('tr').removeClass('disabled hide');
	}

	function enable_disable_field(form, type, name, enabled, hide){
		var elm = null;

		switch(type) {
			case 'select':
				elm = form.find('select[name="i_'+name+'"]');
				if(elm.length == 0){
					elm = form.find('select[name="i_'+name+'[]"]');
				}
				break;

			case 'textarea':
				elm = form.find("textarea[name=i_"+name+"]");
				if(elm.length == 0){
					elm = form.find('textarea[name="i_'+name+'[]"]');
				}			
				break;

			default:
				elm = form.find("input[name=i_"+name+"]");
				if(elm.length == 0){
					elm = form.find('input[name="i_'+name+'[]"]');
				}		
		}

		if(elm && elm.length){
			var rowClass = hide ? 'disabled hide' : 'disabled';

			if(!enabled){
				elm.attr("disabled", true);
				elm.closest('tr.form_field_'+name).addClass(rowClass);
			}else{
				elm.attr("disabled", false);
				elm.closest('tr.form_field_'+name).removeClass('disabled hide');
			}
		}
	}
	function enable_field(form, type, name){
		enable_disable_field(form, type, name, true, false);
	}
	function disable_field(form, type, name){
		enable_disable_field(form, type, name, false, false);
	}
	function disable_hide_field(form, type, name){
		enable_disable_field(form, type, name, false, true);
	}

	function save_field(elm){
		var popup = $("#thwcfd_field_form_pp");
		var form  = $("#thwcfd_field_form");
		var result = validate_field_form(form, popup);

		if(result){
			prepare_field_form(form);
			form.submit();
		}
	}

	function validate_field_form(form, popup){
		var err_msgs = '';

		var fname  = thwcfd_base.get_property_field_value(form, 'text', 'name');
		var ftype  = thwcfd_base.get_property_field_value(form, 'select', 'type');
		var ftitle = thwcfd_base.get_property_field_value(form, 'text', 'label');
		var fotype = thwcfd_base.get_property_field_value(form, 'hidden', 'otype');
		var fvalue = thwcfd_base.get_property_field_value(form, 'text', 'default');
		var option_values = form.find("input[name='i_options_key[]']").map(function(){ return $(this).val(); }).get();

		if(ftype == '' && ($.inArray(fotype, SPECIAL_FIELD_TYPES) == -1) ){
			err_msgs = 'Type is required';

		}else if(fname == ''){
			err_msgs = 'Name is required';

		}else if(!thwcfd_base.isHtmlIdValid(fname)){
			err_msgs = MSG_INVALID_NAME;
		}

		if(fvalue && (option_values.length>0) && (ftype == 'select' || ftype == 'radio' || ftype == 'multiselect' || ftype == 'checkboxgroup')){
			if(ftype == 'select' || ftype == 'radio'){
				if(!(option_values.includes(fvalue))){
					err_msgs = __('Enter default value given in the options.', 'woo-checkout-field-editor-pro');
				}
			}else if(ftype == 'multiselect' || ftype == 'checkboxgroup'){
				var value_array = fvalue.split(', ');
				for(var i = 0; i < value_array.length; i++){
				    var value = value_array[i];
					if(value && !(option_values.includes(value))){
						err_msgs = __('Enter default values given in the options.', 'woo-checkout-field-editor-pro');
					}
				};
			}
		}

		if(fvalue && ftype == 'number' && (/^-?\d+$/.test(fvalue) === false)){
			err_msgs = __('Default value must be a number.', 'woo-checkout-field-editor-pro');
		}

		if(err_msgs != ''){
			form.find('.err_msgs').html(err_msgs);
			thwcfd_base.form_wizard_start(popup);
			return false;
		}

		//return false;

		return true;
	}

	function prepare_field_form(form){
		var options_json = get_options(form);
		thwcfd_base.set_property_field_value(form, 'hidden', 'options_json', options_json, 0);
		thwcfd_base.set_property_field_value(form, 'hidden', 'options', options_json, 0); //Replaced `options_json` with `options` for block-based checkout inorder to make identical code base.
	}
   /*------------------------------------
	*---- PRODUCT FIELDS - END ----------
	*------------------------------------*/

   /*------------------------------------
	*---- OPTIONS FUNCTIONS - SATRT -----
	*------------------------------------*/
	function get_options(form){
		var optionsKey  = form.find("input[name='i_options_key[]']").map(function(){ return $(this).val(); }).get();
		var optionsText = form.find("input[name='i_options_text[]']").map(function(){ return $(this).val(); }).get();

		var optionsSize = optionsText.length;
		var optionsArr = [];

		for(var i=0; i<optionsSize; i++){
			var optionDetails = {};
			optionDetails["key"] = optionsKey[i];
			optionDetails["text"] = optionsText[i];

			optionsArr.push(optionDetails);
		}

		var optionsJson = optionsArr.length > 0 ? JSON.stringify(optionsArr) : '';
		optionsJson = encodeURIComponent(optionsJson);
		return optionsJson;
	}

	function populate_options_list(form, optionsJson){
		var optionsHtml = "";

		if(optionsJson){
			try{
				optionsJson = decodeURIComponent(optionsJson);
				var optionsList = $.parseJSON(optionsJson);
				if(optionsList){
					jQuery.each(optionsList, function() {
						optionsHtml += prepare_option_row_html(this);
					});
				}
			}catch(err) {
				console.log(err);
			}
		}

		var optionsTable = form.find(".thwcfd-option-list tbody");
		if(optionsHtml){
			optionsTable.html(optionsHtml);
		}else{
			optionsTable.html(prepare_option_row_html(null));
		}
	}

	function prepare_option_row_html(option){
		var key = '';
		var text = '';

		if(option){
			key = option.key ? option.key : '';
			text = option.text ? option.text : '';
		}

		var html  = '<tr>';
	        html += '<td class="key"><input type="text" name="i_options_key[]" value="'+key+'" placeholder="' + __('Option Value' , 'woo-checkout-field-editor-pro') + '"></td>';
			html += '<td class="value"><input type="text" name="i_options_text[]" value="'+text+'" placeholder="' + __('Option Text', 'woo-checkout-field-editor-pro') + '"></td>';
			html += '<td class="action-cell">';
			html += '<a href="javascript:void(0)" onclick="thwcfdAddNewOptionRow(this)" class="btn btn-tiny btn-primary" title="'+ __('Add new option', 'woo-checkout-field-editor-pro') +'">+</a>';
			html += '<a href="javascript:void(0)" onclick="thwcfdRemoveOptionRow(this)" class="btn btn-tiny btn-danger" title="'+ __('Remove option', 'woo-checkout-field-editor-pro') +'">x</a>';
			html += '<span class="btn btn-tiny sort ui-sortable-handle"></span></td>';
			html += '</tr>';

		return html;
	}

	function add_new_option_row(elm){
		var ptable = $(elm).closest('table');
		var optionsSize = ptable.find('tbody tr').length;

		if(optionsSize > 0){
			ptable.find('tbody tr:last').after(prepare_option_row_html(null));
		}else{
			ptable.find('tbody').append(prepare_option_row_html(null));
		}
	}

	function remove_option_row(elm){
		var ptable = $(elm).closest('table');
		$(elm).closest('tr').remove();
		var optionsSize = ptable.find('tbody tr').length;

		if(optionsSize == 0){
			ptable.find('tbody').append(prepare_option_row_html(null));
		}
	}
   /*------------------------------------
	*---- OPTIONS FUNCTIONS - END -------
	*------------------------------------*/

	return {
		openNewFieldForm : open_new_field_form,
		openEditFieldForm : open_edit_field_form,
		openCopyFieldForm : open_copy_field_form,
		fieldTypeChangeListner : field_type_change_listner,
		addNewOptionRow : add_new_option_row,
		removeOptionRow : remove_option_row,
		save_field : save_field,
   	};
}(window.jQuery, window, document));

function thwcfdOpenNewFieldForm(sectionName, checkout_type){
	checkout_type = typeof checkout_type !== 'undefined' ? checkout_type : 'classic';
	thwcfd_settings_field.openNewFieldForm(sectionName, checkout_type);
}

function thwcfdOpenEditFieldForm(elm, rowId, checkout_type){
	checkout_type = typeof checkout_type !== 'undefined' ? checkout_type : 'classic';
	thwcfd_settings_field.openEditFieldForm(elm, rowId, checkout_type);
}

function thwcfdOpenCopyFieldForm(elm, rowId, checkout_type){
	checkout_type = typeof checkout_type !== 'undefined' ? checkout_type : 'classic';
	thwcfd_settings_field.openCopyFieldForm(elm, rowId, checkout_type);
}

function thwcfdFieldTypeChangeListner(elm){
	thwcfd_settings_field.fieldTypeChangeListner(elm);
}

function thwcfdAddNewOptionRow(elm){
	thwcfd_settings_field.addNewOptionRow(elm);
}
function thwcfdRemoveOptionRow(elm){
	thwcfd_settings_field.removeOptionRow(elm);
}

function thwcfdSaveField(elm){
	thwcfd_settings_field.save_field(elm);
}

var thwcfd_settings_section = (function($, window, document) {
	'use strict';

	var MSG_INVALID_NAME = 'NAME/ID must begin with a lowercase letter ([a-z]) and may be followed by any number of lowercase letters, digits ([0-9]) and underscores ("_")';

	var BLOCK_SECTION_FORM_FIELDS = {
		name 	   : {name : 'name', label : 'Name/ID', type : 'text', required : 1},
		position   : {name : 'position', label : 'Display Position', type : 'hidden', value: 'set_from_block'},
		cssclass   : {name : 'cssclass', label : 'CSS Class', type : 'text'},
        show_title : {name : 'show_title', label : 'Show section title in checkout page.', type : 'checkbox', value : 'yes', checked : true},
		
		title 		: {name : 'title', label : 'Title', type : 'text'},
		title_class : {name : 'title_class', label : 'Title Class', type : 'text'},

		subtitle 	   : {name : 'subtitle', label : 'Subtitle', type : 'text'},	
	};
	var RESERVED_SECTION_NAMES = ['address', 'contact', 'order'];
	
	function open_edit_section_form(valueJson, checkout_type){
		open_section_form(checkout_type, 'edit', valueJson);
	}

	function open_section_form(checkout_type, type, valueJson){
		var popup = $("#thwcfd_section_form_pp");
		var form  = $("#thwcfd_section_form");

		populate_section_form( checkout_type,popup, form, type, valueJson);
		
		thwcfd_base.form_wizard_open(popup);
	}

	function populate_section_form(checkout_type, popup, form, type, valueJson){
		var title = type === 'edit' ? 'Edit Section' : 'New Section';
		popup.find('.wizard-title').text(title);

		form.find('.err_msgs').html('');
		form.find("input[name=i_name]").prop("readonly", false);

		form.find("input[name=s_action]").val(type);
		form.find("input[name=s_name]").val('');
		form.find("input[name=s_name_copy]").val('');
		form.find("input[name=i_position_old]").val('');
		form.find("input[name=i_rules]").val('');
		form.find("input[name=i_rules_ajax]").val('');
		form.find("input[name=i_repeat_rules]").val('');
		//var current_section_form_fields = checkout_type === 'block' ? BLOCK_SECTION_FORM_FIELDS : SECTION_FORM_FIELDS;
		var current_section_form_fields = BLOCK_SECTION_FORM_FIELDS;
		if(type === 'new'){
			set_form_field_values(form, current_section_form_fields, false);

		}else{
			set_form_field_values(form, current_section_form_fields, valueJson);

			if(type === 'copy'){
				var sNameCopy = valueJson ? valueJson['name'] : '';
				form.find("input[name=i_name]").val("");
				form.find("input[name=s_name_copy]").val(sNameCopy);
			}else{
				form.find("input[name=i_name]").prop("readonly", true);
			}

			form.find("select[name=i_position_old]").val(valueJson.position);
			setTimeout(function(){form.find("select[name=i_position]").focus();}, 1);
		}
	}
	
	function set_form_field_values(form, fields, valuesJson){
		var sname = valuesJson && valuesJson['name'] ? valuesJson['name'] : '';
		$.each( fields, function( name, field ) {
			var type = field['type'];
			if(valuesJson){
				var value = valuesJson[name] ? valuesJson[name] : '';
			}else{
				var value = valuesJson[name] ? valuesJson[name] : field['value'];
			}

			var multiple = field['multiple'] ? field['multiple'] : 0;

			if(type === 'checkbox'){
				if(!valuesJson && field['checked']){
					value = field['checked'];
				}
			}

			thwcfd_base.set_property_field_value(form, type, name, value, multiple);
		});
		
		var prop_form = $('#section_prop_form_'+sname);
		
		var rulesAction = valuesJson && valuesJson['rules_action'] ? valuesJson['rules_action'] : 'show';
		var rulesActionAjax = valuesJson && valuesJson['rules_action_ajax'] ? valuesJson['rules_action_ajax'] : 'show';
		var conditionalRules = prop_form.find(".f_rules").val();
		var conditionalRulesAjax = prop_form.find(".f_rules_ajax").val();
	}
	
	function save_section(elm){
		var popup = $("#thwcfd_section_form_pp");
		var form  = $("#thwcfd_section_form");
		var result = validate_section(form, popup);

		if(result){
			form.submit();
		}
	}
	
	function validate_section(form, popup){
		var name  = form.find("input[name=i_name]").val();
		var title = form.find("input[name=i_title]").val();
		var positionElement = form.find("select[name=i_position]");
    	var position = positionElement.length ? positionElement.val() : form.find("input[name=i_position]").val() || '';

		name = name ? name : '';
		position = position ? position : '';
		
		var err_msgs = '';
		if(name.trim() == ''){
			err_msgs = 'Name/ID is required';
		}else if(!thwcfd_base.isHtmlIdValid(name)){
			err_msgs = MSG_INVALID_NAME;
		}else if(RESERVED_SECTION_NAMES.indexOf(name) !== -1){
			err_msgs = 'The provided Name/ID is already used for the default section. Please use a different name.';
		}else if(title.trim() == ''){
			err_msgs = 'Title is required';
		}
		
		if(err_msgs != ''){
			form.find('.err_msgs').html(err_msgs);
			thwcfd_base.form_wizard_start(popup);
			return false;
		}		
		return true;
	}
	
	   				
	return {
		openEditSectionForm : open_edit_section_form,
		save_section : save_section,
   	};
}(window.jQuery, window, document));


function thwcfdOpenEditSectionForm(section, checkout_type){
	checkout_type = typeof checkout_type !== 'undefined' ? checkout_type : 'classic';
	thwcfd_settings_section.openEditSectionForm(section, checkout_type);		
}

function thwcfdSaveSection(elm){
	thwcfd_settings_section.save_section(elm);	
}



var thwcfd_settings = (function($, window, document) {
	'use strict';

	var _wp$i18n = wp.i18n;
	var __ = _wp$i18n.__;
	var _x = _wp$i18n._x;
	var _n = _wp$i18n._n;
	var _nx = _wp$i18n._nx;
		
	$(function() {
		var settings_form = $('#thwcfd_checkout_fields_form');

		thwcfd_base.setupSortableTable(settings_form, '#thwcfd_checkout_fields', '0');
		thwcfd_base.setup_tiptip_tooltips();
		thwcfd_base.setup_form_wizard();
	});

	$(document).keypress(function(e) {
		if ($("#thwcfd_field_form_pp").is(':visible') && (e.keycode == 13 || e.which == 13)) {
			e.preventDefault();
			thwcfdSaveField(this);
		}
	});
	$(document).ready(function(e){
		var feature_popup = $(".thwcfd-pro-discount-popup");
	    var feature_popup_wrapper = $(".thwcfd-pro-discount-popup-wrapper");

	    if (feature_popup.length > 0) {
	    	$('body').css('overflow','hidden');
	        feature_popup[0].style.display = "flex";
	    }
	});
   
	function select_all_fields(elm){
		var checkAll = $(elm).prop('checked');
		$('#thwcfd_checkout_fields tbody input:checkbox[name=select_field]').prop('checked', checkAll);
	}
   	
	function remove_selected_fields(){
		$('#thwcfd_checkout_fields tbody tr').removeClass('strikeout');
		$('#thwcfd_checkout_fields tbody input:checkbox[name=select_field]:checked').each(function () {
			var row = $(this).closest('tr');
			if(!row.hasClass("strikeout")){
				row.addClass("strikeout");
			}
			row.find(".f_deleted").val(1);
			//row.find(".f_edit_btn").prop('disabled', true);
	  	});
	}

	function enable_disable_selected_fields(enabled){
		$('#thwcfd_checkout_fields tbody input:checkbox[name=select_field]:checked').each(function(){
			var row = $(this).closest('tr');
			if(enabled == 0){
				if(!row.hasClass("thpladmin-disabled")){
					row.addClass("thpladmin-disabled");
				}
			}else{
				row.removeClass("thpladmin-disabled");				
			}
			
			//row.find(".f_edit_btn").prop('disabled', enabled == 1 ? false : true);
			row.find(".td_enabled").html(enabled == 1 ? '<span class="dashicons dashicons-yes tips" data-tip="'+ __('Yes', 'woo-checkout-field-editor-pro') +'"></span>' : '-');
			row.find(".f_enabled").val(enabled);
	  	});	
	}
	function widgetPopUp() {
		var x = document.getElementById("myDIV");
    	var y = document.getElementById("myWidget");
    	var th_animation=document.getElementById("th_quick_border_animation")
    	var th_arrow = document.getElementById("th_arrow_head");

    	if (x.style.display === "none" || !x.style.display) {
        	x.style.display = "block";
//         	y.style.background = "#D34156";
        	th_arrow.style="transform:rotate(-12.5deg);";
        	th_animation.style="box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);";
        	th_animation.style.animation='none';
    	} else {
        	x.style.display = "none";
//         	y.style.background = "#000000";
        	th_arrow.style="transform:rotate(45deg);"
        	th_animation.style.animation='pulse 1.5s infinite';
    	}
	}
	function widgetClose() {
    	var z = document.getElementById("myDIV");
	    var za = document.getElementById("myWidget");
		var th_animation=document.getElementById("th_quick_border_animation")
	    var th_arrow = document.getElementById("th_arrow_head");
	    z.style.display = "none";
		th_arrow.style="transform:rotate(45deg);"
	    th_animation.style.animation='pulse 1.5s infinite';
	}
	function accordionexpand(elm){
		var curr_panel = elm.getElementsByClassName("panel")[0];
		var accordion_qstn = elm.getElementsByClassName("accordion-qstn")[0];
		var accordion_qstn_img = elm.getElementsByClassName("accordion-img")[0];
		var accordion_qstn_img_opn = elm.getElementsByClassName("accordion-img-opn")[0];
		var accordion_qstn_para = accordion_qstn.querySelector('p');
		var panel = document.getElementsByClassName("panel");
		var i;
		for(i = 0; i < panel.length; i++){
			if (curr_panel != panel[i]) {
				if(panel[i].style.display === "block"){
					var parentaccordion = panel[i].parentNode;
					var parent_accordion_qstn = parentaccordion.getElementsByClassName("accordion-qstn")[0];
					var parent_accordion_img = parentaccordion.getElementsByClassName("accordion-img")[0];
					var parent_accordion_img_opn = parentaccordion.getElementsByClassName("accordion-img-opn")[0];
					var parent_accordion_qstn_p = parent_accordion_qstn.querySelector('p');
					panel[i].style.display = "none";
					parent_accordion_qstn_p.style.color = "#121933";
					parentaccordion.style.zIndex = "unset";
					parentaccordion.style.borderColor = "#dfdfdf";
					parent_accordion_qstn.style.marginTop = "0px";
					parent_accordion_img.style.display = "block";
					parent_accordion_img_opn.style.display = "none";
				}
			}
		}
		if (curr_panel.style.display === "block") {
			curr_panel.style.display = "none";
			accordion_qstn_para.style.color = "#121933";
			elm.style.zIndex = "unset";
			accordion_qstn.style.marginTop = "0";
			elm.style.borderColor = "#dfdfdf";
			accordion_qstn_img.style.display = "block";
			accordion_qstn_img_opn.style.display = "none";
		} else {
			curr_panel.style.display = "block";
			accordion_qstn_para.style.color = "#6E55FF";
			elm.style.zIndex = "1";
			elm.style.borderColor = "#6E55FF";
			accordion_qstn.style.marginTop = "1.53rem";
			accordion_qstn_img.style.display = "none";
			accordion_qstn_img_opn.style.display = "block";
		}
	}
	var slideIndex = 1;
	var count = 0;
	var myTimer;
	var contentTimer;
	var slideshowContainer;

	window.addEventListener("load",function() {
		showSlides(slideIndex);
	    myTimer = setInterval(function(){plusSlides(1)}, 3000);
	    slideshowContainer = document.getElementsByClassName('th-user-review-section')[0];
	    if(slideshowContainer){
	    	slideshowContainer.addEventListener('mouseenter', pause)
		    slideshowContainer.addEventListener('mouseleave', resume)
			slideContent(count);
			contentTimer = setInterval(function(){ contentchange(1)},3000);
	    }
	})
	function plusSlides(n){
		clearInterval(myTimer);
		if (n < 0){
			showSlides(slideIndex -= 1);
		} else {
			showSlides(slideIndex += 1); 
		}
		if (n === -1){
			myTimer = setInterval(function(){plusSlides(n + 2)}, 3000);
		} else {
			myTimer = setInterval(function(){plusSlides(n + 1)}, 3000);
		}
	}
	function contentchange(n){
		clearInterval(contentTimer);
	  	if(n<0){
	  		slideContent(count -= 1);
	  	}else{
	  		slideContent(count += 1);
	  	}
	  	if (n === -1){
		    contentTimer = setInterval(function(){ contentchange(1)},3000);
		} else {
		    contentTimer = setInterval(function(){ contentchange(1)},3000);
		}
	}
	function currentSlide(n){
		clearInterval(myTimer);
		myTimer = setInterval(function(){plusSlides(n + 1)}, 3000);
		showSlides(slideIndex = n);
		clearInterval(contentTimer);
		contentTimer = setInterval(function(){ contentchange(n+1)},3000);
		slideContent(count = n);
	}
	function slideContent(n){
		var review_heading = ['Great plugin, even better support (free & pro versions)','Great Checkout Plugin', 'Great Plugin and Support', 'This saved me so much time and effort!','Outstanding – Plugin and support'];
		var headingContainer = document.getElementsByClassName('th-review-heading');
		var review_content = ['I used the free version of this plugin for a while until I needed some of the pro features. It was great as a free plugin and even better as a paid/pro version. On top of that, the support for the pro version is out-of-this-world good! Anuram on the support team went above and beyond. I heartily recommend upgrading to the pro version if it has features you’d like to use, as it is very well worth the price paid!',
			'This full-featured plugin is easy to use and did exactly what I needed.I invested in the Pro version for even more features.',
			'I’ve been using Checkout Field Editor Pro for years and have always been impressed with their support when I had questions or issues.',
			'The free version does everything I need, but I paid for the premium version to support the developers, just because I am so grateful and relieved to find this plugin that actually does what I need. I had been trying to make changes by manipulating CSS, and by spending hours of research to find snippets to add to my functions.php file, and it was so difficult to maintain. This plugin Simply works.',
			'Really amazing plugin for a start. Then I bought the PRO version and it was even better. But the best part is I contacted support about something I wanted to do and got told that it was not possible. BUT about 10 days later, out of the blue, their support responded to my original ticket with custom code that they had created specifically to solve my problem!!! Wow, awesome support guys. Quick and “above & beyond”.',
		];
		var contentContainer = document.getElementsByClassName('th-review-content');
		var review_author = ['Eric Kuznacic','kenttubman','WP-77','Eilonwy926','doughoseck'];
		var authorContainer = document.getElementsByClassName('th-review-user-name');
		if(n > review_heading.length - 1){
			count = 0;
		}
		headingContainer[0].innerHTML =  review_heading[count];
		contentContainer[0].innerHTML = review_content[count];
		authorContainer[0].innerHTML = review_author[count];
	}
	function showSlides(n){
		var i;		  
	  	var dots = document.getElementsByClassName("th-review-nav-btn");
	  	
	  	if(dots.length>0){
	  		if (n > dots.length) {
	  			slideIndex = 1
	  		}
			for (i = 0; i < dots.length; i++) {
				dots[i].className = dots[i].className.replace(" active", "");
			}
	  		dots[slideIndex-1].className += " active";	
	  	}
	}

	function pause() {
	  	clearInterval(myTimer);
	  	clearInterval(contentTimer)
	};

	function resume(){
		clearInterval(myTimer);
	  	clearInterval(contentTimer)
	  	myTimer = setInterval(function(){plusSlides(slideIndex)}, 3000);
	  	contentTimer = setInterval(function(){ contentchange(count)},3000);
	};

	function PopUpClose(elm){
		var addressValue = elm.getAttribute("href");
		window.open(addressValue);
		var link = document.getElementById("thwcfd-discount-close-btn");
		link.click();
		
	}

	return {
		thwcfdwidgetPopUp : widgetPopUp,
		thwcfdwidgetClose : widgetClose,
		selectAllFields : select_all_fields,
		removeSelectedFields : remove_selected_fields,
		enableDisableSelectedFields : enable_disable_selected_fields,
		thwcfdAccordionexpand : accordionexpand,
		currentSlide : currentSlide,
		thwcfdPopUpClose : PopUpClose,
   	};
}(window.jQuery, window, document));	

function thwcfdSelectAllCheckoutFields(elm){
	thwcfd_settings.selectAllFields(elm);
}

function thwcfdRemoveSelectedFields(){
	thwcfd_settings.removeSelectedFields();
}

function thwcfdEnableSelectedFields(){
	thwcfd_settings.enableDisableSelectedFields(1);
}

function thwcfdDisableSelectedFields(){
	thwcfd_settings.enableDisableSelectedFields(0);
}

function thwcfdwidgetPopUp(){
	thwcfd_settings.thwcfdwidgetPopUp();
}

function thwcfdwidgetClose() {
	thwcfd_settings.thwcfdwidgetClose();
}

function thwcfdwidgetPopUp(){
	thwcfd_settings.thwcfdwidgetPopUp();
}

function thwcfdAccordionexpand(elm){
	thwcfd_settings.thwcfdAccordionexpand(elm);
}
function currentSlide(elm) {
	thwcfd_settings.currentSlide(elm);
}

function thwcfdPopUpClose(elm){
	thwcfd_settings.thwcfdPopUpClose(elm);
}