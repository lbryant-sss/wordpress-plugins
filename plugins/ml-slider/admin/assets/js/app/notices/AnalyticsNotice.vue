<template>
    <div class="rounded-md">
        <div class="text-center">
            <h3 class="m-0 p-3 text-lg leading-6 font-medium text-white bg-orange rounded-top">
                {{ __('Thanks for using MetaSlider', 'ml-slider') }}
            </h3>
            <div class="p-5">
                <p class="text-lg leading-5 text-gray-darker mb-0"> {{ __('We are currently building the next version of MetaSlider. Can you help us out by sharing non-sensitive diagnostic information?', 'ml-slider') }} </p>
            </div>
            <div class="relative rounded-md shadow-sm px-5">
                <input type="email" class="form-input block w-full md:text-sm md:leading-5" v-model="optinEmail" />
            </div>
            <p class="p-3 mt-0 max-w-xl text-sm leading-5 text-gray-dark" v-html="modalPrivacyPolicy()" />
        </div>
        <div class="mt-6 sm:grid sm:gap-3 sm:grid-flow-row-dense px-4 pb-5">
            <span class="flex w-full rounded-md shadow-sm sm:col-start-2">
                <button @click="opt('yes')" type="button" :disabled="!isValidEmail" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 text-base leading-6 font-medium text-white shadow-sm bg-orange hover:bg-orange-darker active:bg-orange-darkest transition ease-in-out duration-150 sm:text-sm sm:leading-5 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ __('Agree and continue', 'ml-slider') }}
                </button>
            </span>
            <span class="mt-3 flex w-full rounded-md sm:mt-0 sm:col-start-1">
                <button @click="opt('no')" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 text-base leading-6 font-medium text-black shadow-sm bg-gray hover:text-white hover:bg-gray-darker active:bg-gray-darkest transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    {{ __('No thanks', 'ml-slider') }}
                </button>
            </span>
        </div>
    </div>
</template>

<script>
import { Settings } from '../api'
import { EventManager } from '../utils'
export default {
    data() {
		return {
            optinEmail: ''
		}
	},
    filename: 'AnalyticsNotice',
    created() {
        this.$parent.classes = 'w-full max-w-lg rounded-lg'
        this.$parent.forceOpen = () => {
            this.opt('no')
            this.$parent.forceOpen = false  
        }
        Settings.getUserSetting().then(({data}) => {
			this.optinEmail = data.data
		})
        Settings.saveUserSetting('analytics_onboarding_status', 'no')
    },
    mounted() {
        this.notifyInfo('metaslider/add-slide-css-manager-notice-opened', this.__('Analytics notice opened', 'ml-slider'))
        document.addEventListener('keydown', this.handleKeydown)
    },
    beforeDestroy() {
        this.notifyInfo('metaslider/add-slide-css-manager-notice-closed', this.__('Analytics notice closed', 'ml-slider'))
        document.removeEventListener('keydown', this.handleKeydown)
    },
    computed: {
        isValidEmail() {
            return this.optinEmail && this.optinEmail.trim() !== ''
        }
    },
    methods: {
        handleKeydown(event) {
            if (event.key === 'Escape') {
                this.opt('no')
            }
        },
        modalPrivacyPolicy() {
            return this.sprintf(this.__('We\'d also like to send you infrequent emails with important security and feature updates. See our %s for more details.', 'ml-slider'), '<a target="_blank" class="underline" href="https://www.metaslider.com/privacy-policy">' + this.__('privacy policy', 'ml-slider') + '</a>', 'ml-slider')
        },
        async opt(type) {
            this.$parent.forceOpen = false
            this.$parent.close()
            await Settings.saveUserSetting('analytics_onboarding_status', type)
            if (type === 'yes') {
                await Settings.saveGlobalSettingsSingle('optin_via', 'modal')
                await Settings.saveGlobalSettingsSingle('optin_email', this.optinEmail)
                // A bit contrived but keeps the api from needing a patch endpoint
                Settings.getGlobalSettings().then(({data}) => {
                    const settings = data.data
                    settings.optIn = true
                    Settings.saveGlobalSettings(JSON.stringify({...settings}))
                })
            }
            EventManager.$emit('metaslider/start-tour')
        },
    }
}
</script>
