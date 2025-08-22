import { useState, useEffect } from 'react';
import { useStateValue } from '@Store';
import useDebounceDispatch from '@Utils/debounceDispatch';
import { Input } from '@bsf/force-ui';
import FieldWrapper from '@Components/common/FieldWrapper';

const TextField = ( {
	title,
	description,
	name,
	value,
	badge,
	handleChange,
	autoSave = true,
	error,
} ) => {
	const [ state, dispatch ] = useStateValue();
	const settingsData = state.settingsData || {};
	const settingsValues = settingsData.values || {};

	// Use provided value when handleChange is provided (form mode)
	// Otherwise use global state value (auto-save mode)
	const currentValue =
		handleChange && ! autoSave
			? value ?? ''
			: settingsValues[ name ] ?? value ?? '';
	const [ textValue, setTextValue ] = useState( currentValue );
	const debouncedUpdate = useDebounceDispatch(
		dispatch,
		name,
		undefined,
		400,
		true
	);

	useEffect( () => {
		const newValue =
			handleChange && ! autoSave
				? value ?? ''
				: settingsValues[ name ] ?? value ?? '';
		setTextValue( newValue );
	}, [ settingsValues[ name ], value, handleChange, autoSave ] );

	function handleOnChange( val ) {
		setTextValue( val );

		// If handleChange is provided and autoSave is false, use form mode
		if ( handleChange && ! autoSave ) {
			handleChange( name, String( val ) );
			return;
		}

		// Otherwise use auto-save mode
		if ( autoSave ) {
			debouncedUpdate( String( val ) );
		}
	}

	return (
		<FieldWrapper title={ title } description={ description } type="block">
			<div>
				<Input
					className={ `${
						badge ? 'w-24 ' : 'w-full'
					} focus:[&>input]:ring-focus ${
						error ? 'border-red-500' : ''
					}` }
					type="text"
					size="md"
					name={ name }
					value={ textValue }
					onChange={ handleOnChange }
				/>
				{ error && (
					<p className="text-red-500 text-sm mt-1">{ error }</p>
				) }
			</div>
		</FieldWrapper>
	);
};

export default TextField;
