import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { Container, Sidebar, Menu } from '@bsf/force-ui';
import { useStateValue } from '@Store';
import {
	Cog6ToothIcon,
	TagIcon,
	EnvelopeIcon,
	ChatBubbleBottomCenterIcon,
	CircleStackIcon,
	AdjustmentsVerticalIcon,
	LinkIcon,
} from '@heroicons/react/24/outline';
import TabWrapper from '@Components/common/TabWrapper';
import RenderFields from '@Components/RenderFields';
import ConditionsHelper from '@Utils/conditions';

const iconMap = {
	'general-settings': <Cog6ToothIcon className="h-6 w-6 text-gray-500" />,
	'webhook-settings': <LinkIcon className="h-6 w-6 text-gray-500" />,
	'coupon-settings': <TagIcon className="h-6 w-6 text-gray-500" />,
	'email-settings': <EnvelopeIcon className="h-6 w-6 text-gray-500" />,
	'recovery-report-settings': (
		<CircleStackIcon className="h-6 w-6 text-gray-500" />
	),
	'gdpr-settings': (
		<ChatBubbleBottomCenterIcon className="h-6 w-6 text-gray-500" />
	),
	'advanced-settings': (
		<AdjustmentsVerticalIcon className="h-6 w-6 text-gray-500" />
	),
};

const Settings = () => {
	const [ settingsTab, setSettingsTab ] = useState( '' );
	const navigate = useNavigate();
	const query = new URLSearchParams( useLocation().search );
	const [ state ] = useStateValue();
	const settingsData = state?.settingsData || {};
	const fieldSettings = settingsData?.fields?.settings || {};
	const settingsValues = settingsData?.values || {};

	const tabs = Object.values( fieldSettings ).sort(
		( a, b ) => a.priority - b.priority
	);

	const slugs = tabs.map( ( tab ) => tab.slug );
	const active = slugs.includes( query.get( 'tab' ) )
		? query.get( 'tab' )
		: getSettingsTab();

	function getSettingsTab() {
		return settingsTab || slugs[ 0 ];
	}

	const navigateTo = ( navigateTab ) => {
		setSettingsTab( navigateTab );
		navigate( {
			search: `?page=woo-cart-abandonment-recovery&path=settings&tab=${ navigateTab }`,
		} );
	};

	const conditions = new ConditionsHelper();

	const navigation = tabs.map( ( tab ) => {
		return {
			name: tab.title,
			slug: tab.slug,
			icon: iconMap[ tab.slug ] || (
				<Cog6ToothIcon className="h-6 w-6 text-gray-500" />
			),
			component: (
				<TabWrapper title={ tab.title }>
					{ tab.fields &&
						Object.keys( tab.fields ).map( ( field ) => {
							const data = tab.fields[ field ];
							const value =
								settingsValues[ data.name ] ?? data.value ?? '';
							const isActive = conditions.isActiveControl(
								data,
								settingsValues
							);
							return (
								<RenderFields
									key={ data.name || field }
									data={ data }
									value={ value }
									isActive={ isActive }
								/>
							);
						} ) }
				</TabWrapper>
			),
		};
	} );

	return (
		<div className="flex gap-4 bg-primary-background max-h-[calc(100%_-_6rem)]">
			<div className="h-full min-h-screen flex flex-col w-auto sticky top-0 lg:top-4 sidebar">
				<Sidebar
					borderOn
					className="!h-full md:pl-3 lg:p-4 lg:w-64 border-none box-border flex-grow"
				>
					<Sidebar.Body>
						<Sidebar.Item>
							<Menu size="md" className="p-0">
								<Menu.List open>
									{ navigation.map( ( item ) => (
										<Menu.Item
											key={ item.slug }
											active={ active === item.slug }
											onClick={ () =>
												navigateTo( item.slug )
											}
											className={ `lg:justify-start justify-center rounded-md ${
												active === item.slug
													? 'bg-flamingo-50'
													: ''
											} hover:bg-flamingo-50` }
										>
											{ item.icon }
											<span className="lg:block hidden text-gray-900">
												{ item.name }
											</span>
										</Menu.Item>
									) ) }
								</Menu.List>
							</Menu>
						</Sidebar.Item>
					</Sidebar.Body>
				</Sidebar>
			</div>
			<Container
				className="w-full max-w-[43.5rem] mx-auto mt-8 pr-4 pb-5 gap-0"
				direction="column"
			>
				{
					navigation.find( ( item ) => item.slug === active )
						?.component
				}
			</Container>
		</div>
	);
};

export default Settings;
