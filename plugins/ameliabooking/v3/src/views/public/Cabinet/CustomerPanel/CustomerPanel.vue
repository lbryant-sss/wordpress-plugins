<template>
  <template v-if="!amFonts.customFontSelected">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" type="text/css" :href="`${baseUrls.wpAmeliaPluginURL}v3/src/assets/scss/common/fonts/font.css`" media="all">
  </template>
  <div
    id="amelia-container"
    ref="ameliaContainer"
    class="am-cap__wrapper"
    :class="[{'am-collapsed': sidebarCollapsed}, {'am-auth': !authenticated}]"
    :style="cssVars"
  >
    <Auth v-if="!authenticated"></Auth>
    <template v-if="authenticated">
      <SideBar
        v-if="sidebarVisibility"
        class="am-fs-sb"
        :class="{'am-collapsed': sidebarCollapsed}"
        :style="{width: !sidebarCollapsed ? '240px' : '72px', paddingBottom: `${sidebarFooterHeight + 16}px` }"
      >
        <template #step-list>
          <div class="am-fs-sb__page-wrapper am-fs-sb__page-wrapper__cabinet">
            <template
              v-for="(step, index) in sidebarSteps"
              :key="step.key"
            >
              <div
                v-if="step.key !== 'packages' || store.getters['entities/getPackages'].length"
                class="am-fs-sb__page"
                :class="{'selected': pageKey === step.key}"
                @click="sidebarSelection(step, index)"
              >
                <div
                  class="am-fs-sb__page-inner"
                  :class="{'am-collapsed': sidebarCollapsed}"
                >
                  <div class="am-fs-sb__page-icon">
                    <span :class="`am-icon-${step.icon}`"></span>
                  </div>
                  <transition name="fade">
                    <p
                      v-if="!sidebarCollapsed"
                      class="am-fs-sb__page-heading"
                      :class="sidebarCollapseItemsClass"
                    >
                      {{ step.label }}
                    </p>
                  </transition>
                  <transition name="fade">
                    <div
                      v-if="!sidebarCollapsed"
                      class="am-fs-sb__page-indicator"
                      :class="sidebarCollapseItemsClass"
                    >
                      <span class="am-icon-arrow-big-right"></span>
                    </div>
                  </transition>
                </div>
              </div>
              <div
                v-if="index === 0"
                class="am-fs-sb__page-divider"
              ></div>
            </template>
          </div>
        </template>
        <template #support-info>
          <div
            ref="sidebarFooterRef"
            class="am-fs-sb__footer"
          >
            <div
              class="am-fs-sb__page"
              @click="sidebarCollapsed = !sidebarCollapsed"
            >
              <div class="am-fs-sb__page-inner">
                <div class="am-fs-sb__page-icon">
                  <span class="am-icon-dashboard"></span>
                </div>
                <transition name="fade">
                  <p
                    v-if="!sidebarCollapsed"
                    class="am-fs-sb__page-heading"
                    :class="sidebarCollapseItemsClass"
                  >
                    {{ amLabels.toggle_sidebar }}
                  </p>
                </transition>
              </div>
            </div>
            <div class="am-fs-sb__page-divider"></div>
            <div
              class="am-fs-sb__page"
              @click="logout"
            >
              <div class="am-fs-sb__page-inner">
                <div class="am-fs-sb__page-icon">
                  <span class="am-icon-logout"></span>
                </div>
                <transition name="fade">
                  <p
                    v-if="!sidebarCollapsed"
                    class="am-fs-sb__page-heading"
                    :class="sidebarCollapseItemsClass"
                  >
                    {{amLabels.log_out}}
                  </p>
                </transition>
              </div>
            </div>
          </div>
        </template>
      </SideBar>
      <MainContent
        :max-width="786"
        :old-responsive="false"
      >
        <template #header>
          <MainPanelHeader
            :ready="ready"
          >
            <template #default>
              <div class="am-caph">
                <div class="am-caph__text">
                  {{ sidebarSteps[sidebarIndex].key === 'profile' ? sidebarSteps[sidebarIndex].pageLabel : sidebarSteps[sidebarIndex].label }}
                </div>
                <div
                  v-if="!sidebarVisibility"
                  class="am-caph__menu"
                  @click="() => menuVisibility = !menuVisibility"
                >
                  <span class="am-icon-menu"></span>
                </div>
                <TimeZoneSelect v-if="sidebarVisibility && pageKey !== 'profile' && customizedOptions.timeZone.visibility"/>
                <MenuSlideDialog
                  :menu-items="sidebarSteps"
                  :monitor="pageKey"
                  :visibility="menuVisibility"
                  :custom-css="cssSideMenu"
                  :customized-labels="amLabels"
                  position="right"
                  :width="240"
                  @update:visibility="(e) => menuVisibility = e"
                  @click="menuSelection"
                  @logout="menuLogout"
                />
              </div>
            </template>
          </MainPanelHeader>
        </template>
        <template #step>
          <component
            :is="pagesObj[pageKey]"
            class="am-fs__main-content"
            :load-bookings-counter="loadBookingsCounter"
          ></component>
        </template>
      </MainContent>
    </template>
  </div>
