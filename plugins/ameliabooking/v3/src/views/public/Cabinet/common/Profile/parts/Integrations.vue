<template>
  <el-form
    v-if="!loading"
    ref="employeeFormRef"
    :model="employeeFormData"
    label-position="top"
    class="am-caedo"
    :class="props.responsiveClass"
  >
    <StripeConnect
      v-if="
        amSettings.payments.stripe.enabled &&
        amSettings.payments.stripe.connect.enabled
      "
    />

    <div class="am-caepif__block" :class="props.responsiveClass">
      <template
        v-for="(item, name) in employeeDataFormConstruction"
        :key="name"
      >
        <component
          :is="item.template"
          ref="customerCollectorRef"
          v-model="employeeFormData[name]"
          v-bind="item.props"
          v-on="item.handlers ? item.handlers : {}"
        >
          <template v-if="item.slots && item.slots.default">
            <div v-html="item.slots.default" />
          </template>
        </component>
      </template>
    </div>
  </el-form>
  <Skeleton v-else></Skeleton>
</template>

<script setup>
// * Import from Vue
import { computed, ref, inject, onBeforeMount, markRaw } from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import Form Templates
import { formFieldsTemplates } from '../../../../../../assets/js/common/formFieldsTemplates'
import AmButton from '../../../../../_components/button/AmButton.vue'
import StripeConnect from './StripeConnect.vue'

// * Composables
import {
  useGoogleConnect,
  useGoogleDisconnect,
} from '../../../../../../assets/js/common/integrationGoogle'
import {
  useOutlookConnect,
  useOutlookDisconnect,
} from '../../../../../../assets/js/common/integrationOutlook'
import Skeleton from '../../Authentication/parts/Skeleton.vue'

// * Props
const props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  },
})

// * Store
const store = useStore()

// * Root Urls
const baseUrls = inject('baseUrls')

// * Settings
const amSettings = inject('settings')

// * Labels
let amLabels = inject('amLabels')

let loading = computed(
  () =>
    store.getters['auth/getGoogleLoading'] ||
    store.getters['auth/getOutlookLoading'] ||
    store.getters['auth/getAppleLoading'] ||
    store.getters['auth/getStripeLoading'] ||
    store.getters['auth/getZoomLoading']
)

// * Zoom Users Options
let zoomOptions = computed(() => {
  let users = store.getters['auth/getZoomUsers']

  if (users.length) {
    return users.map((user) => {
      return {
        value: user.id,
        label: `${user.first_name} ${user.last_name} (${user.email})`,
      }
    })
  }

  return []
})

// * Google Calendar Options
let googleCalendarOptions = computed(() => {
  let calendars = store.getters['auth/getGoogleCalendars']

  if (calendars.length) {
    return calendars.map((calendar) => {
      return {
        value: calendar.calendarId,
        label: calendar.summary,
      }
    })
  }

  return []
})

// * Outlook Calendar Options
let outlookCalendarOptions = computed(() => {
  let calendars = store.getters['auth/getOutlookCalendars']

  if (calendars.length) {
    return calendars.map((calendar) => {
      return {
        value: calendar.id,
        label: calendar.name,
      }
    })
  }

  return []
})

// * Apple Calendar Options
let appleCalendarOptions = computed(() => {
  let calendars = store.getters['auth/getAppleCalendars']

  if (calendars.length) {
    return calendars.map((calendar) => {
      return {
        value: calendar.id,
        label: calendar.name,
      }
    })
  }

  return []
})

// * Google Button Text
let googleBtnText = computed(() => {
  return store.getters['employee/getGoogleToken']
    ? amLabels.google_sign_out
    : amLabels.google_sign_in
})

// * Outlook Button Text
let outlookBtnText = computed(() => {
  return store.getters['employee/getOutlookToken']
    ? amLabels.outlook_sign_out
    : amLabels.outlook_sign_in
})

// * Form Data
let employeeFormData = ref({
  googleId: computed({
    get: () => store.getters['employee/getGoogleCalendarId'],
    set: (val) => {
      store.commit('employee/setGoogleCalendarId', val ? val : '')
    },
  }),
  googleBtn: '',
  outlookId: computed({
    get: () => store.getters['employee/getOutlookCalendarId'],
    set: (val) => {
      store.commit('employee/setOutlookCalendarId', val ? val : '')
    },
  }),
  outlookBtn: '',
  appleId: computed({
    get: () => store.getters['employee/getAppleCalendarId'],
    set: (val) => {
      store.commit('employee/setAppleCalendarId', val ? val : '')
    },
  }),
  zoomUserId: computed({
    get: () => store.getters['employee/getZoomUserId'],
    set: (val) => {
      store.commit('employee/setZoomUserId', val ? val : '')
    },
  }),
})

