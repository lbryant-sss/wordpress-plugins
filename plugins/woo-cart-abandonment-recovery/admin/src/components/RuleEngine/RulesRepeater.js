import React, { useState, useEffect } from 'react';
import { Select, Button, Input, Container } from '@bsf/force-ui';
import { PlusIcon, TrashIcon } from '@heroicons/react/24/outline';
import { __ } from '@wordpress/i18n';
import ProductSearchField from '@Components/fields/ProductSearchField';
import MultiSelectField from '@Components/fields/MultiSelectField';

const RulesRepeater = ( { value = [], onChange } ) => {
	// Initialize with default structure following CartFlows Pro pattern
	const getDefaultRules = () => [
		{
			group_id: Math.random().toString( 36 ).substring( 2, 3 ),
			rules: [
				{
					rule_id: Math.random().toString( 36 ).substring( 2, 3 ),
					condition: 'cart_total',
					operator: '',
					value: '',
				},
			],
		},
	];

	// Parse value if it's a JSON string
	const parseValue = ( val ) => {
		if ( typeof val === 'string' ) {
			try {
				const parsed = JSON.parse( val );
				return Array.isArray( parsed ) ? parsed : getDefaultRules();
			} catch ( e ) {
				return getDefaultRules();
			}
		}
		return Array.isArray( val ) && val.length > 0 ? val : getDefaultRules();
	};

	const [ rules, setRules ] = useState( parseValue( value ) );

	// Get rule engine configuration from Pro plugin
	const ruleConfig = window.cart_abandonment_admin?.rule_engine || {};
	const conditions = ruleConfig.conditions || [];
	const fieldData = ruleConfig.field_data || {};

	useEffect( () => {
		// Convert rules array to JSON string before passing to parent
		onChange( JSON.stringify( rules ) );
	}, [ rules ] );

	const getOperatorsForCondition = ( condition ) => {
		const conditionData = fieldData[ condition ];
		if ( ! conditionData || ! conditionData.operators ) {
			return [];
		}
		return Object.keys( conditionData.operators ).map( ( key ) => ( {
			id: key,
			name: conditionData.operators[ key ],
		} ) );
	};

	const getFieldType = ( condition ) => {
		const conditionData = fieldData[ condition ];
		return conditionData?.type || 'text';
	};

	const getFieldOptions = ( condition ) => {
		const conditionData = fieldData[ condition ];
		if ( ! conditionData || ! conditionData.options ) {
			return [];
		}
		return Object.keys( conditionData.options ).map( ( key ) => ( {
			id: key,
			name: conditionData.options[ key ],
		} ) );
	};

	const updateRule = ( groupIndex, ruleIndex, field, updateValue ) => {
		const newRules = [ ...rules ];
		newRules[ groupIndex ].rules[ ruleIndex ][ field ] = updateValue;

		// Reset operator and value when condition changes
		if ( field === 'condition' ) {
			newRules[ groupIndex ].rules[ ruleIndex ].operator = '';
			newRules[ groupIndex ].rules[ ruleIndex ].value = '';
		}

		setRules( newRules );
	};

	const removeRule = ( groupIndex, ruleIndex ) => {
		const newRules = [ ...rules ];
		if ( newRules[ groupIndex ].rules.length > 1 ) {
			newRules[ groupIndex ].rules.splice( ruleIndex, 1 );
		} else {
			// Remove the group if it's the last rule
			newRules.splice( groupIndex, 1 );
		}
		// Ensure at least one group exists
		setRules( newRules.length > 0 ? newRules : getDefaultRules() );
	};

	const addGroup = () => {
		const newRules = [
			...rules,
			{
				group_id: Math.random().toString( 36 ).substring( 2, 3 ),
				rules: [
					{
						rule_id: Math.random().toString( 36 ).substring( 2, 3 ),
						condition: '',
						operator: '',
						value: '',
					},
				],
			},
		];
		setRules( newRules );
	};

	const renderValueField = ( rule, groupIndex, ruleIndex ) => {
		const operators = getOperatorsForCondition( rule.condition );
		const showValueField =
			operators.length > 0 &&
			! [ 'is_empty', 'is_not_empty' ].includes( rule.operator );

		if ( ! showValueField ) {
			return null;
		}

		const fieldType = getFieldType( rule.condition );

		switch ( fieldType ) {
			case 'select':
				const options = getFieldOptions( rule.condition );
				const selectedOption = options.find(
					( opt ) => opt.id === rule.value
				);
				return (
					<Select
						value={ selectedOption?.name || '' }
						onChange={ ( selected ) =>
							updateRule(
								groupIndex,
								ruleIndex,
								'value',
								selected.id
							)
						}
						size="md"
					>
						<Select.Button
							placeholder={ __(
								'Select value',
								'woo-cart-abandonment-recovery'
							) }
						/>
						<Select.Options>
							{ options.map( ( option ) => (
								<Select.Option
									key={ option.id }
									value={ option }
								>
									{ option.name }
								</Select.Option>
							) ) }
						</Select.Options>
					</Select>
				);

			case 'multiselect':
				return (
					<MultiSelectField
						name={ `rule_value_${ groupIndex }_${ ruleIndex }` }
						value={ rule.value || [] }
						optionsArray={ getFieldOptions( rule.condition ) }
						handleChange={ ( name, changedValue ) =>
							updateRule(
								groupIndex,
								ruleIndex,
								'value',
								changedValue
							)
						}
						autoSave={ false }
						disableStyle={ true }
					/>
				);

			case 'products':
				return (
					<ProductSearchField
						id={ `rule_value_${ groupIndex }_${ ruleIndex }` }
						name={ `rule_value_${ groupIndex }_${ ruleIndex }` }
						value={ rule.value || [] }
						placeholder={ __(
							'Search products…',
							'woo-cart-abandonment-recovery'
						) }
						handleChange={ ( name, chngValue ) =>
							updateRule(
								groupIndex,
								ruleIndex,
								'value',
								chngValue
							)
						}
						autoSave={ false }
						disableStyle={ true }
					/>
				);

			case 'number':
				return (
					<Input
						className="w-full focus:[&>input]:ring-focus leading-normal"
						type="number"
						size="md"
						value={ rule.value || '' }
						onChange={ ( e ) => {
							// Handle both event object and direct value
							const selectedValue = e?.target?.value ?? e;
							updateRule(
								groupIndex,
								ruleIndex,
								'value',
								selectedValue
							);
						} }
						placeholder={ __(
							'Enter value',
							'woo-cart-abandonment-recovery'
						) }
					/>
				);

			default:
				return (
					<Input
						className="w-full focus:[&>input]:ring-focus leading-normal"
						type="text"
						size="md"
						value={ rule.value || '' }
						onChange={ ( e ) => {
							// Handle both event object and direct value
							const val = e?.target?.value ?? e;
							updateRule( groupIndex, ruleIndex, 'value', val );
						} }
						placeholder={ __(
							'Enter value',
							'woo-cart-abandonment-recovery'
						) }
					/>
				);
		}
	};

	return (
		<Container className="space-y-4 py-4" direction="column">
			{ rules.map( ( group, groupIndex ) => (
				<Container.Item key={ group.group_id }>
					<Container
						className="p-4 rounded-lg border border-gray-200 border-solid"
						direction="column"
					>
						{ group.rules.map( ( rule, ruleIndex ) => (
							<Container.Item key={ rule.rule_id }>
								{ ruleIndex > 0 && (
									<Container.Item className="my-2 text-center text-gray-500 text-sm">
										{ __(
											'AND',
											'woo-cart-abandonment-recovery'
										) }
									</Container.Item>
								) }

								<Container className="flex gap-3 mb-3">
									<Container.Item className="flex-1">
										<Container
											className="gap-3"
											direction="column"
										>
											<Container.Item>
												<Select
													value={
														conditions.find(
															( c ) =>
																c.id ===
																rule.condition
														)?.name || ''
													}
													onChange={ ( selected ) =>
														updateRule(
															groupIndex,
															ruleIndex,
															'condition',
															selected.id
														)
													}
													size="md"
												>
													<Select.Button
														placeholder={ __(
															'Select condition',
															'woo-cart-abandonment-recovery'
														) }
													/>
													<Select.Options>
														{ conditions.map(
															( condition ) => (
																<Select.Option
																	key={
																		condition.id
																	}
																	value={
																		condition
																	}
																>
																	{
																		condition.name
																	}
																</Select.Option>
															)
														) }
													</Select.Options>
												</Select>
											</Container.Item>

											<Container.Item>
												<Select
													value={
														getOperatorsForCondition(
															rule.condition
														).find(
															( o ) =>
																o.id ===
																rule.operator
														)?.name || ''
													}
													onChange={ ( selected ) =>
														updateRule(
															groupIndex,
															ruleIndex,
															'operator',
															selected.id
														)
													}
													size="md"
													disabled={
														! rule.condition
													}
												>
													<Select.Button
														placeholder={ __(
															'Select operator',
															'woo-cart-abandonment-recovery'
														) }
													/>
													<Select.Options>
														{ getOperatorsForCondition(
															rule.condition
														).map( ( operator ) => (
															<Select.Option
																key={
																	operator.id
																}
																value={
																	operator
																}
															>
																{
																	operator.name
																}
															</Select.Option>
														) ) }
													</Select.Options>
												</Select>
											</Container.Item>

											<Container.Item>
												{ renderValueField(
													rule,
													groupIndex,
													ruleIndex
												) }
											</Container.Item>
										</Container>
									</Container.Item>

									<Container.Item className="flex-shrink-0">
										<Button
											size="sm"
											variant="ghost"
											className="text-red-600"
											onClick={ () =>
												removeRule(
													groupIndex,
													ruleIndex
												)
											}
										>
											<TrashIcon className="h-4 w-4" />
										</Button>
									</Container.Item>
								</Container>
							</Container.Item>
						) ) }

						{ /* <Container.Item>
							<Button
								icon={<PlusIcon aria-label="icon" role="img"/>}
								size="sm"
								variant="ghost"
								onClick={() => addRule(groupIndex)}
							>
								{__('Add Condition', 'woo-cart-abandonment-recovery')}
							</Button>
						</Container.Item> */ }
					</Container>

					{ groupIndex < rules.length - 1 && (
						<Container.Item className="my-3 text-center text-gray-500 text-sm pt-4">
							{ __( 'OR', 'woo-cart-abandonment-recovery' ) }
						</Container.Item>
					) }
				</Container.Item>
			) ) }

			<Container.Item>
				<Button
					icon={ <PlusIcon aria-label="icon" role="img" /> }
					className="text-button-primary"
					size="md"
					variant="ghost"
					onClick={ addGroup }
				>
					{ __( 'Add Rule', 'woo-cart-abandonment-recovery' ) }
				</Button>
			</Container.Item>
		</Container>
	);
};

export default RulesRepeater;
