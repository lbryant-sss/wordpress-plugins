// Import block modules.
import { __ } from '@wordpress/i18n';

// Import global parts.
import './parts-editor/global-editor';
import '../../inc/admin/global-settings/assets/js/global-settings';

// Set google fonts array.
import fonts from '../../inc/admin/fonts/assets/json/google-fonts-dropdown.json';

if ( fonts && ! qiBlocksEditor.vars.googleFontsDisabled ) {
	qiBlocksEditor.vars.fontOptions.push( { label: __( 'Google Fonts', 'qi-blocks' ), value: 'Google' } );

	fonts.map(
		font =>
		{
			qiBlocksEditor.vars.fontOptions.push( { label: font.family, value: font.family } );
		}
	);
}
