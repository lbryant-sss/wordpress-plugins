<template>
  <div
    ref="pageContainer"
    class="am-cap"
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
      :step-key="'appointments'"
      :responsive-class="responsiveClass"
      :empty="empty"
      @change-filters="getAppointments"
    />

    <template v-if="!loading">
      <AppointmentsList
        v-if="dateGroupedAppointments && Object.keys(dateGroupedAppointments).length > 0"
        :grouped-appointments="dateGroupedAppointments"
        :page-width="pageWidth"
        :responsive-class="responsiveClass"
        step-key="appointments"
        @canceled="getAppointments"
        @booked="getAppointments"
      ></AppointmentsList>
      <EmptyState
        v-else
        :heading="amLabels.no_app_found"
        :text="amLabels.have_no_app"
      ></EmptyState>
    </template>
    <Skeleton v-else></Skeleton>
  </div>
</template>

<script setup>
// * import from Vue
import {
  ref,
  computed,
  inject,
  provide,
  onMounted,
  watch,
  nextTick,
  reactive
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import from Libraries
import httpClient from "../../../../../plugins/axios";
import moment from "moment";

// * Templates
import CabinetFilters from "../parts/Filters.vue";
import AppointmentsList from "./parts/AppointmentsList.vue";
import Skeleton from "../../common/parts/Skeleton.vue";
import EmptyState from "../parts/EmptyState.vue"
import AmAlert from "../../../../_components/alert/AmAlert.vue";

// * Composables
import {
  useAuthorizationHeaderObject
} from "../../../../../assets/js/public/panel";
import {
  getDateRange,
} from "../../../../../assets/js/common/date";
import {
  useParsedAppointments,
} from "../../../../../assets/js/admin/appointment";
import {
  useUrlParams
} from "../../../../../assets/js/common/helper";
import {
  useResponsiveClass
} from "../../../../../assets/js/common/responsive";
import {
  useScrollTo
} from "../../../../../assets/js/common/scrollElements";

// * Store
let store = useStore()

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

  let customizedLabels = amCustomize.value.appointments.translations
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

let empty = ref(false)

// * Alert block
let alertContainer = ref(null)
let alertVisibility = ref(false)
let alertType = ref('success')

let alertMessage = ref('')

function closeAlert () {
  alertVisibility.value = false
  store.commit('cabinet/setPaymentLinkError', {value: false, type: 'appointment'})
}

/********
 * Form *
 ********/
let props = defineProps({
  loadBookingsCounter: {
    type: Number,
    default: 0
  },
  appointments: {
    type: Object,
    default: null
  },
})

// * Cabinet type
let cabinetType = inject('cabinetType')
store.commit('cabinetFilters/setDates', getDateRange(cabinetType.value))

// * Loading
let loading = computed(() => store.getters['cabinet/getAppointmentsLoading'])

let dateGroupedAppointments = ref(null)

function getAppointments (passedData = null) {
  store.commit('cabinet/setAppointmentsLoading', true)

  let params = JSON.parse(JSON.stringify(store.getters['cabinetFilters/getAppointmentsFilters']))
  let timeZone = store.getters['cabinet/getTimeZone']

  params.dates = params.dates.map(d => moment(d).format('YYYY-MM-DD'))
  params.timeZone = timeZone
  params.source = 'cabinet-' + cabinetType.value

  httpClient.get(
    '/appointments',
    useUrlParams(
      Object.assign(
        useAuthorizationHeaderObject(store),
        {params: params}
      )
    )
  ).then((response) => {
    dateGroupedAppointments.value = useParsedAppointments(
      response.data.data.appointments,
      timeZone
    )

    let filterEmpty = !(params.services.length || params.providers.length || params.locations.length)
    empty.value = (!dateGroupedAppointments.value || Object.values(dateGroupedAppointments.value).length === 0) && filterEmpty

    if (dateGroupedAppointments.value && Object.values(dateGroupedAppointments.value).length > 0 && filterEmpty) {
      let serviceIds = Object.values(dateGroupedAppointments.value).map(a => a.appointments.map(ap => ap.serviceId)).flat().filter((item, index, array) => array.indexOf(item) === index)
      store.dispatch('cabinetFilters/injectServiceOptions', serviceIds)

      let providerIds = Object.values(dateGroupedAppointments.value).map(a => a.appointments.map(ap => ap.providerId)).flat().filter((item, index, array) => array.indexOf(item) === index)
      store.dispatch('cabinetFilters/injectProviderOptions', providerIds)

      let locationIds = Object.values(dateGroupedAppointments.value).map(a => a.appointments.map(ap => ap.locationId)).flat().filter((item, index, array) => array.indexOf(item) === index)
      store.dispatch('cabinetFilters/injectLocationOptions', locationIds)
    }
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('cabinet/setAppointmentsLoading', false)
    if (passedData && 'message' in passedData) {
      alertVisibility.value = true
      alertMessage.value = passedData.message

      if (pageContainer.value && alertContainer.value) {
        setTimeout(function () {
          useScrollTo(pageContainer.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }
    }
  })
}

watch(() => props.loadBookingsCounter, () => {
  getAppointments()
})

onMounted(() => {
  getAppointments()
})
</script>

<script>
export default {
  name: 'CabinetAppointments',
  key: 'appointments'
}
</script>

<style lang="scss">
@mixin am-cabinet-appointments {
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
  @include am-cabinet-appointments;
}
</style>
