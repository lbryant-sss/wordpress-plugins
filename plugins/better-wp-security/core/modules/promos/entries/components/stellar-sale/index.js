/**
 * External dependencies
 */
import { ThemeProvider, useTheme } from '@emotion/react';

/**
 * WordPress dependencies
 */
import { useViewportMatch } from '@wordpress/compose';
import { useMemo, createInterpolateElement } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { close as dismissIcon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { Text } from '@ithemes/ui';
import { useLocalStorage } from '@ithemes/security-hocs';
import {
	StyledStellarSale,
	StyledStellarSaleContent,
	StyledStellarSaleHeading,
	StyledStellarSaleDismiss,
	StyledStellarSaleButton,
	StyledStellarSaleGraphic,
	StyledStellarSaleLink,
} from './styles';

// Start on July 29th at midnight.
const start = Date.UTC( 2025, 6, 29, 4, 0, 0 );
// End at midnight ET August 5
const end = Date.UTC( 2025, 7, 6, 4, 0, 0 );
const now = Date.now();

export default function StellarSale( { installType } ) {
	const isSmall = useViewportMatch( 'small' );
	const isWide = useViewportMatch( 'wide' );
	const [ isDismissed, setIsDismiss ] = useLocalStorage(
		'itsecPromoStellarSale25'
	);
	const baseTheme = useTheme();
	const theme = useMemo( () => ( {
		...baseTheme,
		colors: {
			...baseTheme.colors,
			text: {
				...baseTheme.colors.text,
				white: '#F9FAF9',
			},
		},
	} ), [ baseTheme ] );

	if ( start > now || end < now ) {
		return null;
	}

	if ( isDismissed ) {
		return null;
	}

	const subtitle = installType === 'free'
		? __( 'Save 30% on Solid Security Pro.', 'better-wp-security' )
		: __( 'Save 30% on Solid Suite.', 'better-wp-security' );
	const shopNow = installType === 'free'
		? 'https://go.solidwp.com/stellar-sale-2025-plugin-solid-security-basic'
		: 'https://go.solidwp.com/stellar-sale-2025-plugin-solid-security-pro';
	const shopBrands = installType === 'free'
		? 'https://go.solidwp.com/stellar-sale-2025-plugin-solid-security-basic-stellar-wp'
		: 'https://go.solidwp.com/stellar-sale-2025-plugin-solid-security-pro-stellar-wp';

	return (
		<ThemeProvider theme={ theme }>
			<StyledStellarSale>
				<StyledStellarSaleContent isSmall={ isSmall }>
					<StyledStellarSaleHeading
						level={ 2 }
						variant="white"
						weight={ 300 }
						size="extraLarge"
						isSmall={ isSmall }
					>
						<strong>{ __( 'Hackers Hate This Sale', 'better-wp-security' ) }</strong>
						<br />
						{ subtitle }
					</StyledStellarSaleHeading>
					{ isSmall && (
						<Text
							variant="white"
							size="subtitleSmall"
							weight={ 300 }
							text={ createInterpolateElement(
								__( 'Save <b>30%</b> on StellarWP brands like StellarSites, Kadence, GiveWP, and more during the Stellar Sale now through August 5th.', 'better-wp-security' ),
								{
									b: <strong />,
								}
							) }
						/>
					) }
					<StyledStellarSaleButton href={ shopNow } weight={ 600 }>
						{ __( 'Shop Now', 'better-wp-security' ) }
					</StyledStellarSaleButton>
					<StyledStellarSaleLink
						as="a"
						href={ shopBrands }
						variant="white"
						weight={ 700 }
						size="subtitleSmall"
						isSmall={ isSmall }
					>
						{ __( 'View all StellarWP Deals', 'better-wp-security' ) }
					</StyledStellarSaleLink>
				</StyledStellarSaleContent>
				<StyledStellarSaleDismiss
					label={ __( 'Dismiss', 'better-wp-security' ) }
					icon={ dismissIcon }
					onClick={ () => setIsDismiss( true ) }
				/>
				<StyledStellarSaleGraphic isWide={ isWide } />
			</StyledStellarSale>
		</ThemeProvider>
	);
}
