/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * SolidWP dependencies
 */
import { Button, Text, TextVariant } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { StyledActions, StyledBody, StyledModel } from './styles';

/**
 * Renders a confirmation dialog with customizable actions.
 *
 * @return {JSX.Element} The confirmation dialog component.
 */
function ConfirmationDialog(
    {
        title,
        body,
        onContinue,
        onCancel,
        continueText,
        cancelText = __( 'Cancel', 'LION' ),
        isBusy,
    } ) {

    return (
        <StyledModel title={ title }
                     onRequestClose={ onCancel }
                     focusOnMount
                     closeButtonLabel={ cancelText }>
            <StyledBody>
                <Text as="p" variant={ TextVariant.DARK } text={ body }/>
                <StyledActions>
                    <Button
                        text={ cancelText }
                        onClick={ onCancel }
                        variant="secondary"
                    />
                    <Button
                        text={ continueText }
                        onClick={ onContinue }
                        variant="primary"
                        isBusy={ isBusy }
                    />
                </StyledActions>
            </StyledBody>
        </StyledModel>
    )
}

export default ConfirmationDialog