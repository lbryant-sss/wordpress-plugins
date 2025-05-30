<script type="text/html" id="tmpl-fl-builder-settings">
	<form class="fl-builder-settings {{data.className}}" {{{data.attrs}}} data-instance-id="{{data.lightboxId}}" data-form-id="{{data.id}}" data-form-group="{{data.type}}" onsubmit="return false;">
		<div class="fl-lightbox-header-wrap">
			<div class="fl-builder-panel-drag-handle">
				<svg width="4" height="20">
					<use href="#fl-v-panel-drag-handle" />
				</svg>
			</div>
			<div class="fl-lightbox-header">
				<h1>
					{{{data.title}}}
					<# if ( data.settings.node_label && ! FLBuilderConfig.node_labels_disabled ) { #>
					{{{FLBuilderConfig.node_labels_separator}}}{{{data.settings.node_label}}}
					<# } #>
					<# for ( var i = 0; i < data.badges.length; i++ ) { #>
					<span class="fl-builder-badge fl-builder-badge-{{data.badges[ i ]}}">{{data.badges[ i ]}}</span>
					<# } #>
				</h1>
				<div class="fl-lightbox-controls">
					<i class="fl-lightbox-resize-toggle <# var className = FLLightbox.getResizableControlClass(); #>{{className}}"></i>
				</div>
			</div>
			<# if ( data.tabs && Object.keys( data.tabs ).length > 1 ) { #>
			<div class="fl-builder-settings-tabs">
				<# var i = 0; for ( var tabId in data.tabs ) { #>
				<# var tab = data.tabs[ tabId ]; #>
				<a href="#fl-builder-settings-tab-{{tabId}}"<# if ( tabId === data.activeTab ) { #> class="fl-active"<# } #>>{{{tab.title}}}</a>
				<# i++; } #>
				<button class="fl-builder-settings-tabs-more">
					<svg viewBox="0 0 18 4">
						<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<g transform="translate(-520.000000, -108.000000)">
								<path d="M524,110 C524,111.1 523.1,112 522,112 C520.9,112 520,111.1 520,110 C520,108.9 520.9,108 522,108 C523.1,108 524,108.9 524,110 Z M536,108 C534.9,108 534,108.9 534,110 C534,111.1 534.9,112 536,112 C537.1,112 538,111.1 538,110 C538,108.9 537.1,108 536,108 Z M529,108 C527.9,108 527,108.9 527,110 C527,111.1 527.9,112 529,112 C530.1,112 531,111.1 531,110 C531,108.9 530.1,108 529,108 Z"></path>
							</g>
						</g>
					</svg>
				</button>
			</div>
			<div class="fl-builder-settings-tabs-overflow-click-mask"></div>
			<div class="fl-builder-settings-tabs-overflow-menu"></div>
			<# } #>
		</div>

		<div class="fl-lightbox-content-wrap">
			<div class="fl-builder-settings-fields fl-nanoscroller">
				<div class="fl-nanoscroller-content">
					<# if ( data.tabs && Object.keys( data.tabs ).length > 0 ) { #>
						<# var i = 0; for ( var tabId in data.tabs ) { #>
						<# var tab = data.tabs[ tabId ]; #>
						<div id="fl-builder-settings-tab-{{tabId}}" class="fl-builder-settings-tab<# if ( tabId === data.activeTab ) { #> fl-active<# } #>">
							<# if ( ! FL?.Builder?.settingsForms.canDeferTab( tabId ) ) { #>
								<# if ( tab.file ) { #>
									<div class="fl-legacy-settings-tab" data-tab="{{tabId}}"></div>
								<# } else if ( tab.template ) { #>
									<# tab = FLBuilderSettingsForms.renderTabTemplate( tab, data.settings ); #>
									{{{tab}}}
								<# } else { #>

									<# if ( tab.description ) { #>
									<p class="fl-builder-settings-tab-description">{{{tab.description}}}</p>
									<# } #>

									<# for ( var sectionId in tab.sections ) { #>
									<# var section = tab.sections[ sectionId ]; #>
									<#
										var isCollapsed = false;
										if ( typeof section.collapsed !== 'undefined' ) {
											isCollapsed = section.collapsed
										}
										if ( typeof section.title !== 'undefined' && true === FLBuilderConfig.collapseSectionsDefault && section.title && '' !== section.title ) {
											isCollapsed = true;
										}
										var collapsedClass = isCollapsed ? 'fl-builder-settings-section-collapsed' : '';

									#>
									<div id="fl-builder-settings-section-{{sectionId}}" class="fl-builder-settings-section {{collapsedClass}}">

										<# if ( section.file ) { #>
											<div class="fl-legacy-settings-section" data-section="{{sectionId}}" data-tab="{{tabId}}"></div>
										<# } else if ( section.template ) { #>
											<# section = FLBuilderSettingsForms.renderSectionTemplate( section, data.settings ); #>
											{{{section}}}
										<# } else { #>

											<# if ( section.title ) { #>
											<div class="fl-builder-settings-section-header">
												<button class="fl-builder-settings-title">
													<svg width="20" height="20">
														<use href="#fl-builder-forms-down-caret" />
													</svg>
													{{{section.title}}}
												</button>
											</div>
											<# } #>

											<div class="fl-builder-settings-section-content">
												<# if ( section.description ) { #>
												<p class="fl-builder-settings-description">{{{section.description}}}</p>
												<# } #>

												<table class="fl-form-table">
												<#
													var node = { type: data.id };
													var fields = FLBuilderSettingsForms.renderFields( section.fields, data.settings, node, tabId, sectionId );
												#>
												{{{fields}}}
												</table>
											</div>

										<# } #>

									</div>
									<# } #>

								<# } #>
							<# } #>
						</div>
						<# i++; } #>
					<# } #>
				</div>
			</div>
			<div class="fl-lightbox-footer">
				<button class="fl-builder-settings-save fl-builder-button fl-builder-button-large" href="javascript:void(0);" onclick="return false;">{{FLBuilderStrings.save}}</button>
				<# if ( jQuery.inArray( 'save-as', data.buttons ) > -1 ) { #>
				<button class="fl-builder-settings-save-as fl-builder-button fl-builder-button-large" href="javascript:void(0);" onclick="return false;">{{FLBuilderStrings.saveAs}}</button>
				<# } #>
				<# if ( data.reset ) { #>
				<button class="fl-builder-settings-reset fl-builder-button fl-builder-button-large" href="javascript:void(0);" onclick="return false;">{{FLBuilderStrings.reset}}</button>
				<# } #>
				<button class="fl-builder-settings-cancel fl-builder-button fl-builder-button-large" href="javascript:void(0);" onclick="return false;">{{FLBuilderStrings.cancel}}</button>
			</div>
		</div>
		<# var settings = FLBuilder._getSettingsJSONForHTML( data.settings ); #>
		<input class="fl-builder-settings-json" type="hidden" value='{{settings}}' />
	</form>
</script>
