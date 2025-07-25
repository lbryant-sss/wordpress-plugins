import React, { useState } from 'react';
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { ToggleControl } from '@wordpress/components';
import { Toast } from '@DashboardComponents';
import { ReactSVG } from 'react-svg';

const ModuleCard = ({
	moduleInfo: { name, title, icon, is_pro, badge, demo_link },
	isLiteInstalled,
}) => {
	const modulesStatuses = useSelect((select) =>
		select('divitorque/dashboard').getModulesStatuses()
	);

	const dispatch = useDispatch('divitorque/dashboard');

	const isModuleActive = modulesStatuses[name] === name;

	const [isLoading, setIsLoading] = useState(false);

	const toggleModuleStatus = async () => {
		setIsLoading(true);

		const newStatus = isModuleActive ? 'disabled' : name;
		const updatedStatuses = { ...modulesStatuses, [name]: newStatus };

		wp.apiFetch({
			path: '/divitorque-lite/v1/save_common_settings',
			method: 'POST',
			data: { modules_settings: updatedStatuses },
		})
			.then((res) => {
				if (res.success) {
					dispatch.updateModuleStatuses(updatedStatuses);
					Toast(__('Successfully saved!', 'divitorque'), 'success');
				} else {
					Toast(__('Something went wrong!', 'divitorque'), 'error');
				}
			})
			.catch((err) => {
				Toast(err.message, 'error');
			})
			.finally(() => {
				setIsLoading(false);
			});
	};

	const moduleIconPath = window.diviTorque?.module_icon_path || '';
	const moduleIcon = `${moduleIconPath}/${icon}`;
	const cardClass = `p-4 bg-white border border-solid border-de-light-gray rounded-md flex items-center gap-x-4`;

	return (
		<div className={cardClass}>
			<div className="flex-shrink-0"></div>
			<div className="flex-1 min-w-0">
				<p
					className={`text-base font-medium text-de-black flex items-center`}
				>
					{title}
					{!is_pro && (
						<span className="ml-2 px-1.5 text-[11px] bg-de-light-gray text-de-gray rounded uppercase">
							{__('Lite', 'divitorque')}
						</span>
					)}
					{badge && (
						<span className="ml-2 px-1.5 text-[10px] bg-de-app-color-dark text-de-light-gray rounded uppercase">
							{badge}
						</span>
					)}
				</p>
			</div>
			{is_pro && (
				<a
					href="https://divitorque.com/pricing/"
					target="_blank"
					rel="noreferrer noopener"
					className="text-de-app-color-dark"
				>
					{__('Pro', 'divitorque')}
				</a>
			)}
			<div className="dt-toggle-control">
				{!is_pro && (
					<ToggleControl
						checked={isModuleActive}
						onChange={toggleModuleStatus}
						disabled={isLoading}
					/>
				)}
			</div>
		</div>
	);
};

export default ModuleCard;
