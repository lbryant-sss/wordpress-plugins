/**
 * @link https://codex.wordpress.org/Javascript_Reference/wp.media
 * @link https://wordpress.stackexchange.com/a/382291
 */

import React from 'react';
import _ from 'lodash';
import {__, sprintf} from '@wordpress/i18n';
import classnames from 'classnames';
import './styles.scss';

type MediaLibrary = {
    id: string;
    value: string;
    onChange: (url: string, alt: string) => void;
    reset: () => void;
    label: string;
    actionLabel?: string;
    disabled?: boolean;
};

export default function UploadMedia({id, value, onChange, label, actionLabel, reset, disabled}: MediaLibrary) {
    // The media library uses Backbone.js, which can conflict with lodash.
    _.noConflict();
    let frame;

    const openMediaLibrary = (event) => {
        event.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = window.wp.media({
            title: __('Upload Media', 'give'),
            button: {
                text: __('Use this media', 'gie'),
            },
            library: {
                type: 'image', // Restricts media library to image files only
            },
            multiple: false, // Set to true to allow multiple files to be selected
        });

        frame.on('select', function () {
            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();

            if (!attachment.type || attachment.type !== 'image') {
                alert(__('Please select an image file only.', 'give'));
                frame.open();
                return;
            }

            onChange(attachment.url, attachment.alt);
        });

        // Finally, open the modal on click
        frame.open();
    };

    const resetImage = (event) => {
        reset();
    };

    const dropHandler = (event) => {
        event.preventDefault();
        openMediaLibrary(event);
    };

    return (
        <div id={id} className={classnames('givewp-media-library-control-wrapper', {'is-disabled': disabled})}>
            {value ? (
                <div className={'givewp-media-library-control'}>
                    <button className={'givewp-media-library-control__reset'} onClick={resetImage} disabled={disabled}>
                        <img
                            className={'givewp-media-library-control__image'}
                            src={value}
                            alt={__('uploaded image', 'give')}
                        />
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 14 14" fill="#fff">
                            <path
                                d="M4.66675 1.74935C4.66675 1.42718 4.92792 1.16602 5.25008 1.16602H8.75008C9.07225 1.16602 9.33342 1.42718 9.33342 1.74935C9.33342 2.07152 9.07225 2.33268 8.75008 2.33268H5.25008C4.92792 2.33268 4.66675 2.07152 4.66675 1.74935Z"
                                fill="#fff"
                            />
                            <path
                                fillRule="evenodd"
                                clipRule="evenodd"
                                d="M1.16675 3.49935C1.16675 3.17718 1.42792 2.91602 1.75008 2.91602H12.2501C12.5722 2.91602 12.8334 3.17718 12.8334 3.49935C12.8334 3.82152 12.5722 4.08268 12.2501 4.08268H11.6292L11.2549 9.69755C11.2255 10.1382 11.2012 10.5029 11.1576 10.7997C11.1122 11.1088 11.0402 11.3912 10.8903 11.6544C10.6569 12.0641 10.3048 12.3935 9.88046 12.5991C9.60788 12.7312 9.3213 12.7843 9.00992 12.809C8.71085 12.8327 8.34537 12.8327 7.90372 12.8327H6.09648C5.65483 12.8327 5.28935 12.8327 4.99028 12.809C4.6789 12.7843 4.39232 12.7312 4.11973 12.5991C3.69539 12.3935 3.34332 12.0641 3.1099 11.6544C2.95996 11.3912 2.88798 11.1088 2.84261 10.7997C2.79903 10.5029 2.77472 10.1382 2.74535 9.69754L2.37102 4.08268H1.75008C1.42792 4.08268 1.16675 3.82152 1.16675 3.49935ZM5.83342 5.54102C6.15558 5.54102 6.41675 5.80218 6.41675 6.12435V9.04102C6.41675 9.36318 6.15558 9.62435 5.83342 9.62435C5.51125 9.62435 5.25008 9.36318 5.25008 9.04102V6.12435C5.25008 5.80218 5.51125 5.54102 5.83342 5.54102ZM8.75008 6.12435C8.75008 5.80218 8.48891 5.54102 8.16675 5.54102C7.84458 5.54102 7.58342 5.80218 7.58342 6.12435V9.04102C7.58342 9.36318 7.84458 9.62435 8.16675 9.62435C8.48891 9.62435 8.75008 9.36318 8.75008 9.04102V6.12435Z"
                                fill="#fff"
                            />
                        </svg>
                    </button>

                    <div className={'givewp-media-library-control__options'}>
                        <button
                            className={
                                'givewp-media-library-control__options givewp-media-library-control__options--remove'
                            }
                            onClick={resetImage}
                            disabled={disabled}
                        >
                            {sprintf(__('Remove %s', 'give'), label.toLowerCase())}
                        </button>
                        <button
                            className={
                                'givewp-media-library-control__options givewp-media-library-control__options--update'
                            }
                            onClick={openMediaLibrary}
                            disabled={disabled}
                        >
                            {sprintf(__('Change %s', 'give'), label.toLowerCase())}
                        </button>
                    </div>
                </div>
            ) : (
                <div className={'givewp-media-library-drop-area'} onDragOver={!disabled && dropHandler}>
                    <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M19 13.5V14.7C19 16.3802 19 17.2202 18.673 17.862C18.3854 18.4265 17.9265 18.8854 17.362 19.173C16.7202 19.5 15.8802 19.5 14.2 19.5H5.8C4.11984 19.5 3.27976 19.5 2.63803 19.173C2.07354 18.8854 1.6146 18.4265 1.32698 17.862C1 17.2202 1 16.3802 1 14.7V13.5M15 6.5L10 1.5M10 1.5L5 6.5M10 1.5V13.5"
                            stroke="#6B7280"
                            strokeWidth="2"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                        />
                    </svg>
                    <button
                        className={'givewp-media-library-control__button'}
                        onClick={openMediaLibrary}
                        disabled={disabled}
                    >
                        {actionLabel}
                    </button>
                    <p>{__('or drag your image here', 'give')}</p>
                </div>
            )}
        </div>
    );
}