</template>

<script setup>
// * Form construction
import MainContent from "../../../common/SbsFormConstruction/MainContent/MainContent.vue";
import SideBar from "../../../common/SbsFormConstruction/SideBar/SideBar.vue";
import MainPanelHeader from "../../../common/SbsFormConstruction/MainContent/parts/MainPanelHeader.vue";

// * Dedicated Components
import TimeZoneSelect from "../common/parts/TimeZoneSelect.vue";
import MenuSlideDialog from "../../../common/SbsFormConstruction/MenuSlideDialog/MenuSlideDialog.vue";

// * import from Vue
import {
  ref,
  reactive,
  watch,
  provide,
  inject,
  markRaw,
  computed,
  onMounted,
  readonly,
  onBeforeMount
} from 'vue'

// * import from Vuex
import { useStore } from 'vuex'

// * import composable
import {
  defaultCustomizeSettings
} from '../../../../assets/js/common/defaultCustomize.js'
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation.js'
import { useCurrentTimeZone } from "../../../../assets/js/common/helper";
import { useElementSize } from "@vueuse/core";

// * Form Component Collection
import Auth from "../common/Authentication/Auth.vue";
import Profile from "../common/Profile/Profile.vue";
import Appointments from "../common/Appointments/Appointments.vue";
import Events from "../common/Events/Events.vue";
import Packages from "../common/Packages/Packages.vue";

// * Import from Libraries
import { useCookies } from 'vue3-cookies'

// * Vars
const vueCookies = useCookies()['cookies']

const store = useStore()

// * Authenticated
let authenticated = computed(() => {
  return store.getters['auth/getAuthenticated']
})

watch(authenticated, (current) => {
  if (current) {
    store.commit('auth/setNewPassword', '')
    store.commit('auth/setConfirmPassword', '')
  }
})

// * Cabinet Type
let cabinetType = ref('customer')
provide('cabinetType', cabinetType)

// * Plugin Licence
let licence = inject('licence')

// * Origin key
let originKey = ref('capc')
provide('originKey', originKey)

// * Component reference
let ameliaContainer = ref(null)

// * Plugin wrapper width
let { width: containerWidth } = useElementSize(ameliaContainer)
provide('containerWidth', containerWidth)

// * Root Settings
const amSettings = inject('settings')

// * Customize
const amCustomize = computed(() => {
  return amSettings.customizedData && 'capc' in amSettings.customizedData ? amSettings.customizedData.capc : defaultCustomizeSettings.capc
})
provide('amCustomize', amCustomize)

// * Fonts
const amFonts = ref(amSettings.customizedData ? amSettings.customizedData.fonts : defaultCustomizeSettings.fonts)
provide('amFonts', amFonts)

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  if (amSettings.customizedData) {
    Object.keys(amCustomize.value).forEach(stepKey => {
      if (stepKey !== 'colors' && amCustomize.value[stepKey].translations) {
        let customizedLabels = amCustomize.value[stepKey].translations
        Object.keys(customizedLabels).forEach(labelKey => {
          if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
            computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
          } else if (customizedLabels[labelKey].default) {
            computedLabels[labelKey] = customizedLabels[labelKey].default
          }
        })
      }
    })
  }
  return computedLabels
})

