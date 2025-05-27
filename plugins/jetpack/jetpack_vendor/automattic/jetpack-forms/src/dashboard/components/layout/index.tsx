/**
 * External dependencies
 */
import { JetpackFooter, useBreakpointMatch } from '@automattic/jetpack-components';
import { shouldUseInternalLinks } from '@automattic/jetpack-shared-extension-utils';
import { TabPanel } from '@wordpress/components';
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import clsx from 'clsx';
import { Outlet, useLocation, useNavigate } from 'react-router-dom';
/**
 * Internal dependencies
 */
import ExportResponsesButton from '../../inbox/export-responses';
import { config } from '../../index';
import CreateFormButton from '../create-form-button';
import JetpackFormsLogo from '../logo';
import './style.scss';

const Layout = ( {
	className = '',
	showFooter = false,
}: {
	className?: string;
	showFooter?: boolean;
} ) => {
	const location = useLocation();
	const navigate = useNavigate();
	const [ isSm ] = useBreakpointMatch( 'sm' );
	const createSmallLabel = __( 'Create', 'jetpack-forms' );
	const createLargeLabel = __( 'Create form', 'jetpack-forms' );
	const createButtonLabel = isSm ? createSmallLabel : createLargeLabel;

	const enableIntegrationsTab = config( 'enableIntegrationsTab' );

	const tabs = [
		{
			name: 'responses',
			title: __( 'Responses', 'jetpack-forms' ),
		},
		...( enableIntegrationsTab
			? [ { name: 'integrations', title: __( 'Integrations', 'jetpack-forms' ) } ]
			: [] ),
		{
			name: 'about',
			title: __( 'About', 'jetpack-forms' ),
		},
	];

	const getCurrentTab = () => {
		const path = location.pathname.split( '/' )[ 1 ];
		const validTabNames = tabs.map( tab => tab.name );
		if ( validTabNames.includes( path ) ) {
			return path;
		}
		return config( 'hasFeedback' ) ? 'responses' : 'about';
	};

	const handleTabSelect = useCallback(
		( tabName: string ) => {
			if ( ! tabName ) {
				tabName = config( 'hasFeedback' ) ? 'responses' : 'about';
			}
			navigate( {
				pathname: `/${ tabName }`,
				search: tabName === 'responses' ? location.search : '',
			} );
		},
		[ navigate, location.search ]
	);

	return (
		<div className={ clsx( 'jp-forms__layout', className ) }>
			<div className="jp-forms__layout-header">
				<div className="jp-forms__logo-wrapper">
					<JetpackFormsLogo />
				</div>
				<div className="jp-forms__layout-header-actions">
					{ getCurrentTab() === 'responses' && <ExportResponsesButton /> }
					<CreateFormButton label={ createButtonLabel } />
				</div>
			</div>
			<TabPanel
				className="jp-forms__dashboard-tabs"
				tabs={ tabs }
				initialTabName={ getCurrentTab() }
				onSelect={ handleTabSelect }
			>
				{ () => <Outlet /> }
			</TabPanel>
			{ showFooter && (
				<JetpackFooter
					className="jp-forms__layout-footer"
					moduleName={ __( 'Jetpack Forms', 'jetpack-forms' ) }
					useInternalLinks={ shouldUseInternalLinks() }
				/>
			) }
		</div>
	);
};

export default Layout;
