<template>
  <div
    ref="pageContainer"
    class="am-cap"
    :style="cssVars"
  >
    <AmAlert
      v-if="alertVisibility"
      ref="alertContainer"
      :type= "alertType"
      :show-border="true"
      :close-after="5000"
      custom-class="am-cap__alert"
      @close="closeAlert"
      @trigger-close="closeAlert"
    >
      <template #title>
        <span class="am-icon-checkmark-circle-full"></span> {{ alertMessage }}
      </template>
    </AmAlert>

    <CabinetFilters
      :step-key="'events'"
      :empty="empty"
      :responsive-class="responsiveClass"
      @change-filters="getEvents"
    />

    <template v-if="!loading">
      <div
        v-if="dateGroupedEvents && Object.keys(dateGroupedEvents).length > 0"
        class="am-cape__wrapper"
        :class="[{'am-no-border': dateGroupedEvents && Object.keys(dateGroupedEvents).length === 1}, responsiveClass]"
      >
        <div
          v-for="(item, dateKey) in dateGroupedEvents"
          :key="dateKey"
          class="am-cape"
        >
          <div
            class="am-cape__date"
            :class="[
              {'am-today': getFrontedFormattedDate(dateKey) === getFrontedFormattedDate(moment().format('YYYY-MM-DD'))},
              {'am-no-flag': dateGroupedEvents && Object.keys(dateGroupedEvents).length === 1},
              responsiveClass
            ]"
          >
            {{getFrontedFormattedDate(dateKey)}}
          </div>
          <CollapseCard
            v-for="(event, index) in item.events"
            :key="index"
            :start="getFrontedFormattedTime(event.periods[0].periodStart.split(' ')[1].slice(0, 5))"
            :name="event.name"
            :employee="eventEmployees(event)"
            :price="useEventBookingsPrice(event)"
            :duration="null"
            :periods="(periods = usePeriodsData(event.periods)) ? periods : []"
            :extras="[]"
            :tickets="useTicketsData(event)"
            :custom-fields="useCustomFieldsData(event.bookings)"
            :location="useEventLocation(store, event)"
            :google-meet-link="event.periods.length === 1 && event.periods[0].googleMeetUrl ? event.periods[0].googleMeetUrl : ''"
            :zoom-link="event.periods.length === 1 && event.periods[0].zoomMeeting ? event.periods[0].zoomMeeting.joinUrl : ''"
            :lesson-space-link="event.periods.length === 1 && event.periods[0].lessonSpace ? event.periods[0].lessonSpace : ''"
            :bookable="event"
            :reservation="event"
            :booking="event.bookings[0]"
            :responsive-class="responsiveClass"
            :parent-width="pageWidth"
            :customized-options="customizedOptions('events')"
            @cancel-booking="(data) => {
            targetBooking = data
          }"
          ></CollapseCard>
        </div>
      </div>

      <EmptyState
        v-if="dateGroupedEvents === null || Object.keys(dateGroupedEvents).length === 0"
        :heading="amLabels.no_evt_found"
        :text="amLabels.have_no_evt"
      ></EmptyState>

      <CancelPopup
        :visibility="targetBooking !== null"
        :title="customizedLabels('cancelEvent').cancel_event"
        :description="customizedLabels('cancelEvent').confirm_cancel_event"
        :close-btn-text="customizedLabels('cancelEvent').close"
        :confirm-btn-text="customizedLabels('cancelEvent').confirm"
        :customized-options="customizedOptions('cancelEvent')"
        :loading="waitingForCancelation"
        @close="targetBooking = null"
        @confirm="cancelBooking"
      >
      </CancelPopup>
    </template>
    <Skeleton v-else></Skeleton>
  </div>
</template>