// * Custmozes Options
let customizedOptions = computed(() => {
  if (pageKey.value === 'packages') return amCustomize.value.packagesList.options
  return amCustomize.value[pageKey.value].options
})

// * Mobile menu
let menuVisibility = ref(false)

// * Form Sidebar Collapse
let sidebarCollapsed = ref(false)
provide('sidebarCollapsed', readonly(sidebarCollapsed))

let sidebarCollapseItemsClass = ref('')

watch(sidebarCollapsed ,(current) => {
  if (current) {
    setTimeout(() => {
      sidebarCollapseItemsClass.value = 'am-collapsed'
    }, 1000)
  } else {
    sidebarCollapseItemsClass.value = ''
  }
})

let sidebarFooterRef = ref(null)
let sidebarFooterHeight = ref(0)

// * Form Sidebar Visibility
let sidebarVisibility = computed(() => containerWidth.value > 480)

onMounted(() => {
  if(sidebarFooterRef.value) {
    setTimeout(() => {
      sidebarFooterHeight.value = sidebarFooterRef.value.offsetHeight
    }, 200)
  }
})

let widthChange = ref(null)
watch(containerWidth, (current) => {
  clearTimeout(widthChange.value)
  widthChange.value = setTimeout(() => {
    sidebarCollapsed.value = !amCustomize.value.sidebar.options.toggle.visibility ? current <= 600 : amCustomize.value.sidebar.options.toggle.visibility
    menuVisibility.value = current > 481 ? false : menuVisibility.value
  }, 200)
})

// * Root Urls
const baseUrls = inject('baseUrls')

store.dispatch(
  'entities/getEntities',
  {
    types: [
      'employees',
      'categories',
      'locations',
      'packages',
      'entitiesRelations',
      'customFields',
    ],
    licence: 'basic',
    loadEntities: true,
    showHidden: true,
  }
)

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * Data loaded
let ready = computed(() => store.getters['getReady'])

let pageKey = ref(shortcodeData.value.appointments || !shortcodeData.value.events ? 'appointments' : 'events')

// * Panel Fluid Pages
let pagesObj = ref({
  profile: markRaw(Profile),
})

if (shortcodeData.value.appointments || !shortcodeData.value.events) {
  pagesObj.value.appointments = markRaw(Appointments)
}

if (shortcodeData.value.events || !shortcodeData.value.appointments) {
  pagesObj.value.events = markRaw(Events)
}

if ((shortcodeData.value.appointments || !shortcodeData.value.events) && (licence.isPro || licence.isDeveloper)) {
  pagesObj.value.packages = markRaw(Packages)
}

// * Array of Sidebar steps
const sidebarSteps = ref([
  {
    key: 'profile',
    icon: 'user',
    pageLabel: amLabels.value.my_profile,
    label: computed(() => {
      if (!amCustomize.value.profile.options.lastName.visibility) return `${store.getters['auth/getProfile'].firstName}`
      return `${store.getters['auth/getProfile'].firstName} ${store.getters['auth/getProfile'].lastName}`
    }),
  }
])

if (shortcodeData.value.appointments || !shortcodeData.value.events) {
  sidebarSteps.value.push(
    {
      key: 'appointments',
      icon: 'service',
      label: amLabels.value.appointments,
    }
  )
}

if (shortcodeData.value.events || !shortcodeData.value.appointments) {
  sidebarSteps.value.push(
    {
      key: 'events',
      icon: 'star-outline',
      label: amLabels.value.events,
    }
  )
}

if ((shortcodeData.value.appointments || !shortcodeData.value.events) && (licence.isPro || licence.isDeveloper)) {
  sidebarSteps.value.push(
    {
      key: 'packages',
      icon: 'shipment',
      label: amLabels.value.packages,
    }
  )
}

provide('sidebarSteps', sidebarSteps)

onMounted(() => {
  sidebarIndex.value = sidebarSteps.value.findIndex(a => a.key === pageKey.value)
})

