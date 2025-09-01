jQuery(document).ready(function($){

	var AnimateCard = {

		type: function(){
			return SideCart.sy('scbp-card-anim-type');
		},

		duration: function(){
			return SideCart.sy('scbp-card-anim-time');
		},

		init: function(){

			var onEvent = SideCart.sy('scbp-card-visible') === 'back_hover' ? 'mouseenter' : 'click';

			$('.xoo-wsc-has-back').off();
		
			$('.xoo-wsc-has-back').on( onEvent, this.animate );
			$('.xoo-wsc-has-back').on( 'mouseleave', this.reverseAnimate );

			if( SideCart.sy('scb-playout') === 'cards' ){
				this.initMasonryLayout();
			}

		},
		animate: function(e){

			if( e.target.classList.contains('xoo-wsc-smr-del') ) return;

			var $img = $(this).find('.xoo-wsc-img-col');

			if( !$img.hasClass('xoo-wsc-caniming') ){
				e.preventDefault();
			}
			else{
				return;
			}

			$img.attr('data-exclasses', $img.attr('class') );

			$img.removeClass()
			$img.addClass($img.attr('data-exclasses'));

			$img.addClass( 'xoo-wsc-caniming' + ' ' + AnimateCard.type() );

		},
		reverseAnimate: function(){

			var $img = $(this).find('.xoo-wsc-img-col');

			if( !$img.hasClass( 'xoo-wsc-caniming' ) ) return;

			$img.addClass( AnimateCard.type() + 'Return' );

			AnimateCard.clear = setTimeout(function(){
				$img.removeClass().addClass( $img.attr('data-exclasses') );
			}, AnimateCard.duration() * 1000 );

		},

		initMasonryLayout(){
			$('.xoo-wsc-products.xoo-wsc-pattern-card').masonry({
				// options
				itemSelector: '.xoo-wsc-product-cont',
				columnWidth: '.xoo-wsc-product-cont', /* Each column takes 50% */
				percentPosition: true
			});
		}
	}
	

	var Customizer = {

		$form: '',
		$styleTag: $('.xoo-as-preview-style'),
		previewTemplate: '',
		formValues: {},
		getPreviewCSS: function() {},
		getPreviewHTMLData: function() {},
		pageLoading: true,

		init: function(){
			this.initColorPicker();
			this.initSortable();
			this.initTemplates();
			this.events();
			this.build();
		},

		events: function(){
			$( Customizer.$form ).on('change', this.onFormChange );
		},

		initTemplates: function(){
			this.previewTemplate = wp.template('xoo-as-preview');
		},

		initColorPicker: function(){
			$('.xoo-as-color-input').wpColorPicker({
				change: function(event, ui){
					$(event.target).val(ui.color.toString()).trigger('change')
				}
			});
		},

		initSortable: function(){
			$('.xoo-as-sortable-list').sortable({
				update: function(){
					Customizer.build();
				}
			});
		},

		onFormChange: function(e){
			Customizer.build();
		},


		setFormValues: function(){
			//var values 		= this.$form.serializeArray();
			//this.formValues = this.objectifyForm(values);
			this.formValues = this.$form.serializeJSON();
		},

		build: function(){
			if( this.pageLoading ) return; // prevent multiple building event on page load due to 'change' event
			this.setFormValues();
			this.buildHTML();
			this.buildCSS();
			AnimateCard.init();
		},


		buildCSS: function(){

			var css = '';

			$.each( Customizer.getPreviewCSS(), function( selector, properties ){
				css += selector+'{';
				$.each( properties, function(property, value){
					css += property+': '+value+';';
				} );
				css += '}';
			} );

			Customizer.$styleTag.html('<style>'+css+'</style>')	
		},

		buildHTML: function(){
			$('.xoo-as-preview').html(Customizer.previewTemplate(Customizer.getPreviewHTMLData()));

		},

		objectifyForm(inp){

			var rObject = {};

			for (var i = 0; i < inp.length; i++){
				if(inp[i]['name'].substr(inp[i]['name'].length - 2) == "[]"){
					var tmp = inp[i]['name'].substr(0, inp[i]['name'].length-2);
					if(Array.isArray(rObject[tmp])){
						rObject[tmp].push(inp[i]['value']);
					} else{
						rObject[tmp] = [];
						rObject[tmp].push(inp[i]['value']);
					}
				} else{
					rObject[inp[i]['name']] = inp[i]['value'];
				}
			}
			return rObject;
		}
	}


	var SideCart = {

		settingsInPreview: ['xoo-wsc-gl-options[scb-show][]', 'xoo-wsc-gl-options[sch-show][]', 'xoo-wsc-gl-options[scf-show][]', 'xoo-wsc-sy-options[scf-button-pos][]'],
		previewSettingsRecorded: false,

		init: function(){
			this.initCustomizer();
			this.events();
			this.toggle('show');
		},


		initCustomizer: function(){
			Customizer.$form 		=  $('form.xoo-as-form');
			Customizer.getPreviewCSS = this.getPreviewCSS;
			Customizer.getPreviewHTMLData = this.getPreviewHTMLData;
			Customizer.init()
		},

		events: function(){
			$(document.body).on( 'click', '.xoo-wsc-basket', this.toggle );
			$(document.body).on( 'click', '.xoo-wsch-close', this.toggle );
		},

		sy: function( key, unit = '' ){
			var value = this.option( 'xoo-wsc-sy-options', key );
			return unit ? value + unit : value;
		},

		gl: function( key ){
			return this.option( 'xoo-wsc-gl-options', key );
		},

		option: function( option, key ){
			if( !this.previewSettingsRecorded ){
				this.settingsInPreview.push( option+'['+key+']' )
			}
			return Customizer.formValues[option][key];
		},


		getPreviewCSS: function(){
			return SideCart.setPreviewCSS();
		},

		setPreviewCSS: function(){

			var basketPosition = this.sy('sck-count-pos');

			var basket = {
				[this.sy('sck-position')]: 	this.sy('sck-offset','px'),	
				'right':  					this.sy('sck-hoffset', 'px'),									
				'background-color': 		this.sy('sck-basket-bg'),
				'color': 					this.sy('sck-basket-color'),
				'box-shadow': 				this.sy('sck-basket-sh'),
				'border-radius': 			this.sy('sck-shape') === 'round' ? '50%' : '14px',
				'width': 					this.sy('sck-bk-size','px'),
				'height':  					this.sy('sck-bk-size','px'),
			};

			var basketActive = {
				'right': this.sy('scm-width', 'px')
			}

			var basketActiveRTL = {
				'left': this.sy('scm-width', 'px'),
				'right': 'auto'
			}

			var basketIcon = {
				'font-size': this.sy('sck-size','px')
			}

			var basketCount = {
				'display': 	 		this.sy('sck-show-count') === 'yes' ? 'block' : 'none',
				'background-color': this.sy('sck-count-bg'),
				'color': 			this.sy('sck-count-color'),
				[basketPosition === 'top_right' || basketPosition === 'top_left' ? 'top' : 'bottom']: '-9px',
				[basketPosition === 'top_right' || basketPosition === 'bottom_right' ? 'right' : 'left']: '-8px',
				'height': 			this.sy('sck-count-size','px'),
				'line-height': 		this.sy('sck-count-size','px'),
				'width': 			this.sy('sck-count-size','px')
			}

			var container = {
				'max-width': 				this.sy('scm-width','px'),
				'right': 					'-'+this.sy('scm-width','px'),
				'font-family': 				this.sy('scm-font'),
				[this.sy('sck-position')]: 	'0'
			}

			var headerCloseIcon = {
				'font-size': 					this.sy('sch-close-fsize','px'),
				[this.sy('sch-close-align')]: 	'10px'
			}

			var headerTop = {
				'justify-content': this.sy('sch-head-align')
			}

			if( this.sy('scm-height') === 'full' ){
				container['top'] = '0';
				container['bottom'] = '0';
			}
			else{
				container['max-height'] = '100vh';
			}


			var header = {
				'background-color': this.sy('sch-bgcolor'),
				'color': 			this.sy('sch-txtcolor'),
				'border-bottom': 	this.sy('sch-border')
			}

			var headerTxt = {
				'font-size': this.sy('sch-head-fsize','px')
			}


			var body = {
				'background-color': this.sy('scb-bgcolor'),
			}

			var bodyText = {
				'font-size': 	this.sy('scb-fsize','px'),
				'color': 		this.sy('scb-txtcolor')
			}

			var footerBtn = {
				'padding': 				this.sy('scf-btn-padding'),
				'background-color': 	this.sy('scf-btn-bgcolor'),
				'color': 				this.sy('scf-btn-txtcolor'),
				'border': 				this.sy('scf-btn-border'),
			}

			var footerBtnHover = {
				'background-color': 	this.sy('scf-btnhv-bgcolor'),
				'color': 				this.sy('scf-btnhv-txtcolor'),
				'border': 				this.sy('scf-btnhv-border'),
			}

			var footer = {
				'padding': 				this.sy('scf-padding'),
				'background-color': 	this.sy('scf-bgcolor'),
				'color': 				this.sy('scf-txtcolor'),
				'box-shadow': 			this.sy('scf-shadow'),
			}

			var footerFSize = {
				'font-size': 			this.sy('scf-fsize', 'px'),
			}

			var product =  {
				'padding': 				this.sy('scbp-padding'),
				'background-color': 	this.sy('scbp-bgcolor'),
				'margin':  				this.sy('scbp-margin'),
				'border-radius': 		this.sy('scbp-bradius', 'px'),
				'box-shadow': 			this.sy('scbp-shadow'),
			}

			var productImgCol = {
				'width': this.sy('scbp-imgw', '%')
			}


			if( this.sy('scf-stick') !== 'yes' ){
				footer['flex-grow'] 	= 1;
				body['flex-grow'] 		= 0;
				body['overflow'] 		= 'unset';
				container['overflow'] 	= 'auto';
			}



			var cardStyle = {
				container: {
					'padding': 	this.sy('scbp-card-padding'),
				},
				image: {
					'max-width': this.sy('scbp-card-imgw', '%'),
					'height': this.sy('scbp-card-imgh', 'px')
				},
				productCont: {
					'width': 100/parseInt( this.sy('scbp-card-count') ) + '%',
				},
				product: {
					'border': this.sy('scbp-card-border'),
					'box-shadow': this.sy('scbp-card-shadow'),
				},
				front: {
					'background-color': this.sy('scbp-card-front-color')
				},
				cardAndFront: {
					'border-bottom-left-radius': this.sy('scbp-card-radius-btm', 'px'),
					'border-bottom-right-radius': this.sy('scbp-card-radius-btm', 'px')
				},
				imgAndImgCol: {
					'border-top-left-radius': this.sy('scbp-card-radius-top', 'px'),
					'border-top-right-radius': this.sy('scbp-card-radius-top', 'px'),
				},
				cardText: {
					'font-size': 	this.sy('scb-fsize','px'),
				},
				cardFront: {
					'color': 		this.sy('scbp-card-txtcolor'),
				},
				cardBack: {
					'color': 		this.sy('scbp-card-backtxt-color'),
				},
				back: {
					'background-color': this.sy('scbp-card-back-color')
				},
				animation: {
					'animation-duration': this.sy('scbp-card-anim-time', 's')
				}

			}

			if( parseInt(this.sy('scbp-card-imgw')) < 100 ){
				cardStyle.imageCol = {
					'background-color': this.sy('scbp-card-img-color')
				}
			}


			var cardSelectors = {
				'.xoo-wsc-product-cont': cardStyle.container,
				'.xoo-wsc-pattern-card .xoo-wsc-img-col img': cardStyle.image,
				'.xoo-wsc-pattern-card .xoo-wsc-product-cont': cardStyle.productCont,
				'.xoo-wsc-pattern-card .xoo-wsc-product': cardStyle.product,
				'.xoo-wsc-pattern-card .xoo-wsc-img-col': cardStyle.imageCol,
				'.xoo-wsc-sm-front': cardStyle.front,
				'.xoo-wsc-pattern-card, .xoo-wsc-sm-front': cardStyle.cardAndFront,
				'.xoo-wsc-pattern-card, .xoo-wsc-img-col img, .xoo-wsc-img-col, .xoo-wsc-sm-back-cont': cardStyle.imgAndImgCol,
				'.xoo-wsc-sm-back': cardStyle.back,
				'.xoo-wsc-pattern-card, .xoo-wsc-pattern-card a, .xoo-wsc-pattern-card .amount': cardStyle.cardText,
				'.xoo-wsc-sm-front, .xoo-wsc-sm-front a, .xoo-wsc-sm-front .amount': cardStyle.cardFront,
				'.xoo-wsc-sm-back, .xoo-wsc-sm-back a, .xoo-wsc-sm-back .amount': cardStyle.cardBack,
				'.magictime': cardStyle.animation
			}

			if( xoo_wsc_admin_params.isMobile === 'yes' && this.sy('scb-playout') === 'cards' && this.sy('scbp-card-visible') === 'back_hover' ){
				cardSelectors['.xoo-wsc-img-col a'] = {
					'pointer-events': 'none'
				}
			}

			var selectors = {
				'.xoo-wsc-basket': basket,
				'.xoo-wsc-cart-active .xoo-wsc-basket': basketActive,
				'body.rtl.xoo-wsc-cart-active .xoo-wsc-basket': basketActiveRTL,
				'.xoo-wsc-bki': basketIcon,
				'.xoo-wsc-items-count': basketCount,
				'.xoo-wsc-container,.xoo-wsc-slider': container,
				'.xoo-wsc-header': header,
				'.xoo-wsch-text': headerTxt,
				'span.xoo-wsch-close': headerCloseIcon,
				'.xoo-wsch-top': headerTop,
				'.xoo-wsc-body': body,
				'.xoo-wsc-products:not(.xoo-wsc-pattern-card), .xoo-wsc-products:not(.xoo-wsc-pattern-card) span.amount, .xoo-wsc-products:not(.xoo-wsc-pattern-card) a': bodyText,
				'.xoo-wsc-ft-buttons-cont a.xoo-wsc-ft-btn, .xoo-wsc-container .xoo-wsc-btn': footerBtn,
				'.xoo-wsc-ft-buttons-cont a.xoo-wsc-ft-btn:hover, .xoo-wsc-container .xoo-wsc-btn:hover': footerBtnHover,
				'.xoo-wsc-footer': footer,
				'.xoo-wsc-footer, .xoo-wsc-footer a, .xoo-wsc-footer .amount': footerFSize,
				'.xoo-wsc-products:not(.xoo-wsc-pattern-card) .xoo-wsc-product': product,
				'.xoo-wsc-products:not(.xoo-wsc-pattern-card) .xoo-wsc-img-col': productImgCol,
				'.xoo-wsch-items-count, .xoo-wsch-save-count': {
					'background-color': this.sy('sck-count-bg'),
					'color': 			this.sy('sck-count-color')
				},
				'.xoo-wsch-new span.xoo-wsch-close': {
					'font-size': this.sy('sch-close-fsize','px')
				},
				'span.xoo-wsch-bki': {
					'font-size': this.sy('sch-basket-fsize','px')
				},
				'span.xoo-wsch-items-count': {
					'width': this.sy('sch-count-size','px'),
					'height': this.sy('sch-count-size','px'),
					'line-height': this.sy('sch-count-size','px')
				}
			}

			var gridCols = 'auto';

			if( this.sy('scf-btns-row') === 'three' ){
				gridCols = '1fr 1fr 1fr';
			}
			else if( this.sy('scf-btns-row') === 'two_one' ){
				gridCols = '2fr 2fr';
				selectors['a.xoo-wsc-ft-btn:nth-child(3)'] = {
					'grid-column': '1/-1'
				}
			}
			else if( this.sy('scf-btns-row') === 'one_two' ){
				gridCols = '2fr 2fr';
				selectors['a.xoo-wsc-ft-btn:nth-child(1)'] = {
					'grid-column': '1/-1'
				}
			}
			

			selectors['.xoo-wsc-ft-buttons-cont'] = {
				'grid-template-columns': gridCols
			}


			if( this.sy('scbp-display') === 'stretched' ){
				selectors['.xoo-wsc-sm-info'] = {
					'flex-grow': '1',
    				'align-self': 'stretch'
				}
				selectors['.xoo-wsc-sm-left'] = {
					'justify-content': 'space-evenly'
				}
			}
			else{
				selectors['.xoo-wsc-sum-col'] = {
					'justify-content': this.sy('scbp-display')
				}
			}


			var sideCartwidth = parseInt(this.sy('scm-width')) + 100;

			selectors['.xoo-wsc-cart-active .xoo-settings-container'] = {
				'width': 'calc( 100% - '+sideCartwidth+'px )'
			}


			selectors['.xoo-wsc-product dl.variation'] = {
				'display': this.sy('scbp-var-format') === 'one_line' ? 'flex' : 'block'
			}


			selectors = $.extend({}, selectors, cardSelectors);

			if( !this.previewSettingsRecorded ){
				$.each( this.settingsInPreview, function( index, name ){
					var $input = $('[name="'+name+'"]').closest('.xoo-as-setting');
					if( !$input.length ) return true;
					$input.addClass( 'xoo-as-has-preview' );
				} );
				this.previewSettingsRecorded = true;
			}
		
			return selectors;

		},

		getPreviewHTMLData: function(){
			return SideCart.setPreviewHTMLData();
		},

		setPreviewHTMLData: function(){
			var data = {
				basket: {
					show: 		this.sy('sck-enable') !== 'always_hide',
					icon: 		this.sy('sck-basket-icon'),
					countType: 	this.gl('m-bk-count')
				},
				header: {
					showBasketIcon: 			this.gl('sch-show').includes('basket'),
					showCloseIcon: 				this.gl('sch-show').includes('close'),
					closeIcon: 					this.sy('sch-close-icon'),
					heading: 					this.gl('sct-cart-heading'),
					layout: 					this.sy('sch-layout'),
					oldLayout: 					xoo_wsc_admin_params.hasOldheader && this.sy('sch-new-layout') !== "yes"
				},
				product: {
					layout: 				this.sy('scb-playout'),
					updateQty: 				false,
					showPImage: 			this.gl('scb-show').includes('product_image'),
					showPname: 				this.gl('scb-show').includes('product_name'),
					showPdel: 				this.gl('scb-show').includes('product_del'),
					showPtotal: 			this.gl('scb-show').includes('product_total'),
					showPmeta: 				this.gl('scb-show').includes('product_meta'),
					showPprice: 			this.gl('scb-show').includes('product_price'),
					showPqty: 				this.gl('scb-show').includes('product_qty'),
					showPriceSavings: 		this.gl('scb-show').includes('product_price_save'),
					showTotalSavings: 		this.gl('scb-show').includes('product_total_save'),
					savingsUnit:  			this.gl('scb-prod-savings'),
					qtyPriceDisplay: 		this.gl('scbp-qpdisplay'),
					deletePosition: 		this.sy('scbp-delpos'),
					deleteText: 			this.gl('sct-delete'),
					deleteType: 			this.sy('scbp-deltype'),
					deleteIcon:  			this.sy('scb-del-icon'),
					priceType:  			this.gl('scb-prod-price')

				},
				card: {
					backShow: {
						name: 					this.sy('scbp-card-back').includes('name'),
						price: 					this.sy('scbp-card-back').includes('price'),
						qty: 					this.sy('scbp-card-back').includes('qty'),
						total: 					this.sy('scbp-card-back').includes('total'),
						meta: 					this.sy('scbp-card-back').includes('meta'),
						link: 					this.sy('scbp-card-back').includes('link'),
						price_save: 			this.sy('scbp-card-back').includes('price_save'),
						total_save: 			this.sy('scbp-card-back').includes('total_save'),
					},
					visibility: this.sy('scbp-card-visible'),
					hasBack: this.sy('scbp-card-visible') !== 'all_on_front' && (this.sy('scbp-card-back').length > 1)
				},
				footer: {
					totals: {
						savings: 	this.gl('scf-show').includes('savings'),
						subtotal: 	this.gl('scf-show').includes('subtotal'),
					},
					subtotalLabel: 		this.gl('sct-subtotal'),
					savingLabel: 		this.gl('sct-savings'),
					footerTxt: 			this.gl('sct-footer'),
					checkoutTotal: 		this.gl('scf-chkbtntotal-en'),
					buttonsPosition: 	this.sy('scf-button-pos'),
					buttonsText: 		{
						cart: this.gl('sct-ft-cartbtn'),
						checkout: this.gl('sct-ft-chkbtn'),
						continue: this.gl('sct-ft-contbtn')
					}
				}
			}

			data.product.oneLiner = data.product.qtyPriceDisplay === 'one_liner' && data.product.showPqty && data.product.showPprice && data.product.showPtotal;

			return data;
		},

		toggle: function( type ){

			var $activeEls 	= $('body'),
				activeClass = 'xoo-wsc-cart-active';

			if( type === 'show' ){
				$activeEls.addClass(activeClass);
			}
			else if( type === 'hide' ){
				$activeEls.removeClass(activeClass);
			}
			else{
				$activeEls.toggleClass(activeClass);
			}

		}
	}

	SideCart.init();



	$('select[name="xoo-wsc-gl-options[m-ajax-atc]"]').on( 'change', function(){

		var $catSetting = $(this).closest('.xoo-as-setting').next();

		if( $(this).val() === 'cat_yes' || $(this).val() === 'cat_no' ){
			$catSetting.show();
		}
		else{
			$catSetting.hide();
		}
	} ).trigger('change');


	//Install login popup plugin
	$('.xoo-wsc-el-install').click(function(e){

		e.preventDefault();
		var $cont = $(this).closest('.xoo-wsc-el-links');
		$cont.html( 'Installing.. Please wait..' );

		$.ajax({
			url: xoo_wsc_admin_params.adminurl,
			type: 'POST',
			data: {
				action: 'xoo_wsc_el_install',
				xoo_wsc_nonce: xoo_wsc_admin_params.nonce
			},
			success: function( response ){

				if( response.firsttime_download ){
					$.post(xoo_wsc_admin_params.adminurl, {
						'action': 'xoo_wsc_el_request_just_to_init_save_settings'
					},function(result){
						if( response.notice ){
							$cont.html(response.notice)
						}
					})
				}
				else{
					if( response.notice ){
						$cont.html(response.notice)
					}
				}
				
			}
		})
	})



	//Hide/show product row and layout settings section
	$('input[name="xoo-wsc-sy-options[scb-playout]"]').on('change', function(){

		var $rowSection 	= $('.xoo-ass-style-scb_product'),
			$cardSection 	= $('.xoo-ass-style-scb_productcard'),
			$cardlink 		= $('a[href="#style_scb_productcard"]'),
			$rowlink 		= $('a[href="#style_scb_product"]');

		if( $(this).val() === 'rows' && $(this).prop('checked') ){
			$rowSection.show();
			$cardSection.hide();
			$cardlink.hide();
			$rowlink.show();
		}
		else{
			$rowSection.hide();
			$cardSection.show();
			$rowlink.hide();
			$cardlink.show();
		}
	});

	$('input[name="xoo-wsc-sy-options[scb-playout]"]:checked').trigger('change');


	//Hide product elements for card layout depending on the items enabled/disabled
	$('input[name="xoo-wsc-gl-options[scb-show][]"]').on( 'change', function(){

		var val 		= $(this).val(),
			isChecked 	= $(this).prop('checked');

		var $relatedEl = $('input[name="xoo-wsc-sy-options[scbp-card-back][]"][value="'+val.replace("product_", "")+'"]');
		if( $relatedEl ){
			if( isChecked ){
				$relatedEl.closest('label').show();
			}
			else{
				$relatedEl.closest('label').hide();
			}
		}


		var oneLinerEligibile = ['product_price','product_qty','product_total'];

		if( oneLinerEligibile.includes( val ) ){

			var $oneLinerSetting = $('select[name="xoo-wsc-gl-options[scbp-qpdisplay]"], select[name="xoo-wsc-sy-options[scbp-qpdisplay]"]').closest('.xoo-as-setting');

			if( !isChecked ){
				$oneLinerSetting.hide();
			}
			else{
				var allValues = $("input[name='xoo-wsc-gl-options[scb-show][]']").map(function() {
				    return $(this).val();
				}).get();

				var failed = false;

				$.each( oneLinerEligibile, function( index, eligval ){
					if( !allValues.includes(eligval) ){
						failed = true;
						return false;
					}
				} )

				if( !failed ){
					$oneLinerSetting.show();
				}

			}
		}

		
	} ).trigger('change');


	$('select[name="xoo-wsc-gl-options[scbp-qpdisplay]"]').on( 'change', function(){
		$('select[name="xoo-wsc-sy-options[scbp-qpdisplay]"]').val($(this).val());
	} );

	$('select[name="xoo-wsc-sy-options[scbp-qpdisplay]"]').on( 'change', function(){
		$('select[name="xoo-wsc-gl-options[scbp-qpdisplay]"]').val($(this).val());
	} );


	$('select[name="xoo-wsc-gl-options[scbp-qpdisplay]"], select[name="xoo-wsc-sy-options[scbp-qpdisplay]"]').on('change', function(){

		var $toggle = $('input[name="xoo-wsc-sy-options[scbp-card-back][]"][value="total"], input[name="xoo-wsc-sy-options[scbp-card-back][]"][value="price"]').closest('label');

		if( $(this).val() === 'one_liner' ){
			$toggle.hide();
		}
		else{
			$toggle.show();
		}
	}).trigger('change');


	$('select[name="xoo-wsc-sy-options[scbp-card-visible]"]').on('change', function(){

		var $toggle = $('input[name="xoo-wsc-sy-options[scbp-card-back][]"]').closest('.xoo-as-setting');

		if( $(this).val() === 'all_on_front' ){
			$toggle.hide();
		}
		else{
			$toggle.show();
		}
	})

	


	$('input[name="xoo-wsc-sy-options[scm-width]"]').on('change', function(){
		if( !$('body').hasClass('folded') && $('.xoo-settings-container').width() < 900 ){
			var $collapse = $('#collapse-button');
			if( $collapse.length ){
				$collapse.trigger('click');
			}
		}
		$(window).trigger('resize');
	}).trigger('change');



	Customizer.pageLoading = false;
	Customizer.build();


	$('.xoo-wsc-admin-popup img.xoo-as-patimg').on('click', function(){

		var $cont 		= $(this).closest('.xoo-as-setting'),
			fieldID 	= $cont.data('field_id'),
			key			= $(this).data('key'),
			$formField 	= $('.xoo-as-setting[data-field_id="'+fieldID+'"]').not($cont);


		if( $formField.length ){
			$formField.find( 'img.xoo-as-patimg[data-key="'+key+'"]' ).trigger('click');
		}


	})


	$('.xoo-wsc-admin-popup select[name="xoo-wsc-gl-options[scbp-qpdisplay]"]').on('change', function(){
		$('select[name="xoo-wsc-gl-options[scbp-qpdisplay]"]').not(this).val($(this).val()).trigger('change');
	});



	 $('button.xoo-wsc-adpopup-go').on('click', function(){

	 	$('body').removeClass('xoo-wsc-adpopup-active');

		$('.xoo-wsc-admin-popup').remove();


		$('html, body').animate({ scrollTop: 0 }, 0);
	});
	

	$('ul[id^="xooWscH-"]').sortable({
      connectWith: ".xooWscHconnectedSortable",
       axis: "x",
       update: function(event, ui) {

       	if( ui.sender ){

	       	var newName = ui.item.find('input').attr('name').replace(
	       		'['+ui.sender.data('name')+']',
	       		'['+ui.item.closest('ul').data('name')+']'
	       	);

	       	ui.item.find('input').attr('name',newName);
	    }

	    ui.item.find('input').trigger('change');

       }
    }).disableSelection();


	$('body').on('click', '.xoo-wsc-acc-head', function(){

		var $container 	= $(this).closest('.xoo-wsc-accordion'),
			$content 	= $container.children('.xoo-wsc-acc-cont');

		$container.toggleClass('xoo-wsc-acc-active');
	})



    var Rewards = {

		templateBar: '',
		templateCheckpoint: '',
		barInputNames: {},
		$cont: $('.xoo-wsc-rewards-cont'),

		init: function(){
			this.initTemplates();
			this.events(); 
			this.createSettingsOnLoad();
		},

		barNumbering: function(){
			$.each( $('.xoo-wsc-bar'), function( index, el ){
				var $el 		= $(el),
					$titleInput = $el.find('.xoo-wsc-bar-title-input');
				$titleInput.val( $titleInput.val().replace( '[%^]','#'+ (index + 1) ) ).trigger('input');
			} )
		},

		createSettingsOnLoad(){
			var bars = xoo_wsc_admin_params.bars;
			if( !bars ) return;
			$.each( bars, function( index, barData ){

				$bar 	= $(Rewards.templateBar(barData.settings));

				$('.xoo-wsc-bars').append($bar);

				if( barData.checkpoints ){
					$.each( barData.checkpoints, function( index, checkpointData ){
						var $checkpoint = $(Rewards.templateCheckpoint(checkpointData));
						$bar.find('.xoo-wsc-bar-checkpoints').append($checkpoint);
					} );
				}

				$bar.find('select.xoo-wsc-bar-barValue').trigger('change');
				
			} )

			Rewards.onBarAdd();
			Rewards.initProductSearchBox();
		},


		onBarAdd: function(){
			Rewards.initSortable();
			Rewards.initColorPicker();
			Rewards.barNumbering();
		},


		addBar: function(){

			$('.xoo-wsc-bar').removeClass('xoo-wsc-acc-active');

			var $bar = $(Rewards.templateBar(xoo_wsc_admin_params.barDefaults.settings));
	
			$('.xoo-wsc-bars').append($bar);

			$bar.addClass('xoo-wsc-acc-active');

			Rewards.onBarAdd();

			
		},

		events: function(){
			$('button.xoo-wsc-add-bar').on('click', Rewards.addBar );
			$('body').on('click', 'button.xoo-wsc-bar-add-chkpoint', Rewards.addBarCheckpoint );
			$('body').on('click', '.xoo-wsc-bar-delete', Rewards.deleteBar);
			$('body').on('click', '.xoo-wsc-checkpoint-delete', Rewards.deleteCheckpoint);
			$('body').on('click', '.xoo-wsc-bar-chkpoint > .xoo-wsc-acc-head', Rewards.onCheckPointToggle );
			$('body').on( 'input', '.xoo-wsc-chkpoint-title-input', Rewards.onCheckPointTitleChange );
			$('body').on( 'input', '.xoo-wsc-bar-title-input', Rewards.onBarTitleChange );
			$('body').on( 'change', 'select.xoo-wsc-bar-barValue', Rewards.onBarValueChange );

			$('button.xoo-as-form-save').on( 'click', Rewards.beforeSettingsSave );
			$(document).ajaxComplete(Rewards.onSettingsSave);
			
		},


		onBarValueChange: function(){
			var $bar 						= $(this).closest('.xoo-wsc-bar'),
				$checkPointSelector 		= $bar.find('.xoo-wsc-checkpoint-selector select'),
				$checkPointSelectorShipping = $checkPointSelector.find('option[value="freeshipping"]'),
				$freeShippingCheckpoint 	= $bar.find('.xoo-wsc-bar-chkpoint[data-type="freeshipping"]');


			if( $(this).val() === 'quantity' ){
				$checkPointSelectorShipping.add($freeShippingCheckpoint).hide();
				$checkPointSelector.val('discount').trigger('change');
			}
			else{
				$checkPointSelectorShipping.add($freeShippingCheckpoint).show();
			}
		},

		onCheckPointTitleChange: function(){
			$(this).closest('.xoo-wsc-bar-chkpoint').find('.xoo-wsc-chkpoint-title').text($(this).val());
		},

		onBarTitleChange: function(){
			var $bar = $(this).closest('.xoo-wsc-bar');
			$bar.find('.xoo-wsc-bar-title').text($(this).val());
		},

		onCheckPointToggle: function(){
			var $checkpoint = $(this).closest('.xoo-wsc-bar-chkpoint');
			Rewards.initIconPicker( $checkpoint );
			Rewards.productSearchFillDefaultValues( $checkpoint );
		},


		productSearchFillDefaultValues( $checkpoint ){

			$.each( $checkpoint.find('.xoo-wsc-bar-prodsearch'), function( index, el ){

				var $searchCont 	= $(el),
					$defaultCont 	= $searchCont.find('.xoo-wsc-barpsearch-defaults');

				if( !$defaultCont.length ) return true;

				var $defaultInputs  = $defaultCont.find('input'),
					$searchSelect 	= $searchCont.find('select.wc-product-search'),
					defaultValues 	= [] ;

				if( !$defaultInputs.length ) return true;

				let productIDs = $defaultInputs.map(function(){
				    return $(this).val();
				}).get();

				$searchCont.addClass('xoo-as-processing');

				$.ajax({
					url: xoo_wsc_admin_params.adminurl,
					type: 'POST',
					data: {
						action: 'xoo_wsc_product_search_fill_defaults',
						product_ids: productIDs,
						xoo_wsc_nonce: xoo_wsc_admin_params.nonce
					},
					success: function( response ){
						$searchSelect.html(response);
						$defaultCont.remove();
						$searchCont.removeClass('xoo-as-processing');
					}
				})

			} );


		},

		onSettingsSave: function(event,xhr,options){

			if( $(event.target.activeElement).hasClass('xoo-as-form-save') ){

				$.each( Rewards.barInputNames, function( newName, oldName ){
					$('[name="'+newName+'"]').attr('name', oldName);
				})

				Rewards.barInputNames = {};

				Rewards.$cont.removeClass('xoo-as-processing');

			}
		},

		beforeSettingsSave: function(){

			var $cont 	= Rewards.$cont,
				id 		= '[%$]';

			$cont.addClass('xoo-as-processing');

			$('.xoo-wsc-bar').each( function(index, el){
				
				$(el).find('[name*="' + id + '"]').each( function(i, inel){

					var name 	= $(inel).attr('name'),
						newName = name.replace( '[%$]', '['+index+']' );

					if( name.includes('[%#]') ){
						var checkPointIndex = $(inel).closest('.xoo-wsc-bar-chkpoint').index();
						newName = newName.replace('[%#]', '['+checkPointIndex+']' );
					}

					Rewards.barInputNames[newName] = name;

					$(inel).attr('name' , newName );

					if( name === id+'[id]' ){
						$(inel).val( 'id_'+index );
					}

				} );


			} );
		},


		initColorPicker: function(){
			$('.xoo-wsc-barColorPicker input:not(.wp-color-picker)').wpColorPicker({
				change: function(event, ui){
					$(event.target).val(ui.color.toString()).trigger('change')
				}
			});
		},


		initSortable: function(){
			$('.xoo-wsc-bars').sortable({
				handle: '.xoo-wsc-bar-head'
			});
		},

		initTemplates: function(){
			this.templateBar 		= wp.template('xoo-as-bar');
			this.templateCheckpoint = wp.template('xoo-as-chkpoint');
		},

		

		deleteBar: function(e){
			if( !confirm( 'Are you sure you want to delete this progress bar and all its checkpoints?' ) ){
				e.preventDefault();
				return;
			}
			$(this).closest('.xoo-wsc-bar').remove();
		},

		deleteCheckpoint: function(e){
			$(this).closest('.xoo-wsc-bar-chkpoint').remove();
			e.stopImmediatePropagation();
		},

		addBarCheckpoint: function(){

			var $bar 			= $(this).closest('.xoo-wsc-bar'),
				$type  			= $bar.find('.xoo-wsc-checkpoint-selector select');

			var checkpointData 	= {
				type: $type.val(),
			}

			checkpointData = $.extend( xoo_wsc_admin_params.barDefaults.checkpoints[checkpointData.type], checkpointData );

			$bar.find('.xoo-wsc-bar-chkpoint').removeClass('xoo-wsc-acc-active');

			var $checkpoint = $(Rewards.templateCheckpoint(checkpointData));

			$bar.find('.xoo-wsc-bar-checkpoints').append($checkpoint);

			$checkpoint.addClass('xoo-wsc-acc-active');

			Rewards.initIconPicker( $checkpoint );
			Rewards.initProductSearchBox();


		},

		initProductSearchBox(){
			$( document.body ).trigger( 'wc-enhanced-select-init' );
		},

		initIconPicker( $checkpoint ){

			$checkpoint.find('.xoo-wsc-bar-icon:not(.iconpicker-input)').iconpicker({
				hideOnSelect: true,
			}).on('iconpickerSelected', function(e){
			  $(e.target).next().attr('class',e.iconpickerValue || $(e.target).val() );
			}).trigger('iconpickerSelected');
			
		}

	}

	Rewards.init();

	$(document).on('click', '.iconpicker-item', function(e) {
	    e.preventDefault(); // stops "#" from being written
	});


	var $changeNewHeaderLayoutOption = $('input[name="xoo-wsc-sy-options[sch-new-layout]"]');

	if( $changeNewHeaderLayoutOption.length ){

		var $newLayoutSetting = $('.xoo-as-setting[data-field_id="xoo-wsc-sy-options[sch-layout]"] , .xoo-as-setting[data-field_id="xoo-wsc-sy-options[sch-count-size]"], .xoo-as-setting[data-field_id="xoo-wsc-sy-options[sch-count-size]"]'),
				$prevLayoutSettings = $('.xoo-as-setting[data-field_id="xoo-wsc-sy-options[sch-head-align]"], .xoo-as-setting[data-field_id="xoo-wsc-sy-options[sch-close-align]"]');

		$changeNewHeaderLayoutOption.on( 'change', function(){

			if( $(this).prop('checked') ){
				$newLayoutSetting.show();
				$prevLayoutSettings.hide();
			}
			else{
				$newLayoutSetting.hide();
				$prevLayoutSettings.show();
			}

		} ).trigger('change');

	}

	
	
})