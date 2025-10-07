<template>
	<div>
		<aio-login-captcha
			:has-pro="has_pro"
			:enabled="form_data.enabled"
			:config-data="configData"
			@toggle-captcha="handleToggleCaptcha"
			@save-settings="handleSaveSettings"
		/>

		<aio-login-snackbar
			:message="snackbar.message"
			:duration="snackbar.duration"
			v-if="snackbar.show"
			v-on:close="handleCloseSnackbar"
		/>
	</div>
</template>

<script>

export default {
	name: 'grecaptcha',

	data: () => ( {
		has_pro: 'true' === aio_login__app_object.has_pro,
		nonce: '',
		namespace: 'aio-login/grecaptcha',
		form_data: {
			enabled: false,
			version: 'v2',
			v2_site_key: '',
			v2_secret_key: '',
			theme: 'light',
			v3_site_key: '',
			v3_secret_key: '',
			threshold: '0.5',
		},
		snackbar: {
			message: '',
			duration: 3000,
			show: false,
		}
	} ),

	computed: {
		configData() {
			return {
				version: this.form_data.version,
				siteKey: this.form_data.version === 'v2' ? this.form_data.v2_site_key : this.form_data.v3_site_key,
				secretKey: this.form_data.version === 'v2' ? this.form_data.v2_secret_key : this.form_data.v3_secret_key,
				theme: this.form_data.theme,
				threshold: this.form_data.threshold
			};
		}
	},

	methods: {
		handleToggleCaptcha(enabled) {
			this.form_data.enabled = enabled;
			this.saveSettings();
		},

		handleSaveSettings(data) {
			// Update form data with popup data
			this.form_data.version = data.version;
			this.form_data.theme = data.theme;
			this.form_data.threshold = data.threshold;
			
			if (data.version === 'v2') {
				this.form_data.v2_site_key = data.siteKey;
				this.form_data.v2_secret_key = data.secretKey;
			} else {
				this.form_data.v3_site_key = data.siteKey;
				this.form_data.v3_secret_key = data.secretKey;
			}
			
			this.saveSettings();
		},

		saveSettings() {
			this.form_data._wpnonce = this.nonce;
			axios.post(this.namespace + '/save-settings', this.form_data)
				.then(response => {
					this.snackbar.message = response.data.message;
					this.snackbar.show = true;
				})
				.catch(error => {
					console.error('Error saving settings:', error);
				});
		},

		handleCloseSnackbar() {
			this.snackbar.show = false;
		}
	},

	mounted() {
		// Load settings
		axios.get(this.namespace + '/get-settings')
			.then(response => {
				this.form_data.enabled = response.data.enabled;
				this.form_data.version = response.data.version;
				this.form_data.v2_site_key = response.data.v2_site_key;
				this.form_data.v2_secret_key = response.data.v2_secret_key;
				this.form_data.theme = response.data.theme;
				this.form_data.v3_site_key = response.data.v3_site_key;
				this.form_data.v3_secret_key = response.data.v3_secret_key;
				this.form_data.threshold = response.data.threshold;
				this.nonce = response.data.nonce;
			})
			.catch(error => {
				console.error('Error loading settings:', error);
			});
	}
}
</script>

<style scoped>

</style>