let sidebarIndex = ref(0)

function sidebarSelection (step, index) {
  pageKey.value = step.key
  sidebarIndex.value = index
  store.commit('cabinetFilters/setResetFilters')
}

function menuSelection (obj) {
  sidebarSelection(obj.step, obj.index)
  store.commit('cabinetFilters/setResetFilters')
}

function logout () {
  store.dispatch('auth/logout')
}

function menuLogout () {
  menuVisibility.value = false
  logout()
}

let loadBookingsCounter = ref(0)

function bookingsCounterChanger () {
  loadBookingsCounter.value++
}

provide('bookingsCounterChanger', {
  bookingsCounterChanger
})

const adminTimeZone = inject('timeZone')

onBeforeMount(() => {
  if (!amSettings.general.showClientTimeZone) {
    let initialTimeZone = adminTimeZone.value

    if (vueCookies.get('ameliaUserTimeZone')) {
      initialTimeZone = vueCookies.get('ameliaUserTimeZone')
    }

    store.commit('cabinet/setTimeZone', initialTimeZone)
  }

  if (!store.getters['cabinet/getTimeZone']) store.commit('cabinet/setTimeZone', useCurrentTimeZone())
})

// * Colors block
let amColors = computed(() => {
  return amCustomize.value.colors
})
provide('amColors', amColors);

let cssVars = computed(() => {
  return {
    '--am-c-primary': amColors.value.colorPrimary,
    '--am-c-success': amColors.value.colorSuccess,
    '--am-c-error': amColors.value.colorError,
    '--am-c-warning': amColors.value.colorWarning,
    '--am-c-main-bgr': amColors.value.colorMainBgr,
    '--am-c-main-heading-text': amColors.value.colorMainHeadingText,
    '--am-c-main-text': amColors.value.colorMainText,
    '--am-c-sb-bgr': amColors.value.colorSbBgr,
    '--am-c-sb-text': amColors.value.colorSbText,
    '--am-c-inp-bgr': amColors.value.colorInpBgr,
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-inp-text': amColors.value.colorInpText,
    '--am-c-inp-placeholder': amColors.value.colorInpPlaceHolder,
    '--am-c-drop-bgr': amColors.value.colorDropBgr,
    '--am-c-drop-text': amColors.value.colorDropText,
    '--am-c-btn-prim': amColors.value.colorBtnPrim,
    '--am-c-btn-prim-text': amColors.value.colorBtnPrimText,
    '--am-c-btn-sec': amColors.value.colorBtnSec,
    '--am-c-btn-sec-text': amColors.value.colorBtnSecText,
    '--am-c-btn-danger': amColors.value.colorBtnDanger,
    '--am-c-btn-danger-text': amColors.value.colorBtnDangerText,
    '--am-c-skeleton-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-skeleton-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-skeleton-sb-op20': useColorTransparency(amColors.value.colorSbText, 0.2),
    '--am-c-skeleton-sb-op60': useColorTransparency(amColors.value.colorSbText, 0.6),
    '--am-c-scroll-op30': useColorTransparency(amColors.value.colorPrimary, 0.30),
    '--am-c-scroll-op10': useColorTransparency(amColors.value.colorPrimary, 0.10),
    '--am-font-family': amFonts.value.fontFamily,

    // css properties
    '--am-rad-input': '6px',
    '--am-fs-input': '15px',
    // -mw- max width
    // -brad- border-radius
    '--am-mw-main': sidebarVisibility.value ? sidebarCollapsed.value ? '858px' : '1024px' : '520px',
    '--am-brad-main': sidebarVisibility.value ? '0 0.5rem 0.5rem 0' : '0.5rem'
  }
})

let cssSideMenu = computed(() => {
  return {
    '--am-c-msd-bgr': amColors.value.colorSbBgr,
    '--am-c-msd-text': amColors.value.colorSbText,
    '--am-c-msd-text-op05': useColorTransparency(amColors.value.colorSbText, 0.05),
    '--am-c-msd-text-op10': useColorTransparency(amColors.value.colorSbText, 0.1),
    '--am-c-msd-text-op60': useColorTransparency(amColors.value.colorSbText, 0.6),
  }
})
</script>

