import { Label } from '@bsf/force-ui';
import parse from 'html-react-parser';

import Tooltip from '@Components/fields/Tooltip';
import FeatureBadge from '@Components/common/FeatureBadge';

const FieldWrapper = ( {
	title,
	name = '',
	description,
	type = 'inline',
	tooltip = '',
	size = 'normal',
	disableStyle = false,
	badge = '',
	children,
} ) => {
	// Generate a unique ID
	const fieldId = `field-${ Math.random().toString( 36 ).substr( 2, 9 ) }`;

	return (
		<section
			id={ name ? `wcar-${ name }` : undefined }
			className={ `flex ${
				type === 'block' ? 'flex-col' : 'flex-col sm:flex-row'
			} ${
				size === 'normal' ? 'py-6' : 'py-5'
			} justify-between gap-2 lg:gap-5 ${
				! disableStyle
					? 'border-0 border-b border-solid border-gray-200 last:border-b-0'
					: '!p-0'
			}` }
			aria-labelledby={ title ? `${ fieldId }-title` : undefined }
		>
			{ ( title || description ) && (
				<div
					className={ `${
						type === 'block' ? 'w-full' : 'w-full sm:w-[70%]'
					}` }
				>
					{ title && (
						<div className="flex gap-2 items-center mb-1">
							<Label
								className={ `${
									size === 'normal'
										? 'font-semibold'
										: 'font-medium'
								}` }
								htmlFor={ fieldId }
								size={ `${ size === 'normal' ? 'md' : 'sm' }` }
								id={ `${ fieldId }-title` }
								as="h3"
							>
								{ title.replace( /\b\w/g, ( char ) =>
									char.toLocaleUpperCase()
								) }
								{ tooltip && tooltip?.content && (
									<Tooltip
										content={ tooltip?.content }
										title={ tooltip?.title }
									/>
								) }
							</Label>
							{ badge && <FeatureBadge feature={ badge } /> }
						</div>
					) }
					{ description && (
						<p
							className="font-normal text-sm text-gray-500 m-0"
							id={ `${ fieldId }-description` }
							aria-hidden="true"
						>
							{ parse( description ) }
						</p>
					) }
				</div>
			) }
			{ children }
		</section>
	);
};

export default FieldWrapper;
