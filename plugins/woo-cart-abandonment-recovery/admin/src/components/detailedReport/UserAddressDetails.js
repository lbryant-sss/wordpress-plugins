import { Title } from '@bsf/force-ui';

import SectionWrapper from '@Components/common/SectionWrapper';
import OrderStatusBadge from '@Components/common/OrderStatusBadge';
import SkeletonLoader from '@Components/common/skeletons/SkeletonLoader';

const UserAddressDetails = ( {
	userDetails,
	email,
	orderStatus,
	checkoutLink,
	isLoading,
} ) => {
	return (
		<SectionWrapper className="flex flex-col gap-4">
			<div className="flex items-center justify-between">
				<Title
					size="sm"
					tag="h2"
					title="User Address Details"
					className="[&_h2]:text-gray-900"
				/>
				{ ! isLoading && <OrderStatusBadge status={ orderStatus } /> }
			</div>
			{ isLoading ? (
				<div className="flex flex-col md:flex-row gap-8">
					<div className="flex-1 flex flex-col gap-3">
						<SkeletonLoader height="24px" width="150px" />
						{ [ ...Array( 8 ) ].map( ( _, index ) => (
							<SkeletonLoader
								key={ index }
								height="20px"
								// eslint-disable-next-line no-mixed-operators
								width={ `${ 70 + Math.random() * 30 }%` }
							/>
						) ) }
					</div>
					<div className="flex-1 flex flex-col gap-3">
						<SkeletonLoader height="24px" width="150px" />
						{ [ ...Array( 5 ) ].map( ( _, index ) => (
							<SkeletonLoader
								key={ index }
								height="20px"
								// eslint-disable-next-line no-mixed-operators
								width={ `${ 70 + Math.random() * 30 }%` }
							/>
						) ) }
						<div className="mt-2">
							<SkeletonLoader height="24px" width="200px" />
						</div>
					</div>
				</div>
			) : (
				<div className="flex flex-col md:flex-row gap-8">
					{ /* Billing Address */ }
					<div className="flex-1 flex flex-col gap-3">
						<Title
							size="xs"
							tag="h3"
							title="Billing Address:"
							className="[&_h2]:text-gray-900"
						/>
						<div className="flex flex-col gap-2">
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">
									Full Name:
								</span>{ ' ' }
								{ `${ userDetails.wcf_first_name } ${ userDetails.wcf_last_name }` }
							</p>
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">
									Contact Email:
								</span>{ ' ' }
								{ email }
							</p>
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">
									Contact Number:
								</span>{ ' ' }
								{ userDetails.wcf_phone_number }
							</p>
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">
									Street Address 1:
								</span>{ ' ' }
								{ userDetails.wcf_billing_address_1 }
							</p>
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">
									Street Address 2:
								</span>{ ' ' }
								{ userDetails.wcf_billing_address_2 }
							</p>
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">
									Country, City:
								</span>{ ' ' }
								{ userDetails.wcf_location }
							</p>
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">State:</span>{ ' ' }
								{ userDetails.wcf_billing_state }
							</p>
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">Zip Code:</span>{ ' ' }
								{ userDetails.wcf_billing_postcode }
							</p>
						</div>
					</div>
					{ /* Shipping Address */ }
					<div className="flex-1 flex flex-col gap-3">
						<Title
							size="xs"
							tag="h3"
							title="Shipping Address:"
							className="[&_h2]:text-gray-900"
						/>
						<div className="flex flex-col gap-2">
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">
									Street Address 1:
								</span>{ ' ' }
								{ userDetails.wcf_shipping_address_1 }
							</p>
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">
									Street Address 2:
								</span>{ ' ' }
								{ userDetails.wcf_shipping_address_2 }
							</p>
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">
									Country, City:
								</span>{ ' ' }
								{ userDetails.wcf_shipping_city +
									', ' +
									userDetails.wcf_shipping_country }
							</p>
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">State:</span>{ ' ' }
								{ userDetails.wcf_shipping_state }
							</p>
							<p className="m-0 text-gray-500">
								<span className="text-gray-900">Zip Code:</span>{ ' ' }
								{ userDetails.wcf_shipping_postcode }
							</p>
						</div>
						<div className="flex gap-4 items-center">
							<span className="text-base text-gray-900 font-medium">
								Checkout Page:
							</span>
							<a
								href={ checkoutLink }
								className="pb-0.5 text-xs font-semibold text-flamingo-400 no-underline border-0 border-solid border-b border-flamingo-400"
							>
								Link to Checkout Page
							</a>
						</div>
					</div>
				</div>
			) }
		</SectionWrapper>
	);
};

export default UserAddressDetails;
