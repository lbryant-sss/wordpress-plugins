	$.fbuilder.typeList.push(
		{
			id:"fMedia",
			name:"Media",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fMedia' ]=function(){};
	$.extend(
		true,
		$.fbuilder.controls[ 'fMedia' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			ftype:"fMedia",
            sMediaType:"image", // image, audio, video
			initAdv : function(){
					delete this.advanced.css.label;
					delete this.advanced.css.help;
					delete this.advanced.css.input;
					if ( ! ( 'caption' in this.advanced.css ) ) this.advanced.css.caption = {label: 'Fig caption',rules:{}};
					if ( ! ( 'image' in this.advanced.css ) ) this.advanced.css.image = {label: 'Image',rules:{}};
					if ( ! ( 'audio' in this.advanced.css ) ) this.advanced.css.audio = {label: 'Audio',rules:{}};
					if ( ! ( 'video' in this.advanced.css ) ) this.advanced.css.video = {label: 'Video',rules:{}};
				},
			init : function()
				{
					this.data = {
							image:{
								sWidth:"",
								sHeight:"",
								sSrc:"",
								sAlt:"",
								sLink:"",
								sTarget:"",
								sFigcaption: "",
								sLazy:0
							},
							audio:{
								sWidth:"",
								sSrc:"",
								sSrcAlt:"",
								sControls:1,
								sLoop:0,
								sAutoplay:0,
								sMuted:0,
								sPreload:"auto",
								sFallback:"",
								sFigcaption:"",
								sHideDownload:0
							},
							video:{
								sWidth:"",
								sHeight:"",
								sSrc:"",
								sSrcAlt:"",
								sPoster:"",
								sControls:1,
								sLoop:0,
								sAutoplay:0,
								sMuted:0,
								sPreload:"auto",
								sFallback:"",
								sFigcaption:"",
								sHideDownload:0
							}
						};
				},
            _display_image: function()
                {
                    var d = this.data.image,
                        esc = cff_esc_attr,
                        a = [],
                        l = [],
                        r = '<div class="cff-empty-image"></div>';

                    if( String( d.sSrc ).trim() ) {
						a.push( 'src="'+esc( d.sSrc )+'"' );
						if( String( d.sWidth ).trim() ) a.push( 'width="'+esc( d.sWidth )+'"' );
						if( String( d.sHeight ).trim() ) a.push( 'height="'+esc( d.sHeight )+'"' );
						if( String( d.sAlt ).trim() ) a.push( 'alt="'+esc( d.sAlt )+'"' );
						else a.push( 'alt="image"' );
						r = '<img '+a.join( ' ' )+' />';
					}

                    return r;
                },
            _display_audio_video: function( d, isV )
                {
                    var esc = cff_esc_attr,
                        a = [],
						s = [],
                        t = ( isV) ? 'video' : 'audio';

                    if( String( d.sWidth ).trim() ) s.push( 'width:'+esc( d.sWidth )+';' );
                    if( isV && String( d.sHeight ).trim() ) s.push( 'height:'+esc( d.sHeight )+';' );
                    if( isV && String( d.sPoster ).trim() ) a.push( 'poster="'+esc( d.sPoster )+'"' );
                    /* if( String( d.sSrc ).trim() ) a.push( 'src="'+esc( d.sSrc )+'"' ); */
                    /* if( d.sAutoplay ) a.push( 'autoplay' ); */
                    if( d.sControls ) a.push( 'controls' );
                    if( d.sLoop ) a.push( 'loop' );
                    if( d.sMuted ) a.push( 'muted' );
					if( d.sHideDownload ) a.push( 'controlsList="nodownload"' );
                    a.push( 'preload="none"' );

                    return '<'+t+' '+a.join(' ')+' style="'+s.join(' ')+'">'+((String(d.sSrcAlt).trim()) ? '<source src="'+esc(d.sSrcAlt)+'" />' : '')+'<p>'+cff_sanitize(d.sFallback)+'</p></'+t+'>';
                },
            _display_audio: function()
                {
                    return this._display_audio_video( this.data.audio, false );
                },
            _display_video: function()
                {
                    return this._display_audio_video( this.data.video, true );
                },
            display:function( css_class )
				{
					css_class = css_class || '';
					return '<div class="fields fmark '+this.name+' '+this.ftype+' '+css_class+'" id="field'+this.form_identifier+'-'+this.index+'" title="'+this.controlLabel('Media')+'"><div class="arrow ui-icon ui-icon-grip-dotted-vertical "></div>'+this.iconsContainer()+this.showColumnIcon()+'<div>'+this[ '_display_'+this.sMediaType ]()+'</div><span>'+this.data[ this.sMediaType ][ 'sFigcaption' ]+'</span><div class="clearer"></div></div>';
				},
			editItemEvents:function()
				{
                    var me = this;
                    $("#sMediaBtn").on("click", function(e)
						{
							me._loadMedia( 'sSrc', me.sMediaType );
						});
                    $("#sMediaAltBtn").on("click", function(e)
						{
							me._loadMedia( 'sSrcAlt', me.sMediaType );
						});
                    $("#sPosterBtn").on("click", function(e)
						{
							me._loadMedia( 'sPoster', 'image' );
						});
                    $("[name='sMediaType']").on("click", {obj: this}, function(e)
                        {
                            e.data.obj[ this.name ] = $(this).val();
                            $.fbuilder.editItem( e.data.obj.index );
                            $.fbuilder.reloadItems({'field':e.data.obj});
                        });
					$("#sControls,#sLoop,#sAutoplay,#sMuted,#sHideDownload,#sLazy").on("click", {obj: this}, function(e)
                        {
                            e.data.obj[ 'data' ][ e.data.obj[ 'sMediaType' ] ][ this.id ] = ( this.checked) ? 1 : 0;
                            $.fbuilder.reloadItems({'field':e.data.obj});
                        });
					$("#sWidth,#sHeight,#sSrc,#sSrcAlt,#sPoster,#sAlt,#sLink,#sTarget,#sFallback,#sFigcaption,#sPreload").on("change keyup", {obj: this}, function(e)
						{
							e.data.obj[ 'data' ][ e.data.obj[ 'sMediaType' ] ][ this.id ] = $(this).val();
                            $.fbuilder.reloadItems({'field':e.data.obj});
						});

					$.fbuilder.controls[ 'ffields' ].prototype.editItemEvents.call(this);
				},
            showSpecialDataInstance: function()
                {
                    return this._showMediaList()+this._showSettingsBox();
                },
		    _showMediaList: function()
                {
                    var l = [ 'image', 'audio', 'video' ],
                        r  = "", v;

                    for( var i = 0, h = l.length; i < h; i++ )
                    {
                        v = cff_esc_attr(l[i]);
                        r += '<label class="column width30"><input type="radio" name="sMediaType" value="'+v+'" '+((this.sMediaType == v) ? 'CHECKED' : '')+' >'+v+'</label>';
                    }
                    return '<label>Select media type</label>'+r+'<div class="clearer"></div>';
                },
            _loadMedia: function( f, type )
                {
                    var src_field = $( '#'+f ),
                        media = wp.media({
                            title: 'Select Source',
                            button: {
                                text: 'Select Source'
                            },
                            multiple: false
                    }).on('select',
                        (function( field, type ){
                            return function() {
                                var regExp = new RegExp( type, 'i'),
                                    attachment = media.state().get('selection').first().toJSON();
                                if( !regExp.test( attachment.mime ) )
                                {
                                    alert( 'Invalid mime type' );
                                    return;
                                }
                                field.val( attachment.url ).trigger('change');
                            };
                        })( src_field, type )
                    ).open();
                    return false;
                },
            _showSettingsBox: function()
                {
                    var d = this.data[ this.sMediaType ];
                    return '<div class="groupBox" style="margin-top:10px;">'+this[ '_showSettings_'+this.sMediaType ]( d )+'</div>';
                },
            _showSettings_image: function( d )
                {
                    var esc = cff_esc_attr;
					return '<label for="sWidth">Width</label><input type="text" class="large" name="sWidth" id="sWidth" value="'+esc(d.sWidth)+'">'+
                    '<label for="sHeight">Height</label><input type="text" class="large" name="sHeight" id="sHeight" value="'+esc(d.sHeight)+'">'+
                    '<label for="sSrc">Source</label><div><input type="text" style="width:70%;" name="sSrc" id="sSrc" value="'+esc(d.sSrc)+'"><input id="sMediaBtn" type="button" class="button-secondary" value="Browse" style="width:28%;" /></div>'+
                    '<label for="sAlt">Alternate</label><input type="text" class="large" name="sAlt" id="sAlt" value="'+esc(d.sAlt)+'">'+
                    '<label for="sFigcaption">Figcaption</label><input type="text" class="large" name="sFigcaption" id="sFigcaption" value="'+esc(d.sFigcaption)+'">'+
                    '<label for="sLink">Link</label><input type="text" class="large" name="sLink" id="sLink" value="'+esc(d.sLink)+'">'+
                    '<label for="sTarget">Target</label><input type="text" class="large" name="sTarget" id="sTarget" value="'+esc(d.sTarget)+'" /><span>_blank, _new, _parent, _self, _top</span>'+
                    '<label for="sLazy"><input type="checkbox" name="sLazy" id="sLazy" '+(d.sLazy ? 'CHECKED' : '')+' /> Lazy Load</label>';
                },
            _showSettings_audio_video: function( d, isV )
                {
                    var esc = cff_esc_attr,
                    r  = '<label for="sWidth">Width</label><input type="text" class="large" name="sWidth" id="sWidth" value="'+esc(d.sWidth)+'">';

                    if( isV )
                    r += '<label for="sHeight">Height</label><input type="text" class="large" name="sHeight" id="sHeight" value="'+esc(d.sHeight)+'">';

                    r += '<label for="sSrc">Source</label><div><input type="text" style="width:70%;" name="sSrc" id="sSrc" value="'+esc(d.sSrc)+'"><input id="sMediaBtn" type="button" class="button-secondary" value="Browse" style="width:28%;" /></div>';

                    if( isV )
                    r += '<label for="sPoster">Poster</label><div><input type="text" style="width:70%;" name="sPoster" id="sPoster" value="'+esc(d.sPoster)+'"><input id="sPosterBtn" type="button" class="button-secondary" value="Browse" style="width:28%;" /></div>';

                    r += '<label for="sSrcAlt">Alternative Source</label><div><input type="text" style="width:70%;" name="sSrcAlt" id="sSrcAlt" value="'+esc(d.sSrcAlt)+'"><input id="sMediaAltBtn" type="button" class="button-secondary" value="Browse" style="width:28%;" /></div>'+
                    '<label><input type="checkbox" name="sControls" id="sControls" value="1" '+((d.sControls) ? 'CHECKED' : '')+'> Controls</label>'+
                    '<label><input type="checkbox" name="sLoop" id="sLoop" value="1" '+((d.sLoop) ? 'CHECKED' : '')+'> Loop</label>'+
                    '<label><input type="checkbox" name="sAutoplay" id="sAutoplay" value="1" '+((d.sAutoplay) ? 'CHECKED' : '')+'> Autoplay</label>'+
                    '<label><input type="checkbox" name="sMuted" id="sMuted" value="1" '+((d.sMuted) ? 'CHECKED' : '')+'> Muted</label>'+
					'<label><input type="checkbox" name="sHideDownload" id="sHideDownload" value="1" '+((d.sHideDownload) ? 'CHECKED' : '')+'> Hide the download file control</label>';
                    var s = '',
                        l = [ 'none', 'auto', 'metadata' ];
                    for( var i = 0; i < 3; i++ )
                    {
                        s += '<option value="'+esc(l[i])+'" '+((l[i] == d.sPreload) ? 'SELECTED' : '')+'>'+esc(l[i])+'</option>';
                    }
                    r += '<label for="sPreload">Preload</label><select class="large" name="sPreload" id="sPreload">'+s+'</select>'+
                    '<label for="sFallback">Fallback Text</label><input type="text" class="large" name="sFallback" id="sFallback" value="'+esc(d.sFallback)+'">'+
                    '<label for="sFigcaption">Figcaption</label><input type="text" class="large" name="sFigcaption" id="sFigcaption" value="'+esc(d.sFigcaption)+'">';
                    return r;
                },
            _showSettings_audio: function( d )
                {
                    return this._showSettings_audio_video( d, false );
                },
            _showSettings_video: function( d )
                {
                    return this._showSettings_audio_video( d, true );
                },
            showTitle: function(){ return ''; },
            showShortLabel: function(){ return ''; },
			showUserhelp: function(){ return ''; }
	});