import clsx from 'clsx';
import { twMerge } from 'tailwind-merge';
import { __ } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import { ReactComponent as BlackDiamond } from '../../images/black-diamond.svg';
import { STEPS } from '../steps/util';

export const whiteLabelEnabled = () => {
	return astraSitesVars?.isWhiteLabeled ? true : false;
};

export const getWhileLabelName = () => {
	return astraSitesVars?.whiteLabelName;
};

export const getWhiteLabelAuthorUrl = () => {
	return astraSitesVars?.whiteLabelUrl;
};

export const isPro = () => {
	return astraSitesVars?.isPro;
};

export const getProUrl = () => {
	return astraSitesVars?.getProURL;
};

export const sendPostMessage = ( data ) => {
	// console.log( 'sendPostMessage' );
	const frame = document.getElementById( 'astra-starter-templates-preview' );
	if ( ! frame ) {
		return;
	}

	frame.contentWindow.postMessage(
		{
			call: 'starterTemplatePreviewDispatch',
			value: data,
		},
		'*'
	);
};

export const getDataUri = ( url, callback ) => {
	const image = new Image();

	image.onload = function () {
		const canvas = document.createElement( 'canvas' );
		canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
		canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size

		canvas.getContext( '2d' ).drawImage( this, 0, 0 );

		// ... or get as Data URI
		callback( canvas.toDataURL( 'image/png' ) );
	};

	image.src = url;
};

export const storeCurrentState = ( currentState ) => {
	try {
		localStorage.setItem(
			'starter-templates-onboarding',
			JSON.stringify( currentState )
		);
	} catch ( err ) {
		return false;
	}
};

export const getStoredState = () => {
	return JSON.parse( localStorage.getItem( 'starter-templates-onboarding' ) );
};

export const getDefaultColorPalette = ( demo ) => {
	let defaultPaletteValues = [];

	if ( demo && 'astra-site-customizer-data' in demo ) {
		const customizerData = demo[ 'astra-site-customizer-data' ] || '';
		if ( customizerData ) {
			const globalPalette =
				customizerData[ 'astra-settings' ][ 'global-color-palette' ]
					?.palette || [];

			if ( globalPalette ) {
				defaultPaletteValues = [
					{
						slug: 'default',
						title: __( 'Original', 'astra-sites' ),
						colors: globalPalette,
					},
				];
			}
		}
	}
	return defaultPaletteValues;
};

export const getDefaultTypography = ( demo ) => {
	let defaultTypography = {};

	if ( demo && 'astra-site-customizer-data' in demo ) {
		const customizerData = demo[ 'astra-site-customizer-data' ] || '';
		if ( customizerData ) {
			const customizerSettings = customizerData[ 'astra-settings' ] || [];
			const headingFontFamily =
				customizerSettings[ 'headings-font-family' ];

			defaultTypography = {
				default: true,
				'body-font-family': customizerSettings[ 'body-font-family' ],
				'body-font-variant': customizerSettings[ 'body-font-variant' ],
				'body-font-weight': customizerSettings[ 'body-font-weight' ],
				'font-size-body': customizerSettings[ 'font-size-body' ],
				'body-line-height': customizerSettings[ 'body-line-height' ],
				'headings-font-family': headingFontFamily,
				'headings-font-weight':
					customizerSettings[ 'headings-font-weight' ],
				'headings-line-height':
					customizerSettings[ 'headings-line-height' ],
				'headings-font-variant':
					customizerSettings[ 'headings-font-variant' ],
			};
		}
	}
	return defaultTypography;
};

export const getHeadingFonts = ( demo ) => {
	const headingFonts = {};

	const headingsTags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];

	if ( demo && 'astra-site-customizer-data' in demo ) {
		const customizerData = demo[ 'astra-site-customizer-data' ] || '';
		if ( customizerData ) {
			const customizerSettings = customizerData[ 'astra-settings' ] || [];

			headingsTags.forEach( ( tag ) => {
				headingFonts[ 'font-family-' + tag ] =
					customizerSettings[ `font-family-${ tag }` ];
				headingFonts[ 'font-weight-' + tag ] =
					customizerSettings[ `font-weight-${ tag }` ];
				headingFonts[ 'text-transform-' + tag ] =
					customizerSettings[ `text-transform-${ tag }` ];
				headingFonts[ 'line-height-' + tag ] =
					customizerSettings[ `line-height-${ tag }` ];
			} );
		}
	}
	return headingFonts;
};

