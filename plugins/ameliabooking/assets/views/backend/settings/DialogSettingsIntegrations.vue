<template>

  <!-- Dialog Settings Integrations -->
  <div>

    <!-- Dialog -->
    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="20">
            <h2>
              {{ $root.labels.integrations_settings }}
            </h2>
          </el-col>
          <el-col :span="4" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>
      <!-- /Dialog Header -->

      <!-- Form -->
      <el-form label-position="top" @submit.prevent="onSubmit">

        <!-- Tabs -->
        <el-tabs v-model="activeTab">

          <!-- Google Calendar -->
          <el-tab-pane
            v-if="notInLicence() ? licenceVisible() : true"
            :label="$root.labels.google_calendar"
            name="googleCalendar"
          >
            <LicenceBlockHeader/>

            <google-calendar
              :class="licenceClassDisabled()"
              :googleCalendar="googleCalendarSettings"
              @openDialog="openDialog"
            />
          </el-tab-pane>
          <!-- /Google Calendar -->

          <!-- Outlook Calendar -->
          <el-tab-pane
            v-if="notInLicence() ? licenceVisible() : true"
            :label="$root.labels.outlook_calendar"
            name="outlookCalendar"
          >
            <LicenceBlockHeader/>

            <outlook-calendar
              :class="licenceClassDisabled()"
              :outlookCalendar="outlookCalendarSettings"
              @openDialog="openDialog"
            />
          </el-tab-pane>
          <!-- /Outlook Calendar -->

          <!-- Apple Calendar -->
          <el-tab-pane
            v-if="notInLicence() ? licenceVisible() : true"
            :label="$root.labels.apple_calendar"
            name="appleCalendar"
            >
            <LicenceBlockHeader/>

            <apple-calendar
              :class="licenceClassDisabled()"
              :appleCalendar="appleCalendarSettings"
              @openDialog="openDialog"
            />
          </el-tab-pane>
          <!-- /Apple Calendar -->

          <!-- Zoom -->
          <el-tab-pane
            v-if="notInLicence() ? licenceVisible() : true"
            :label="$root.labels.zoom"
            name="zoom"
          >
            <LicenceBlockHeader/>

            <zoom
              :class="licenceClassDisabled()"
              :zoom="zoomSettings"
            />
          </el-tab-pane>
          <!-- /Zoom -->

          <!-- Web Hooks -->
          <el-tab-pane
            v-if="notInLicence() ? licenceVisible() : true"
            :label="$root.labels.web_hooks"
            name="webHooks"
          >
            <LicenceBlockHeader/>

            <web-hooks
              :class="licenceClassDisabled()"
              :webHooks="webHooksSettings"
            />
          </el-tab-pane>
          <!-- /Web Hooks -->

          <!-- Marketing -->
          <el-tab-pane
            v-if="notInLicence('starter') ? licenceVisible() : true"
            :label="$root.labels.marketing_tools"
            name="marketing"
          >
            <LicenceBlockHeader :licence="'starter'"/>

            <marketing
              ref="marketing"
              :class="licenceClassDisabled('starter')"
              :facebookPixel="facebookPixelSettings"
              :googleAnalytics="googleAnalyticsSettings"
              :customFields="customFields"
              :googleTag="googleTagSettings"
              :mailchimp="mailchimpSettings"
              :mailchimp-lists="mailchimpLists"
              :open-mailchimp-collapse="openMailchimpCollapse || openMailchimp"
              @disconnectMailchimp="disconnectMailchimp"
            />
          </el-tab-pane>
          <!-- /Marketing -->

          <!-- Social Login -->
          <el-tab-pane
              v-if="notInLicence() ? licenceVisible() : true"
              :label="$root.labels.social_login"
              name="socialLogin"
          >
            <LicenceBlockHeader/>

            <social-login
              :class="licenceClassDisabled()"
              ref="socialLogin"
              :social-login="socialLoginSettings"
            />
          </el-tab-pane>
          <!-- /Social Login -->

          <!-- Lesson Space -->
          <el-tab-pane
            v-if="notInLicence('starter') ? licenceVisible() : true"
            :label="$root.labels.lesson_space"
            name="lessonSpace"
          >
            <LicenceBlockHeader :licence="'starter'"/>

            <lesson-space
              :class="licenceClassDisabled('starter')"
              :lessonSpace="lessonSpaceSettings"
            />
          </el-tab-pane>
          <!-- /Lesson Space -->

        </el-tabs>
        <!-- /Tabs -->

      </el-form>
      <!-- /Form -->

    </div>
    <!-- /Dialog -->

    <!-- Dialog Footer -->
    <div class="am-dialog-footer">
      <div class="am-dialog-footer-actions">
        <el-row>
          <el-col :sm="24" class="align-right">
            <el-button type="" @click="closeDialog" class="">{{ $root.labels.cancel }}</el-button>
            <el-button type="primary" @click="onSubmit" class="am-dialog-create">{{ $root.labels.save }}</el-button>
          </el-col>
        </el-row>
      </div>
    </div>
    <!-- /Dialog Footer -->

  </div>
  <!-- /Dialog Settings Integrations -->

</template>

