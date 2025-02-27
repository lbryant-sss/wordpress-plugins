	$.fbuilder.typeList.push(
		{
			id:"femail",
			name:"Email",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'femail'] = function(){};
	$.extend(
		true,
		$.fbuilder.controls[ 'femail' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Email",
			ftype:"femail",
            autocomplete:"off",
			predefined:"",
			predefinedClick:false,
			required:false,
			exclude:false,
			readonly:false,
			size:"medium",
			equalTo:"",
			regExp:"",
			regExpMssg:"",
			display:function( css_class )
				{
					css_class = css_class || '';
					let id = 'field'+this.form_identifier+'-'+this.index;
					return '<div class="fields '+this.name+' '+this.ftype+' '+css_class+'" id="'+id+'" title="'+this.controlLabel('Email')+'"><div class="arrow ui-icon ui-icon-grip-dotted-vertical "></div>'+this.iconsContainer()+'<label for="'+id+'-box">'+cff_sanitize(this.title, true)+''+((this.required)?"*":"")+'</label><div class="dfield">'+this.showColumnIcon()+'<input id="'+id+'-box" class="field disabled '+this.size+'" type="text" value="'+cff_esc_attr(this.predefined)+'"/><span class="uh">'+cff_sanitize(this.userhelp, true)+'</span></div><div class="clearer"></div></div>';
				},
			editItemEvents:function()
				{
					var evt = [
							{s:"#sRegExp",e:"change keyup", l:"regExp"},
							{s:"#sRegExpMssg",e:"change keyup", l:"regExpMssg"},
							{s:"#sEqualTo",e:"change", l:"equalTo", x:1}
						],
						items = this.fBuild.getItems();
					$('.equalTo').each(function()
						{
							var str = '<option value="" '+(("" == $(this).attr("dvalue"))?"selected":"")+'></option>';
							for (var i=0;i<items.length;i++)
							{
								if (
									$.inArray(items[i].ftype, ['ftext', 'femail', 'fpassword', 'ftextds', 'femailds']) != -1 &&
									items[i].name != $(this).attr("dname")
								)
								{
									str += '<option value="'+items[i].name+'" '+((items[i].name == $(this).attr("dvalue"))?"selected":"")+'>'+cff_esc_attr(items[i].title)+'</option>';
								}
							}
							$(this).html(str);
						});
					$.fbuilder.controls[ 'ffields' ].prototype.editItemEvents.call(this, evt);
				},
			showSpecialDataInstance: function()
				{
                    function email_validator_link(){
                        return '<a class="button-primary large" href="https://cff-bundles.dwbooster.com/product/email-validator" target="_blank" style="text-align:center;margin-top:10px;">Advanced email validator [+]</a>';
                    }

					var str = '<label for="sRegExp">Validate against a regular expression</label><div style="display:flex;"><input type="text" name="sRegExp" id="sRegExp" value="'+cff_esc_attr(this.regExp)+'" class="large" /><input type="button" onclick="window.open(\'https://cff-bundles.dwbooster.com/product/regexp\');" value="+" title="Resources" class="button-secondary" /></div><label for="sRegExpMssg">Error message when the regular expression fails</label><input type="text" name="sRegExpMssg" id="sRegExpMssg" value="'+cff_esc_attr(this.regExpMssg)+'" class="large" /><div class="cff-email-validator">'+(
                        ('cff-email-validator-checked' in $.fbuilder && !$.fbuilder['cff-email-validator-checked']) ? email_validator_link() : ''
                    )+'</div>';

                    if(!('cff-email-validator-checked' in $.fbuilder))
                    {
                        $.fbuilder['cff-email-validator-checked'] = true;
                        $.ajax('admin.php?page=cff-email-validator-submenu').fail(function(a,b,c){
                            $.fbuilder['cff-email-validator-checked'] = false;
                             $('.cff-email-validator').html(email_validator_link());
                        });
                    }
                    return str;
				}
	});