let employeeDataFormConstruction = ref({
  googleId: {
    template: formFieldsTemplates.select,
    props: {
      itemName: 'googleId',
      label: amLabels.google_calendar,
      placeholder: '',
      class: computed(() => `am-caepif__item ${props.responsiveClass}`),
      disabled: computed(() => !store.getters['employee/getGoogleToken']),
      clearable: false,
      options: googleCalendarOptions.value,
      loading: computed(() => store.getters['auth/getGoogleLoading']),
      loadingIcon: 'loading',
    },
  },
  googleBtn: {
    template: markRaw(AmButton),
    props: {
      class: computed(
        () =>
          `am-caepif__item am-google-calendar-button ${props.responsiveClass}`
      ),
      category: computed(() => 'primary'),
    },
    slots: {
      default: computed(
        () => {
          return `<img src="${baseUrls.wpAmeliaPluginURL}/v3/src/assets/img/cabinet/google-button.svg" alt="Google" />${googleBtnText.value}`
        }),
    },
    handlers: {
      click: () => {
        store.getters['employee/getGoogleToken']
          ? useGoogleDisconnect(store)
          : useGoogleConnect(store)
      },
    },
  },
  outlookId: {
    template: formFieldsTemplates.select,
    props: {
      itemName: 'outlookId',
      label: amLabels.outlook_calendar,
      placeholder: '',
      class: computed(() => `am-caepif__item ${props.responsiveClass}`),
      disabled: computed(() => !store.getters['employee/getOutlookToken']),
      clearable: false,
      options: outlookCalendarOptions.value,
      loading: computed(() => store.getters['auth/getOutlookLoading']),
      loadingIcon: 'loading',
    },
  },
  outlookBtn: {
    template: markRaw(AmButton),
    props: {
      class: computed(() => `am-caepif__item am-outlook-button ${props.responsiveClass}`),
      category: computed(() => 'primary'),
    },
    slots: {
      default: computed(() => {
        return `<div class="am-outlook-img"><img src="${baseUrls.wpAmeliaPluginURL}/v3/src/assets/img/cabinet/outlook-calendar.png" alt="Outlook"/></div>${outlookBtnText.value}`
      }),
    },
    handlers: {
      click: () => {
        store.getters['employee/getOutlookToken']
          ? useOutlookDisconnect(store)
          : useOutlookConnect(store)
      },
    },
  },
  appleId: {
    template: formFieldsTemplates.select,
    props: {
      itemName: 'appleId',
      label: amLabels.apple_calendar,
      placeholder: '',
      class: computed(() => `am-caepif__item ${props.responsiveClass}`),
      disabled: false,
      clearable: true,
      options: appleCalendarOptions.value,
      loading: computed(() => store.getters['auth/getAppleLoading']),
      loadingIcon: 'loading',
    },
  },
  zoomUserId: {
    template: formFieldsTemplates.select,
    props: {
      itemName: 'zoomUserId',
      label: amLabels.zoom_user,
      placeholder: amLabels.zoom_user_placeholder,
      class: computed(() => `am-caepif__item ${props.responsiveClass}`),
      options: zoomOptions.value,
    },
  },
})

onBeforeMount(() => {
  if (!amSettings.zoom.enabled) {
    delete employeeDataFormConstruction.value.zoomUserId
  }

  if (!amSettings.googleCalendar.enabled) {
    delete employeeDataFormConstruction.value.googleId
    delete employeeDataFormConstruction.value.googleBtn
  }

  if (!amSettings.outlookCalendar.enabled) {
    delete employeeDataFormConstruction.value.outlookId
    delete employeeDataFormConstruction.value.outlookBtn
  }

  if (!amSettings.appleCalendar) {
    delete employeeDataFormConstruction.value.appleId
  }
})
</script>

<style lang="scss">
@mixin am-cabinet-profile {
  .am-caedo {
    .am-google-calendar-button {
      padding: 0;
      height: 40px;
      border: 1px solid #747775 !important;
      margin: inherit;
      background-color: #ffffff !important;
      -webkit-border-radius: 1px;
      border-radius: 1px;
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
      -webkit-transition: background-color 0.218s, border-color 0.218s,
        box-shadow 0.218s;
      transition: background-color 0.218s, border-color 0.218s,
        box-shadow 0.218s;
      -webkit-user-select: none;
      -webkit-appearance: none;
      background-image: none;
      cursor: pointer;
      outline: none;
      overflow: hidden;
      position: relative;
      text-align: center;
      vertical-align: middle;
      white-space: nowrap;

      &:hover {
        box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3),
          0 1px 3px 1px rgba(60, 64, 67, 0.15) !important;
        background-color: rgba(48, 48, 48, 0.08) !important;
      }

      .am-button__inner {
        color: #131314;

        img {
          position: absolute;
          left: 10px;
          top: 50%;
          transform: translateY(-50%);
          width: 20px;
          height: 20px;
        }
      }
    }

    .am-outlook-button {
      position: relative;

      .am-outlook-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 40px;
        height: 100%;
        border-radius: 4px 0 0 4px;
        background-color: #fff;

        img {
          position: absolute;
          top: 50%;
          transform: translateY(-50%);
          left: 10px;
          width: 20px;
          height: 20px;
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-profile;
}
</style>
