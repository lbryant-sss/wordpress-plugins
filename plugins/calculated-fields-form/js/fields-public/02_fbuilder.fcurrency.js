	$.fbuilder.controls[ 'fcurrency' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fcurrency' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Currency",
			ftype:"fcurrency",
			predefined:"",
			predefinedClick:false,
			required:false,
			readonly:false,
            numberpad:false,
			spinner:false,
			size:"small",
			currencySymbol:"$",
			currencyText:"USD",
			thousandSeparator:",",
			centSeparator:".",
			noCents: false,
			min:"",
			max:"",
			step:1,
			formatDynamically:false,
			twoDecimals:false,
			set_step:function(v, rmv)
				{
					var e = $('[id="'+this.name+'"]');
					if(rmv) e.removeAttr('step');
					else {
						var vb = e.val();
						e.removeAttr('value');
						if(!isNaN(v*1)) e.attr('step', Math.abs(v*1 ? v : 1));
						e.val(vb);
					}
                    if(!e.hasClass('cpefb_error')) e.removeClass('required');
					e.valid();
                    if(this.required) e.addClass('required');
				},
			set_min:function(v, rmv)
				{
					var e = $('[id="'+this.name+'"]');
					if(rmv) e.removeAttr('min');
					else if(!isNaN(v*1)) e.attr('min', v);
					if(!e.hasClass('cpefb_error')) e.removeClass('required');
					e.valid();
                    if(this.required) e.addClass('required');
				},
			set_max:function(v, rmv)
				{
					var e = $('[id="'+this.name+'"]');
					if(rmv) e.removeAttr('max');
					else if(!isNaN(v*1)) e.attr('max', v);
					if(!e.hasClass('cpefb_error')) e.removeClass('required');
					e.valid();
                    if(this.required) e.addClass('required');
				},
			getFormattedValue:function(value)
				{
					if(value == '') return value;
					if(this.formatDynamically)
					{
						var me = this,
							ts = me.thousandSeparator,
							tse = ts.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'),
							cs = ((cs = String(me.centSeparator).trim()) !== '') ? cs : '.',
							v = $.fbuilder.parseVal(
								( ts !== '' ? String(value).replace(new RegExp(tse + '(?!\\d{1,2}\\D*$)', "gi"), '') : value ),
								ts, cs
							),
							parts = [],
							counter = 0,
							str = '',
							sign = '';

						if(!isNaN(v))
						{
							if(v < 0) sign = '-';
							v = ABS(v);
							if(this.twoDecimals) v = v.toFixed(2);
							parts = v.toString().split(".");

							for(var i = parts[0].length-1; i >= 0; i--)
							{
								counter++;
								str = parts[0][i]+str;
								if(counter%3 == 0 && i != 0) str = ts+str;
							}
							parts[0] = str;

							if(parts[1])
							{
								if(parts[1].length == 1) parts[1] += '0';
							}
							else parts[ 1 ] = '00';

							return me.currencySymbol+sign+((me.noCents)?parts[0]:parts.join(cs))+me.currencyText;
						}
					}
					return value;
				},
			init:function()
				{
					if(!/^\s*$/.test(this.min)) this._setHndl('min');
					if(!/^\s*$/.test(this.max)) this._setHndl('max');
					if(!/^\s*$/.test(this.step)) this._setHndl('step');
					else this.step = 1;
				},
			show:function()
				{
					this.predefined = this._getAttr('predefined', true);
					return '<div class="fields '+cff_esc_attr(this.csslayout)+' '+(this.spinner ? 'cff-spinner ' : '')+this.name+' cff-currency-field" id="field'+this.form_identifier+'-'+this.index+'"  style="'+cff_esc_attr(this.getCSSComponent('container'))+'"><label for="'+this.name+'" style="'+cff_esc_attr(this.getCSSComponent('label'))+'">'+cff_sanitize(this.title, true)+''+((this.required)?"<span class='r'>*</span>":"")+'</label><div class="dfield">'+
					(this.spinner ? '<div class="cff-spinner-components-container '+cff_esc_attr(this.size)+'"><button type="button" class="cff-spinner-down" style="'+cff_esc_attr(this.getCSSComponent('spinner_left'))+'">-</button>' : '')+
					'<input '+((this.numberpad) ? 'inputmode="decimal"' : '')+' aria-label="'+cff_esc_attr(this.title)+'" '+((this.readonly)? 'readonly' : '')+' id="'+this.name+'" name="'+this.name+'" class="field cffcurrency '+(this.spinner ? 'large' : cff_esc_attr(this.size))+((this.required)?" required":"")+'" type="text" value="'+cff_esc_attr(this.getFormattedValue(this.predefined))+'" '+((!/^\s*$/.test(this.min)) ? 'min="'+cff_esc_attr($.fbuilder.parseVal(this._getAttr('min'), this.thousandSeparator, this.centSeparator))+'" ' : '')+((!/^\s*$/.test(this.max)) ? ' max="'+cff_esc_attr($.fbuilder.parseVal(this._getAttr('max'), this.thousandSeparator, this.centSeparator))+'" ' : '')+((!/^\s*$/.test(this.step)) ? ' step="'+cff_esc_attr(this._getAttr('step'))+'" ' : '')+' style="'+cff_esc_attr(this.getCSSComponent('input'))+'" />'+
					(this.spinner ? '<button type="button" class="cff-spinner-up" style="'+cff_esc_attr(this.getCSSComponent('spinner_right'))+'">+</button></div>' : '')+
					'<span class="uh" style="'+cff_esc_attr(this.getCSSComponent('help'))+'">'+cff_sanitize(this.userhelp, true)+'</span></div><div class="clearer"></div></div>';
				},
			after_show:function()
				{
					var me = this;
					if(me.formatDynamically) {
						$(document).on('change', '[name="'+me.name+'"]', function(){
							this.value = me.getFormattedValue(this.value);
						});
					}
					$('#'+me.name).rules('add', {'step':false});
				},
			val:function(raw, no_quotes)
				{
					raw = raw || false;
                    no_quotes = no_quotes || false;
					var e = $('[id="'+this.name+'"]:not(.ignore)');
					if(e.length)
					{
						var v = String(e.val()).trim();
						v = this.getFormattedValue(v);
						if(raw) return ($.fbuilder.isNumeric(v)) ? v : $.fbuilder.parseValStr(v, raw, no_quotes);

						v = v.replace(new RegExp($.fbuilder[ 'escapeSymbol' ](this.currencySymbol), 'g'), '')
						     .replace(new RegExp($.fbuilder[ 'escapeSymbol' ](this.currencyText), 'g'), '');

						return $.fbuilder.parseVal(v, this.thousandSeparator, this.centSeparator, no_quotes);
					}
					return 0;
				}
		}
	);