<script>
  import GoogleCalendar from './Integrations/IntegrationsGoogleCalendar.vue'
  import licenceMixin from '../../../js/common/mixins/licenceMixin'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import OutlookCalendar from './Integrations/IntegrationsOutlookCalendar.vue'
  import WebHooks from './Integrations/IntegrationsWebHooks.vue'
  import Zoom from './Integrations/IntegrationsZoom.vue'
  import Marketing from './Integrations/IntegrationsMarketing.vue'
  import LessonSpace from './Integrations/IntegrationsLessonSpace.vue'
  import AppleCalendar from './Integrations/IntegrationsAppleCalendar.vue'
  import SocialLogin from './Integrations/SocialLogin.vue'

  export default {
    components: {
      SocialLogin,
      AppleCalendar,
      GoogleCalendar,
      OutlookCalendar,
      Zoom,
      WebHooks,
      Marketing,
      LessonSpace
    },

    props: {
      outlookSignedIn: {
        type: Boolean,
        default: false
      },
      googleCalendar: {
        type: Object
      },
      outlookCalendar: {
        type: Object
      },
      appleCalendar: {
        type: Object
      },
      zoom: {
        type: Object
      },
      webHooks: {
        type: Array
      },
      facebookPixel: {
        type: Object
      },
      googleAnalytics: {
        type: Object
      },
      customFields: {
        default: []
      },
      googleTag: {
        type: Object
      },
      mailchimp: {
        type: Object
      },
      lessonSpace: {
        type: Object
      },
      socialLogin: {
        type: Object
      },
      mailchimpLists: {
        type: Array
      },
      openMailchimpCollapse: {
        type: Boolean,
        default: false
      }
    },

    mixins: [
      licenceMixin,
      imageMixin
    ],

    data () {
      return {
        googleCalendarSettings: Object.assign({}, this.googleCalendar),
        outlookCalendarSettings: Object.assign({}, this.outlookCalendar),
        appleCalendarSettings: Object.assign({}, this.appleCalendar),
        zoomSettings: Object.assign({}, this.zoom),
        lessonSpaceSettings: Object.assign({}, this.lessonSpace),
        googleAnalyticsSettings: Object.assign({}, this.googleAnalytics),
        facebookPixelSettings: Object.assign({}, this.facebookPixel),
        googleTagSettings: Object.assign({}, this.googleTag),
        mailchimpSettings: Object.assign({}, this.mailchimp),
        webHooksSettings: this.webHooks.map((webHook) => webHook),
        activeTab: this.notInLicence() ? 'marketing' : 'googleCalendar',
        socialLoginSettings: Object.assign({}, this.socialLogin),
        openMailchimp: false
      }
    },

    mounted () {
      if (this.outlookSignedIn) {
        this.activeTab = 'outlookCalendar'
      } else if (this.openMailchimpCollapse) {
        this.activeTab = 'marketing'
      } else {
        this.activeTab = this.notInLicence() ? 'marketing' : 'googleCalendar'
      }
    },

    methods: {
      disconnectMailchimp () {
        this.mailchimpSettings = {
          accessToken: null,
          server: null,
          list: null
        }
      },

      openDialog (name) {
        this.$emit('openDialog', name)
      },

      closeDialog () {
        this.$emit('closeDialogSettingsIntegrations')
      },

      async onSubmit () {
        let socialLoginValid = true
        let mailchimpValid = true
        if (this.$refs.socialLogin && typeof this.$refs.socialLogin.validate === 'function') {
          socialLoginValid = await this.$refs.socialLogin.validate()
        }
        if (!socialLoginValid) {
          this.activeTab = 'socialLogin'
          return
        }

        if (this.$refs.marketing.$refs.mailchimpSettings && typeof this.$refs.marketing.$refs.mailchimpSettings.validate === 'function') {
          try {
            mailchimpValid = await this.$refs.marketing.$refs.mailchimpSettings.validate()
          } catch (error) {
            mailchimpValid = false
          }
        }

        if (!mailchimpValid) {
          this.activeTab = 'marketing'
          this.openMailchimp = true
          return
        }

        ['facebookPixelSettings', 'googleAnalyticsSettings', 'googleTagSettings'].forEach((vendor) => {
          ['appointment', 'package', 'event'].forEach((type) => {
            switch (vendor) {
              case ('facebookPixelSettings'):
                this[vendor].tracking[type] = this[vendor].tracking[type].filter(item => item.type.trim() && item.event.trim())

                break
              case ('googleAnalyticsSettings'):
                this[vendor].tracking[type] = this[vendor].tracking[type].filter(item => item.type.trim() && item.event.trim())

                break
              case ('googleTagSettings'):
                this[vendor].tracking[type] = this[vendor].tracking[type].filter(item => item.type.trim() && item.category.trim() && item.action.trim())

                break
            }
          })
        })

        this.outlookCalendarSettings.mailEnabled = !!(this.outlookCalendarSettings.clientID &&
          this.outlookCalendarSettings.clientSecret &&
          this.outlookCalendarSettings.mailEnabled &&
          this.outlookCalendarSettings.token)

        this.$emit('closeDialogSettingsIntegrations')
        this.$emit('updateSettings', {
          'googleCalendar': this.googleCalendarSettings,
          'outlookCalendar': this.outlookCalendarSettings,
          'appleCalendar': this.appleCalendarSettings,
          'zoom': this.zoomSettings,
          'webHooks': this.webHooksSettings,
          'facebookPixel': this.facebookPixelSettings,
          'googleAnalytics': this.googleAnalyticsSettings,
          'googleTag': this.googleTagSettings,
          'mailchimp': this.mailchimpSettings,
          'lessonSpace': this.lessonSpaceSettings,
          'socialLogin': this.socialLoginSettings
        })
      }
    }
  }
</script>
