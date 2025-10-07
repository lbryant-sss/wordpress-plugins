<template>
		<input
			v-if="'textarea' !== type"
			class="regular-text"
			:id="id"
			:name="name"
			:value="value"
			:type="type"
			@input="$emit( 'update:modelValue', $event.target.value )"
		>

		<textarea
			v-else
			class="regular-text"
			:id="id"
			:name="name"
		>{{ value }}</textarea>
</template>

<script>
export default {
	name: 'aio-login-text',

	props: {
		type: {
			type: String,
			default: 'text',
		},
		id: {
			type: String,
			required: true,
		},
		name: {
			type: String,
			required: true,
		},
		value: {
			type: String,
			default: '',
		},
	},
}
</script>

<style scoped>

</style>