<script setup>
// * import from Vue
import {
  ref,
  reactive,
  computed,
  inject,
  provide,
  onMounted,
  watch,
  nextTick,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import from Libraries
import httpClient from "../../../../../plugins/axios";

// * Templates
import CancelPopup from "../../common/parts/CancelPopup.vue";
import CollapseCard from "../parts/CollapseCard/CollapseCard.vue";
import CabinetFilters from "../parts/Filters.vue";
import Skeleton from "../../common/parts/Skeleton.vue";

// * Composables
import {
  useAuthorizationHeaderObject
} from "../../../../../assets/js/public/panel";
import {
  getDateRange,
  getFrontedFormattedDate,
  getFrontedFormattedTime
} from "../../../../../assets/js/common/date";
import {
  useParsedEvents,
  useEventBookingsPrice,
  usePeriodsData,
  useTicketsData,
  useEventLocation,
} from "../../../../../assets/js/admin/event";
import {
  useCustomFieldsData,
} from "../../../../../assets/js/admin/booking";
import {
  useResponsiveClass
} from "../../../../../assets/js/common/responsive";
import {
  useColorTransparency
} from "../../../../../assets/js/common/colorManipulation";
import {
  useUrlParams
} from "../../../../../assets/js/common/helper";
import moment from "moment";
import EmptyState from "../parts/EmptyState.vue";
import AmAlert from "../../../../_components/alert/AmAlert.vue";
import {useScrollTo} from "../../../../../assets/js/common/scrollElements";

// * Store
let store = useStore()

// * Page Content width
let pageContainer = ref(null)
let pageWidth = ref(0)

// * Sidebar collapsed var
let sidebarCollapsed = inject('sidebarCollapsed')

// * window resize listener
window.addEventListener('resize', resize);
// * resize function
function resize() {
  if (pageContainer.value) {
    pageWidth.value = pageContainer.value.offsetWidth
  }
}

watch(sidebarCollapsed, (current) => {
  if (current) {
    setTimeout(() => {
      collapseTriggered()
    }, 1500)
  } else {
    setTimeout(() => {
      collapseTriggered()
    }, 500)
  }
})

function collapseTriggered () {
  pageWidth.value = pageContainer.value.offsetWidth
}

onMounted(() => {
  nextTick(() => {
    pageWidth.value = pageContainer.value.offsetWidth
  })
})

let responsiveClass = computed(() => {
  return useResponsiveClass(pageWidth.value)
})

// * Root Settings
const amSettings = inject('settings')

// * Customized form data
let amCustomize = inject('amCustomize')

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  let customizedLabels = amCustomize.value.events.translations
  if (customizedLabels) {
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

provide('amLabels', amLabels)

// * Alert block
let alertContainer = ref(null)
let alertVisibility = ref(false)
let alertType = ref('success')

let alertMessage = ref('')

function closeAlert () {
  alertVisibility.value = false
  store.commit('cabinet/setPaymentLinkError', {value: false, type: 'event'})
}

function customizedLabels (step) {
  let computedLabels = reactive({...labels})

  let customizedLabels = amCustomize.value[step].translations
  if (customizedLabels) {
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
}

function customizedOptions (step) {
  return amCustomize.value[step].options
}

/********
 * Form *
 ********/
let props = defineProps({
  loadBookingsCounter: {
    type: Number,
    default: 0
  },
})

// * Cabinet type
let cabinetType = inject('cabinetType')
store.commit('cabinetFilters/setDates', getDateRange(cabinetType.value))

// * Loading
let loading = computed(() => store.getters['cabinet/getEventsLoading'])

let empty = ref(false)

let targetBooking = ref(null)

let dateGroupedEvents = ref(null)

function getEvents () {
  targetBooking.value = null

  store.commit('cabinet/setEventsLoading', true)

  let timeZone = store.getters['cabinet/getTimeZone']
  let params = JSON.parse(JSON.stringify(store.getters['cabinetFilters/getEventsFilters']))
  params.dates = params.dates.map(d => moment(d).format('YYYY-MM-DD'))
  params.timeZone = timeZone
  params.source = 'cabinet-' + cabinetType.value
  params.id = params.events

  httpClient.get(
    '/events',
      useUrlParams(
        Object.assign(
          useAuthorizationHeaderObject(store),
          {params: params}
        )
      )
  ).then((response) => {
    store.commit('eventEntities/setEvents', response.data.data.events)
    dateGroupedEvents.value = useParsedEvents(
      response.data.data.events,
      store.getters['cabinet/getTimeZone'],
      store
    )

    let filterEmpty = !(params.events.length || params.providers.length || params.locations.length)
    empty.value = (!dateGroupedEvents.value || Object.values(dateGroupedEvents.value).length === 0) && filterEmpty

    if (dateGroupedEvents.value && Object.values(dateGroupedEvents.value).length > 0 && filterEmpty) {
      store.dispatch('cabinetFilters/injectEventsOptions')

      let providerIds = Object.values(dateGroupedEvents.value)
        .map(a => a.events.map(ap => ap.organizerId)).flat()
        .concat(Object.values(dateGroupedEvents.value).map(a => a.events.map(ap => ap.providers).flat().map(p => p.id)).flat())
        .filter((item, index, array) => array.indexOf(item) === index)
      store.dispatch('cabinetFilters/injectProviderOptions', providerIds)

      let locationIds = Object.values(dateGroupedEvents.value).map(a => a.events.map(ap => ap.locationId)).flat().filter((item, index, array) => array.indexOf(item) === index)
      store.dispatch('cabinetFilters/injectLocationOptions', locationIds)
    }
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('cabinet/setEventsLoading', false)
  })
}

let waitingForCancelation = ref(false)
function cancelBooking () {
  waitingForCancelation.value = true
  httpClient.post(
    '/bookings/cancel/' + targetBooking.value.id,
    {
      type: 'event',
      source: 'cabinet-' + cabinetType.value,
    },
    Object.assign(useAuthorizationHeaderObject(store), {params: {source: 'cabinet-' + cabinetType.value}})
  ).then(() => {
    targetBooking.value = null
    getEvents()

    alertVisibility.value = true
    alertMessage.value = amLabels.value.event_canceled

    if (pageContainer.value && alertContainer.value) {
      setTimeout(function () {
        useScrollTo(pageContainer.value, alertContainer.value.$el, 0, 300)
      }, 500)
    }
  }).catch((error) => {
    if (!('data' in error.response.data) && 'message' in error.response.data) {
      alertVisibility.value = true
      alertMessage.value = error.response.data.message

      if (pageContainer.value && alertContainer.value) {
        setTimeout(function () {
          useScrollTo(pageContainer.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }
    }
  }).finally(() => {
    waitingForCancelation.value = false
  })
}

watch(() => props.loadBookingsCounter, () => {
  getEvents()
})

onMounted(() => {
  getEvents()
})

// * Event oraganizer
function eventEmployees (evt) {
  let arr = []
  let employees = store.getters['entities/getEmployees']

  if (evt.organizerId) {
    if(employees.find(item => item.id === evt.organizerId)) {
      let organizer = {...employees.find(item => item.id === evt.organizerId), ...{rank: 'organizer'}}
      if (!evt.providers.length) return organizer
      arr.push(organizer)
    }
  }

  if (evt.providers.length) {
    evt.providers.forEach((pro) => {
      if(pro.id !== evt.organizerId && employees.find(item => item.id === pro.id)) {
        arr.push(employees.find(item => item.id === pro.id))
      }
    })
  }

  return arr
}

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-cape-bgr': amColors.value.colorMainBgr,
    '--am-c-cape-text': amColors.value.colorMainText,
    '--am-c-cape-text-op70': useColorTransparency(amColors.value.colorMainText, 0.7),
    '--am-c-cape-text-op25': useColorTransparency(amColors.value.colorMainText, 0.25),
    '--am-c-cape-primary': amColors.value.colorPrimary,
  }
})
</script>

<script>
export default {
  name: 'CabinetEvents',
  key: 'events'
}
</script>

<style lang="scss">
@mixin am-cabinet-events {
  // cape -- cabinet panel events
  .am-cape {
    position: relative;

    &__wrapper {
      position: relative;
      padding: 0 0 0 32px;

      &:before {
        content: '';
        display: block;
        width: 1px;
        height: calc(100% - 4px);
        position: absolute;
        top: 4px;
        left: 8px;
        background-image: linear-gradient(180deg, transparent, transparent 50%, var(--am-c-cape-bgr) 50%, var(--am-c-cape-bgr) 100%), linear-gradient(180deg, var(--am-c-cape-text-op25), var(--am-c-cape-text-op25), var(--am-c-cape-text-op25), var(--am-c-cape-text-op25));
        background-size: 3px 12px, 100% 20px;
      }

      &.am-no-border {
        padding: 0;

        &:before {
          display: none;
        }
      }

      &.am-rw-500 {
        padding: 0;

        &:before {
          display: none;
        }
      }
    }

    &__date {
      font-size: 15px;
      font-weight: 500;
      line-height: 1.6;
      color: var(--am-c-cape-text-op70);
      margin: 24px 0 8px;

      &:before {
        content: '';
        display: block;
        width: 16px;
        height: 16px;
        position: absolute;
        top: 4px;
        left: -32px;
        background-color: var(--am-c-cape-text-op70);
        border-radius: 50%;
        z-index: 2;
      }

      &:after {
        content: '';
        display: block;
        width: 16px;
        height: 16px;
        position: absolute;
        top: 4px;
        left: -32px;
        background-color: var(--am-c-cape-bgr);
        border-radius: 50%;
        z-index: 1;
      }

      &.am-today {
        &:before {
          background-color: var(--am-c-cape-primary);
        }
      }

      &.am-no-flag {
        &:before, &:after {
          display: none;
        }
      }

      &.am-rw-500 {
        display: flex;
        align-items: center;
        &:before {
          position: static;
          margin-right: 8px;
        }

        &:after {
          display: none;
        }
      }
    }
  }

  .am-cap {
    &__alert {
      margin: 0 0 16px;
      .el-alert {
        padding: 4px 12px 4px 0;
        box-sizing: border-box;

        &__content {
          .el-alert__closebtn {
            top: 50%;
            transform: translateY(-50%);
          }
        }

        &__title {
          display: flex;
          align-items: center;
          font-size: 16px;
          line-height: 1.5;

          .am-icon-checkmark-circle-full {
            font-size: 28px;
            line-height: 1;
            color: var(--am-c-alerts-bgr);
          }
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-events;
}
</style>
