/**
 * External dependencies
 */
import styled from '@emotion/styled';

export const ConnectionInfo = styled.div`
	display: flex;
`;

export const ConnectionInfoImage = styled.div`
	width: 40px;
	height: 40px;
	border-radius: 4px;
	margin-right: 20px;

	@media ( max-width: ${ ( { theme } ) => `${ theme.breaks.medium }px` } ) {
		display: none;
	}
`;

export const ConnectionInfoName = styled.div`
	span {
		display: block;
		margin-top: 5px;
	}
`;

export const ConnectionToggle = styled.div`
	@media ( max-width: ${ ( { theme } ) => `${ theme.breaks.medium }px` } ) {
		display: none;
	}
`;

export const ConnectionActions = styled.div`
	button {
		margin-right: 10px;
	}

	button:last-child {
		margin-right: 0;
	}
`;
