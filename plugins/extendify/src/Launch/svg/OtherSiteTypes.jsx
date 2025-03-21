import { memo } from '@wordpress/element';

const OtherSiteTypes = (props) => {
	const { className, ...otherProps } = props;

	return (
		<svg
			xmlns="http://www.w3.org/2000/svg"
			viewBox="0 -960 960 960"
			className={className}
			{...otherProps}>
			<path d="M278.46-276.16h513.85q4.61 0 8.46-3.84 3.84-3.85 3.84-8.46v-460.77H266.15v460.77q0 4.61 3.85 8.46 3.85 3.84 8.46 3.84Zm0 45.39q-23.53 0-40.61-17.08t-17.08-40.61v-513.85q0-23.53 17.08-40.61T278.46-860h513.85q23.52 0 40.61 17.08Q850-825.84 850-802.31v513.85q0 23.53-17.08 40.61-17.09 17.08-40.61 17.08H278.46ZM167.69-120q-23.52 0-40.61-17.08Q110-154.17 110-177.7v-559.22h45.39v559.22q0 4.62 3.84 8.47 3.85 3.84 8.46 3.84h559.23V-120H167.69Zm98.46-694.61v538.45-538.45Z" />
		</svg>
	);
};

export default memo(OtherSiteTypes);
