/**
 * External dependencies
 */
import styled from '@emotion/styled';

/**
 * WordPress dependencies
 */
import { Flex } from '@wordpress/components';

/**
 * SolidWP dependencies
 */
import { Text } from '@ithemes/ui';

export const ConnectionsTable = styled.div``;

export const ConnectionsTableRow = styled.div`
	display: grid;
	grid-template-columns: 6fr 4fr 2fr;
	padding: 20px 32px;
	border-bottom: 1px solid ${ ( { theme } ) => theme.colors.border.normal };

	@media ( max-width: ${ ( { theme } ) => `${ theme.breaks.medium }px` } ) {
		grid-template-columns: 8fr 2fr;
	}
`;

export const StyledFlex = styled( Flex )`
	margin-bottom: ${ ( { theme } ) => theme.spacing.section };
`;

export const StyledText = styled( Text )`
	@media ( max-width: ${ ( { theme } ) => `${ theme.breaks.medium }px` } ) {
		display: ${ ( { hideOnSmall } ) => ( hideOnSmall ? 'none' : '' ) };
	}
`;