<script>

export default {
  name: "CustomerPanel",
}
</script>

<style lang="scss">
@import '../../../../assets/scss/public/overides/overides';
@import '../../../../assets/scss/common/reset/reset';
@import '../../../../assets/scss/common/icon-fonts/style';
@import '../../../../assets/scss/common/skeleton/skeleton.scss';
@import '../../../../assets/scss/common/empty-state/_empty-state-mixin.scss';
@import '../../../../assets/scss/common/transitions/_transitions-mixin.scss';

:root {
  // Colors
  // shortcuts
  // -c-    color
  // -bgr-  background
  // -prim- primary
  // -sec-  secondary
  // primitive colors
  --am-c-primary: #{$blue-1000};
  --am-c-success: #{$green-1000};
  --am-c-error: #{$red-900};
  --am-c-warning: #{$yellow-1000};
  // main container colors - right part of the form
  --am-c-main-bgr: #{$am-white};
  --am-c-main-heading-text: #{$shade-800};
  --am-c-main-text: #{$shade-900};
  // sidebar container colors - left part of the form
  --am-c-sb-bgr: #17295A;
  --am-c-sb-text: #{$am-white};
  // input global colors - usage input, textarea, checkbox, radio button, select input, adv select input
  --am-c-inp-bgr: #{$am-white};
  --am-c-inp-border: #{$shade-250};
  --am-c-inp-text: #{$shade-900};
  --am-c-inp-placeholder: #{$shade-500};
  // dropdown global colors - usage select dropdown, adv select dropdown
  --am-c-drop-bgr: #{$am-white};
  --am-c-drop-text: #{$shade-1000};
  // button global colors
  --am-c-btn-prim: #{$blue-900};
  --am-c-btn-prim-text: #{$am-white};
  --am-c-btn-sec: #{$am-white};
  --am-c-btn-sec-text: #{$shade-900};

  // Properties
  // shortcuts
  // -h- height
  // -fs- font size
  // -rad- border radius
  --am-h-input: 40px;
  --am-fs-input: 15px;
  --am-rad-input: 6px;
  --am-fs-label: 15px;
  --am-fs-btn: 15px;

  // Font
  --am-font-family: 'Amelia Roboto', sans-serif;
}

.amelia-v2-booking {
  background-color: transparent;

  #amelia-container {
    background-color: transparent;

    * {
      font-family: var(--am-font-family);
      font-style: initial;
      box-sizing: border-box;
      word-break: break-word;
    }

    // cap - cabinet panel
    &.am-cap {
      // Container Wrapper
      &__wrapper {
        display: flex;
        justify-content: center;
        max-width: var(--am-mw-main);
        width: 100%;
        height: 700px;
        margin: 100px auto;
        border-radius: 8px;
        box-shadow: 0 30px 40px rgba(0, 0, 0, 0.12);
        transition: max-width 0.3s ease-in-out;

        &.am-auth {
          box-shadow: unset;
          height: auto;
        }

        &.am-collapsed {
          transition-delay: 1s;
        }

        * {
          font-family: var(--am-font-family);
          box-sizing: border-box;
        }
      }
    }

    .am-cap {
      &__header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        .am-select-wrapper {
          max-width: 250px;
        }
      }
    }

    .am-asi {
      .el-form {
        &-item {
          display: block;
          font-family: var(--am-font-family);
          font-size: var(--am-fs-label);
          margin-bottom: 30px;

          &__label {
            flex: 0 0 auto;
            text-align: left;
            font-size: var(--am-fs-label);
            line-height: 1.3;
            color: var(--am-c-main-text);
            box-sizing: border-box;
            margin: 0;

            &:before {
              color: var(--am-c-error);
            }
          }

          &__content {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            flex: 1;
            position: relative;
            font-size: var(--am-fs-input);
            min-width: 0;
          }

          &__error {
            font-size: 12px;
            color: var(--am-c-error);
            padding-top: 4px;
          }
        }
      }
    }
  }
}
</style>
