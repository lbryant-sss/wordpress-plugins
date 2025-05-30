import { memo } from '@wordpress/element';

const Business = (props) => {
	const { className, ...otherProps } = props;

	return (
		<svg
			xmlns="http://www.w3.org/2000/svg"
			viewBox="0 -960 960 960"
			className={className}
			{...otherProps}>
			<path d="M94.62-136.92v-700h372.69v163.46h398.07v536.54H94.62ZM140-182.31h118.46v-118.46H140v118.46Zm0-163.84h118.46v-118.08H140v118.08Zm0-163.47h118.46v-118.46H140v118.46Zm0-163.84h118.46v-118.08H140v118.08Zm163.85 491.15h118.07v-118.46H303.85v118.46Zm0-163.84h118.07v-118.08H303.85v118.08Zm0-163.47h118.07v-118.46H303.85v118.46Zm0-163.84h118.07v-118.08H303.85v118.08Zm163.46 491.15H820v-445.77H467.31v118.46H555v45.39h-87.69v118.08H555v45.38h-87.69v118.46Zm193.84-281.92v-45.39h45.39v45.39h-45.39Zm0 163.46v-45.38h45.39v45.38h-45.39Z" />
		</svg>
	);
};

export default memo(Business);
