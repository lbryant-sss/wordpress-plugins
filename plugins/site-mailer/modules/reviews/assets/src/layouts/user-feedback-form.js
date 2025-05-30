import Box from '@elementor/ui/Box';
import Popover from '@elementor/ui/Popover';
import Typography from '@elementor/ui/Typography';
import { styled } from '@elementor/ui/styles';
import { eventNames, mixpanelService, useStorage } from '@site-mailer/globals';
import { useEffect, useRef } from '@wordpress/element';
import { escapeHTML } from '@wordpress/escape-html';
import { __ } from '@wordpress/i18n';
import API from '../api';
import DismissButton from '../components/dismiss-button';
import FeedbackForm from '../components/feedback-form';
import RatingForm from '../components/rating-form';
import ReviewForm from '../components/review-form';
import { useNotifications, useSettings } from '../hooks/use-settings';

const UserFeedbackForm = ( ) => {
	const anchorEl = useRef( null );

	const { success, error } = useNotifications();
	const { save, get } = useStorage();
	const {
		rating,
		setRating,
		feedback,
		isOpened,
		setIsOpened,
		setCurrentPage,
	} = useSettings();

	useEffect( () => {
		/**
		 * Show the popover if the user has not submitted repo feedback.
		 */
		if ( window?.siteMailerReviewData?.reviewData?.rating > 3 && ! window?.siteMailerReviewData?.reviewData?.repo_review_clicked ) {
			setCurrentPage( 'review' );
			setRating( window?.siteMailerReviewData?.reviewData?.rating ); // re-add the saved rating
		}
	}, [] );

	useEffect( () => {
		if ( isOpened ) {
			mixpanelService.init().then( () => {
				mixpanelService.sendEvent( eventNames.review.promptShown, {} );
			} );
		}
	}, [ isOpened ] );

	/**
	 * Close the popover.
	 * @param {Object} event
	 * @param {string} reason
	 */
	const handleClose = ( event, reason ) => {
		if ( 'backdropClick' !== reason ) {
			setIsOpened( false );
		}

		mixpanelService.sendEvent( eventNames.review.dismissClicked );
	};

	const id = isOpened ? 'reviews-popover' : undefined;

	const { currentPage } = useSettings();

	const headerMessage = {
		ratings: __( 'How would you rate Site Mailer so far?', 'site-mailer' ),
		feedback: __( 'We’re thrilled to hear that! What would make it even better?', 'site-mailer' ),
		review: __( "We're thrilled you're enjoying Site Mailer", 'site-mailer' ),
	};

	const handleSubmit = async ( close, avoidClosing = false ) => {
		try {
			await API.sendFeedback( { rating, feedback } ).then( async () => {
				await save( {
					site_mailer_review_data: {
						...get.data.site_mailer_review_data,
						rating: parseInt( rating ),
						feedback: escapeHTML( feedback ),
						submitted: true,
					},
				} );
			} );

			if ( rating && ! feedback ) {
				mixpanelService.sendEvent( eventNames.review.starSelected, {
					rating: parseInt( rating ),
				} );
			}

			if ( feedback ) {
				mixpanelService.sendEvent( eventNames.review.feedbackSubmitted, {
					feedback_text: escapeHTML( feedback ),
					rating: parseInt( rating ),
				} );
			}

			if ( ! avoidClosing ) {
				await close();
			}
			await success( __( 'Thank you for your feedback!', 'site-mailer' ) );
			return true;
		} catch ( e ) {
			error( __( 'Failed to submit!', 'site-mailer' ) );
			console.log( e );
			return false;
		}
	};

	return (
		<Popover
			open={ isOpened }
			anchorOrigin={ { vertical: 'bottom', horizontal: 'right' } }
			anchorReference="anchorPosition"
			anchorPosition={ { top: window.innerHeight - 10, left: window.innerWidth - 10 } }
			id={ id }
			onClose={ handleClose }
			anchorEl={ anchorEl.current }
			disableEscapeKeyDown
			disableScrollLock
			disablePortal
			slotProps={ {
				paper: {
					sx: {
						pointerEvents: 'auto', // allow interactions inside popover
					},
				},
			} }
			sx={ {
				pointerEvents: 'none', // allow click-through behind
			} }
		>
			<StyledBox>
				<Header>
					<Typography
						variant="subtitle1"
						color="text.primary"
					>
						{ headerMessage?.[ currentPage ] }
					</Typography>
					<DismissButton close={ close } />
				</Header>
				{ 'ratings' === currentPage && <RatingForm close={ handleClose } handleSubmitForm={ handleSubmit } /> }
				{ 'feedback' === currentPage && <FeedbackForm close={ handleClose } handleSubmitForm={ handleSubmit } /> }
				{ 'review' === currentPage && <ReviewForm close={ handleClose } /> }
			</StyledBox>
		</Popover>
	);
};

export default UserFeedbackForm;

const StyledBox = styled( Box )`
	width: 350px;
	padding: ${ ( { theme } ) => theme.spacing( 1.5 ) };
`;

const Header = styled( Box )`
	display: flex;
	flex-direction: row;
	justify-content: space-between;
	align-items: center;
	margin-bottom: ${ ( { theme } ) => theme.spacing( 2 ) };
`;
