<template>

  <!-- Integration Social Login -->
  <el-form :model="settings" :rules="rules" ref="settings" label-position="top" @submit.prevent="onSubmit">

    <div class="am-social-login">
      <!-- Google Social Login Enabled -->
      <div class="am-social-login__google">
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="21">
            <el-col :span="21">
              {{ $root.labels.enable_google_social_login }}
            </el-col>
            <el-col :span="4" class="align-right">
              <el-switch
                  v-model="settings.enableGoogleLogin"
                  active-text=""
                  inactive-text=""
              >
              </el-switch>
            </el-col>
          </el-row>
        </div>
        <div>
          <el-col :span="24">
            <el-tooltip class="am-google-calendar-tooltip" effect="dark" placement="top">
              <div slot="content" v-html="$root.labels.social_login_google_configuration"></div>
              <el-button
                  class="am-google-calendar-button am-button-icon"
                  style="height: 48px;"
                  type="primary"
                  @click="redirectToDocumentation()"
              >
                <img class="svg-amelia" :src="$root.getUrl + 'public/img/question.svg'"/>
              </el-button>
            </el-tooltip>
          </el-col>
        </div>
      </div>
      <!-- /Google Social Login Enabled -->

      <!-- Facebook Social Login Enabled -->
      <div class="am-social-login__facebook">
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="21">
            <el-col :span="21">
              {{ $root.labels.enable_facebook_social_login }}
            </el-col>
            <el-col :span="4" class="align-right">
              <el-switch
                  v-model="settings.enableFacebookLogin"
                  active-text=""
                  inactive-text=""
              >
              </el-switch>
            </el-col>
          </el-row>
        </div>
        <div>
          <el-col :span="24">
            <el-tooltip class="am-google-calendar-tooltip" effect="dark" placement="top">
              <div slot="content" v-html="$root.labels.social_login_facebook_configuration"></div>
              <el-button
                  class="am-google-calendar-button am-button-icon"
                  style="height: 48px;"
                  type="primary"
                  @click="redirectToDocumentation()"
              >
                <img class="svg-amelia" :src="$root.getUrl + 'public/img/question.svg'"/>
              </el-button>
            </el-tooltip>
          </el-col>
        </div>
      </div>
      <!-- /Facebook Social Login Enabled -->

      <!-- Facebook App Id and App Secret -->
      <div class="am-social-login__facebook-id-secret" v-if="settings.enableFacebookLogin">
        <!-- Facebook App Id -->
        <el-form-item :label="$root.labels.facebook_app_id+':'" prop="facebookAppId">
          <el-row :gutter="24">

            <el-col :span="24">
              <el-input v-model.trim="settings.facebookAppId" auto-complete="off"></el-input>
            </el-col>

          </el-row>
        </el-form-item>
        <!-- /Facebook App Id -->

        <!-- Facebook App Secret -->
        <el-form-item :label="$root.labels.facebook_app_secret+':'" prop="facebookAppSecret">
          <el-row :gutter="24">

            <el-col :span="24">
              <el-input v-model.trim="settings.facebookAppSecret" auto-complete="off"></el-input>
            </el-col>

          </el-row>
        </el-form-item>
        <!-- /Facebook App Secret -->

      </div>

    </div>
  </el-form>
  <!-- /Integration Social Login -->

</template>

<script>
export default {
  props: {
    socialLogin: {
      type: Object
    }
  },

  data () {
    return {
      settings: this.socialLogin,
      rules: {
        facebookAppId: [
          {
            validator: (rule, value, callback) => {
              if (this.settings.enableFacebookLogin && !value) {
                callback(new Error(this.$root.labels.facebook_app_id_required))
              } else {
                callback()
              }
            },
            trigger: 'blur'
          }
        ],
        facebookAppSecret: [
          {
            validator: (rule, value, callback) => {
              if (this.settings.enableFacebookLogin && !value) {
                callback(new Error(this.$root.labels.facebook_app_secret_required))
              } else {
                callback()
              }
            },
            trigger: 'blur'
          }
        ]
      }
    }
  },

  methods: {
    openDialog(name) {
      this.$emit('openDialog', name)
    },

    redirectToDocumentation () {
      window.open('https://wpamelia.com/social-login-google-facebook/', '_blank')
    },

    validate() {
      return new Promise((resolve) => {
        this.$refs.settings.validate(valid => {
          resolve(valid)
        })
      })
    }
  }
}

</script>