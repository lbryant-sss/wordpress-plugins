/**
 * External dependencies
 */
import React from '@wordpress/element';

/**
 * WordPress dependencies
 */
import { Button, Card, CardBody, CardHeader, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

function GeneralSettings( { settings, setSettings } ) {
    function handleToggleChange( newValue ) {
        setSettings( ( prevSettings ) => ( {
            ...prevSettings,
            disable_logs: newValue ? 'no' : 'yes',
        } ) );
    }

    return (
        <Card>
            <CardHeader>
                <h2>{ __( 'Email Logs', 'LION' ) }</h2>
            </CardHeader>
            <CardBody>
                <ToggleControl
                    label={ __( 'Enable Logs', 'LION' ) }
                    checked={ settings.disable_logs === 'no' }
                    onChange={ handleToggleChange }
                />
                <Button variant={ 'primary' } type={ 'submit' }>
                    { __( 'Save', 'LION' ) }
                </Button>
            </CardBody>
        </Card>
    );
}

export default GeneralSettings;