export const getColorScheme = ( demo ) => {
	let colorScheme = 'light';

	if (
		demo &&
		'astra-site-color-scheme' in demo &&
		'' !== demo[ 'astra-site-color-scheme' ]
	) {
		colorScheme = demo[ 'astra-site-color-scheme' ];
	}
	return colorScheme;
};

export const getAllSites = () => {
	return astraSitesVars?.all_sites;
};

export const getSupportLink = ( templateId, subject ) => {
	return `${ starterTemplates.supportLink }&template-id=${ templateId }&subject=${ subject }`;
};

export const getGridItem = ( site ) => {
	let imageUrl = site[ 'thumbnail-image-url' ] || '';
	if ( '' === imageUrl && false === whiteLabelEnabled() ) {
		if ( astraSitesVars?.default_page_builder === 'fse' ) {
			imageUrl = `${ starterTemplates.imageDir }spectra-placeholder.png`;
		} else {
			imageUrl = `${ starterTemplates.imageDir }placeholder.png`;
		}
	}

	let badge = '';
	let type = 'free';
	if ( site[ 'astra-sites-type' ] === 'signature' ) {
		badge = (
			<>
				<BlackDiamond /> { __( 'Signature', 'astra-sites' ) }
			</>
		);
		type = 'signature';
	} else if ( site[ 'astra-sites-type' ] !== 'free' ) {
		badge = <>{ __( 'Premium', 'astra-sites' ) }</>;
		type = 'premium';
	}

	return {
		id: site.id,
		image: imageUrl,
		title: decodeEntities( site.title ),
		type,
		badge,
		...site,
	};
};

export const getTotalTime = ( value ) => {
	const hours = Math.floor( value / 60 / 60 );
	const minutes = Math.floor( value / 60 ) - hours * 60;
	const seconds = value % 60;

	if ( minutes ) {
		return minutes + '.' + seconds;
	}

	return '0.' + seconds;
};

export const saveGutenbergAsDefaultBuilder = ( pageBuilder = 'gutenberg' ) => {
	const content = new FormData();
	content.append( 'action', 'astra-sites-change-page-builder' );
	content.append( '_ajax_nonce', astraSitesVars?._ajax_nonce );
	content.append( 'page_builder', pageBuilder );

	fetch( ajaxurl, {
		method: 'post',
		body: content,
	} );
};

export const classNames = ( ...classes ) => twMerge( clsx( classes ) );

/**
 *
 * @param {string} key
 * @param {any}    value
 * @return {any} value
 */
export const setLocalStorageItem = ( key, value ) => {
	try {
		if ( typeof window === 'undefined' ) {
			return;
		}
		localStorage.setItem( key, JSON.stringify( value ) );
	} catch ( error ) {
		// Handle error (e.g., localStorage is full, etc.)
	}
};

/**
 * Get localStorage item
 *
 * @param {string} key
 * @return {any} value
 */
export const removeLocalStorageItem = ( key ) => {
	try {
		if ( typeof window === 'undefined' ) {
			return;
		}
		localStorage.removeItem( key );
	} catch ( error ) {
		console.error( 'Error while removing localStorage:', error );
	}
};

export const debounce = ( func, wait, immediate ) => {
	let timeout;
	return ( ...args ) => {
		const later = () => {
			timeout = null;
			if ( ! immediate ) {
				func( ...args );
			}
		};
		const callNow = immediate && ! timeout;
		clearTimeout( timeout );
		timeout = setTimeout( later, wait );
		if ( callNow ) {
			func( ...args );
		}
	};
};

/**
 * Get step index from step name.
 *
 * @param {string} name
 * @return {number} index
 */
export const getStepIndex = ( name = '' ) => {
	return STEPS.findIndex( ( step ) => step.name === name );
};
