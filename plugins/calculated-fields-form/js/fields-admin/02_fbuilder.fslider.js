		$.fbuilder.typeList.push(
			{
				id:"fslider",
				name:"Slider",
				control_category:1
			}
		);
        $.fbuilder.controls[ 'fslider' ] = function(){};
		$.extend(
			true,
			$.fbuilder.controls[ 'fslider' ].prototype,
			$.fbuilder.controls[ 'ffields' ].prototype,
			{
				title:"Slider",
				ftype:"fslider",
				exclude:false,
				readonly:false,
				predefined:"",
				predefinedMin:"",
				predefinedMax:"",
				predefinedClick:false,
				size:"small",
				thousandSeparator:",",
				centSeparator:".",
				typeValues:false,
				min:0,
				max:100,
				step:1,
				marks:false,
				divisions:5,
				range:false,
				logarithmic:false,
				caption:"{0}",
				minCaption:"",
				maxCaption:"",
				initAdv: function() {
					if ( ! ( 'slider' in this.advanced.css ) ) this.advanced.css.slider = {label: 'Slider',rules:{}};
					if ( ! ( 'slider_handle' in this.advanced.css ) ) this.advanced.css.slider_handle = {label: 'Slider handle',rules:{}};
					if ( ! ( 'slider_range' in this.advanced.css ) ) this.advanced.css.slider_range = {label: 'Slider range',rules:{}};
					if ( ! ( 'caption' in this.advanced.css ) ) this.advanced.css.caption = {label: 'Caption',rules:{}};
					if ( ! ( 'caption_left' in this.advanced.css ) ) this.advanced.css.caption_left= {label: 'Left caption',rules:{}};
					if ( ! ( 'caption_right' in this.advanced.css ) ) this.advanced.css.caption_right= {label: 'Right caption',rules:{}};
				},
				display:function( css_class )
				{
					css_class = css_class || '';
					let id = 'field'+this.form_identifier+'-'+this.index;
					return '<div class="fields '+this.name+' '+this.ftype+' '+css_class+'" id="'+id+'" title="'+this.controlLabel('Slider')+'"><div class="arrow ui-icon ui-icon-grip-dotted-vertical "></div>'+this.iconsContainer()+'<label for="'+id+'-box">'+cff_sanitize(this.title, true)+'</label><div class="dfield">'+this.showColumnIcon()+'<input id="'+id+'-box" class="field disabled '+this.size+'" type="text" value="'+( ( !this.range ) ? cff_esc_attr( this.predefined ) : cff_esc_attr( '['+this.predefinedMin+','+this.predefinedMax+']' ) )+'"/><span class="uh">'+cff_sanitize(this.userhelp, true)+'</span></div><div class="clearer"></div></div>';
				},
				editItemEvents:function()
				{
					var evt = [
						{s:"#sTypeValues",e:"click", l:"typeValues", f:function(el){return el.is(':checked');}},
						{s:"#sMin",e:"change keyup", l:"min", x:1},
						{s:"#sMax",e:"change keyup", l:"max", x:1},
						{s:"#sStep",e:"change keyup", l:"step", x:1},
						{s:"#sDivisions",e:"change keyup", l:"divisions", x:1},
						{s:"#sRange",e:"change", l:"range", f:function(el){
							var v = el.is(':checked');
							$( 'div.range'    )[ ( v ) ? 'show' : 'hide' ]();
							$( 'div.no-range' )[ ( v ) ? 'hide' : 'show' ]();
							if(v) $('#sLogarithmic').prop('checked', false).trigger('change');
							return v;
						}},
						{s:"#sMarks",e:"change", l:"marks", f:function(el){return el.is(':checked');}},
						{s:"#sLogarithmic",e:"change", l:"logarithmic", f:function(el){
							var v = el.is(':checked');
							$( 'div.marks' )[ ( v ) ? 'hide' : 'show' ]();
							if(v) {
								$('#sMarks').prop('checked', false).trigger('change');
								$('#sRange').prop('checked', false).trigger('change');
							}
							return v;
						}},
						{s:"#sCaption",e:"change keyup", l:"caption"},
						{s:"#sMinCaption",e:"change keyup", l:"minCaption"},
						{s:"#sMaxCaption",e:"change keyup", l:"maxCaption"},
						{s:"#sPredefinedMin",e:"change keyup", l:"predefinedMin", x:1},
						{s:"#sPredefinedMax",e:"change keyup", l:"predefinedMax", x:2},
						{s:"#sThousandSeparator",e:"change keyup", l:"thousandSeparator", x:1},
						{s:"#sCentSeparator",e:"change keyup", l:"centSeparator", x:1}
					];
					$.fbuilder.controls[ 'ffields' ].prototype.editItemEvents.call(this, evt);
				},
				showRequired: function(){ return '<label><input type="checkbox" name="sTypeValues" id="sTypeValues" '+( (this.typeValues) ? 'CHECKED' : '')+'> Allow to type the values</label>'; },
				showPredefined: function()
				{
					return '<div class="no-range" style="display:'+( ( this.range ) ? 'none' : 'block')+';"><label for="sPredefined">Predefined Value</label><input type="text" class="large" name="sPredefined" id="sPredefined" value="'+cff_esc_attr( this.predefined )+'"></div>'+
					'<div class="range" style="display:'+( ( this.range ) ? 'block' : 'none')+';"><div class="column width50"><label for="sPredefinedMin">Predefined Min</label><input type="text" name="sPredefinedMin" id="sPredefinedMin" value="'+cff_esc_attr( this.predefinedMin )+'" class="large"></div><div class="column width50"><label for="sPredefinedMax">Predefined Max</label><input type="text" name="sPredefinedMax" id="sPredefinedMax" value="'+cff_esc_attr( this.predefinedMax )+'" class="large"></div></div>'+
					'<i>It is possible to use another field in the form as predefined value. Ex: fieldname1</i>'+
					'<div class="clearer"></div>';
				},
				showRangeIntance: function()
				{
					return $.fbuilder.showSettings.showExclude(this.exclude) +
						$.fbuilder.showSettings.showReadonly(this.readonly) +
						'<div><div class="column width30"><label for="sMin">Min</label><input type="text" name="sMin" id="sMin" value="'+cff_esc_attr(this.min)+'" placeholder="0 by default" class="large"></div><div class="column width30"><label for="sMax">Max</label><input type="text" name="sMax" id="sMax" value="'+cff_esc_attr(this.max)+'" placeholder="100 by default" class="large"></div><div class="column width30"><label for="sStep">Step</label><input type="text" name="sStep" id="sStep" value="'+cff_esc_attr(this.step)+'" placeholder="1 by default" class="large"></div><div class="clearer"></div></div><div style="margin-bottom:10px;"><i>It is possible to associate other fields in the form with the attributes "min", "max" and "step". Ex: fieldname1</i></div><label><input type="checkbox" name="sRange" id="sRange" '+( ( this.range ) ? 'CHECKED' : '' )+' /> Range slider </label><label class="no-range"><input type="checkbox" name="sLogarithmic" id="sLogarithmic" '+( ( this.logarithmic ) ? 'CHECKED' : '' )+' /> Logarithmic slider </label><div class="marks" style="display:'+(this.logarithmic? 'none' : 'block')+';"><label><input type="checkbox" name="sMarks" id="sMarks" '+( ( this.marks ) ? 'CHECKED' : '' )+' /> Show marks </label><div class="width30"><label for="sDivisions">Divisions</label><input type="text" name="sDivisions" id="sDivisions" value="'+cff_esc_attr(this.divisions)+'" class="large"></div></div><div><label for="sCaption">Field Caption</label><input class="large" type="text" name="sCaption" id="sCaption" value="'+cff_esc_attr( this.caption )+'"></div><div><label for="sMinCaption">Min Corner Caption</label><input class="large" type="text" name="sMinCaption" id="sMinCaption" value="'+cff_esc_attr( this.minCaption )+'"></div><div><label for="sMaxCaption">Max Corner Caption</label><input class="large" type="text" name="sMaxCaption" id="sMaxCaption" value="'+cff_esc_attr( this.maxCaption )+'"></div><div><label for="sThousandSeparator">Symbol for grouping thousands in the field\'s caption(Ex: 3,000,000)</label><input type="text" name="sThousandSeparator" id="sThousandSeparator" class="large" value="'+cff_esc_attr( this.thousandSeparator )+'" /></div><div><label for="sCentSeparator">Decimals separator symbol (Ex: 25.20)</label><input type="text" name="sCentSeparator" id="sCentSeparator" class="large" value="'+cff_esc_attr( this.centSeparator )+'" /></div>';
				}
		});