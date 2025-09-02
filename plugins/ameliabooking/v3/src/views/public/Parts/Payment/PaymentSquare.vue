<template>
  <div>
    <div v-show="squareLoading" class="am-fs__square-loading" :style="cssVars">
      <!-- Skeleton -->
      <el-skeleton animated>
        <el-skeleton-item />
      </el-skeleton>
      <!-- /Skeleton -->
    </div>
    <!-- Credit Card via Square-->
    <div v-show="!squareLoading" class="am-fs__payment-square" :style="cssVars">
      <div class="am-fs__payment-square__google-pay">
        <div id="google-pay-button"/>
      </div>

      <div class="am-fs__payment-divider">
        <span class="am-divider-text">{{ amLabels.payment_or_pay_with_card }}</span>
      </div>

      <div id="payment-status-container"></div>
      <div id="card-container"></div>
    </div>
    <!-- /Credit Card via Square-->
  </div>

</template>

<script setup>
import {computed, inject, onMounted, ref, watchEffect, nextTick} from 'vue'
import {
  getErrorMessage,
  useBookingData,
  useCreateBooking,
  useCreateBookingError,
  useCreateBookingSuccess,
} from '../../../../assets/js/public/booking.js'
import {useColorTransparency} from '../../../../assets/js/common/colorManipulation.js'
import {useStore} from "vuex"
import httpClient from "../../../../plugins/axios"
import {useScrollTo} from "../../../../assets/js/common/scrollElements";

// * Global settings
const amSettings = inject('settings')
const store = useStore()

// * Labels
const amLabels = inject('amLabels')

// * Colors
let amColors = inject('amColors')

// * Css variables
let cssVars = computed(() => {
  return {
    '--am-c-pay-text': amColors.value.colorMainText,
    '--am-c-pay-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6)
  }
})

// * Components Emits
const emits = defineEmits(['payment-error'])

const { nextStep, footerButtonReset, footerButtonClicked } = inject('changingStepsFunctions', {
  nextStep: () => {},
  footerButtonReset: () => {},
  footerButtonClicked: {
    value: false
  }
})

const cardInstance = ref(null)

async function continueWithBooking () {
  footerButtonReset()
  store.commit('booking/setLoading', true)

  if (!cardInstance.value) {
    store.commit('booking/setLoading', true)
  }

  const token = await squareTokenize(cardInstance.value)
  if (!token) {
    store.commit('booking/setLoading', false)
    return
  }
  await createSquarePayment(token)
}

// * Watching when footer button was clicked
watchEffect(() => {
  if (footerButtonClicked.value) {
    if (!store.getters['booking/getCouponValidated']) {
      footerButtonReset()
      emits('payment-error', amLabels.value.coupon_mandatory)
    } else {
      continueWithBooking()
    }
  }
})

const cardReady = ref(false)
const googlePayReady = ref(false)
const squareLoading = computed(() => !(cardReady.value && googlePayReady.value))

async function getAmountToPay() {
  let checkoutPaymentData = null

  await httpClient.post(
      '/payments/amount',
      useBookingData(
          store,
          null,
          true,
          {},
          null
      )['data']
  ).then((response) => {
    checkoutPaymentData = response.data.data
  }).catch(e => {
    const message = e?.response?.data?.message || e.message || 'Unknown error'
    emits('payment-error', message)
  })

  const totalPriceParts = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: checkoutPaymentData.currency,
  }).formatToParts(checkoutPaymentData.amount)

  const IntegerPart = totalPriceParts.find((part) => part.type === 'integer')?.value || ''
  const fractionPart = totalPriceParts.find((part) => part.type === 'fraction')?.value || ''
  const decimalPart = totalPriceParts.find((part) => part.type === 'decimal')?.value || ''
  const formattedAmount = `${IntegerPart}${decimalPart}${fractionPart}`
  return {
    formattedAmount,
    rawAmount: checkoutPaymentData.amount,
    countryCode: checkoutPaymentData.countryCode,
  }
}

async function payingNow() {
  return await getAmountToPay()
}

onMounted(async () => {
// Defer mounting logic until DOM is visible
  cardReady.value = false
  googlePayReady.value = false

  await nextTick()
  await initSquarePayment()
})

const squareLocationId = amSettings.payments.square.locationId
const squareClientId = amSettings.payments.square.testMode ? amSettings.payments.square.clientTestId : amSettings.payments.square.clientLiveId

const bookingData =  useBookingData(
    store,
    null,
    true,
    {},
    null
)

let paymentStepRef = inject('paymentRef')

const payments = window.Square.payments(squareClientId, squareLocationId)

async function initSquarePayment() {
  const squareCardStyle = {
    '.input-container': {
      borderColor: '#d9d9d9',
      borderRadius: '6px',
    }
  }

  try {
    const cardContainer = document.getElementById('card-container')
    if (cardContainer) {
      cardContainer.innerHTML = ''
    }
    const card = await payments.card({
      style: squareCardStyle
    })
    await card.attach('#card-container')
    cardInstance.value = card
    cardReady.value = true
  } catch (e) {
    const statusContainer = document.getElementById('payment-status-container')
    console.error(e)
    store.commit('setLoading', false)
    store.commit('booking/setLoading', false)

    if (statusContainer) {
      statusContainer.className = 'missing-credentials'
      statusContainer.style.visibility = 'visible'
    }
  }

  try {
    const googlePayButton = document.getElementById('google-pay-button')

    if (googlePayButton) {
      googlePayButton.innerHTML = ''

      const paymentInfo = await payingNow()
      if (!paymentInfo) return

      const initialRequest = payments.paymentRequest({
        countryCode: paymentInfo.countryCode,
        currencyCode: bookingData.data.payment.currency,
        total: {
          amount: paymentInfo.formattedAmount.toString(),
          label: 'Total',
        },
      })

      const googlePay = await payments.googlePay(initialRequest)
      await googlePay.attach('#google-pay-button')
      googlePayReady.value = true

      googlePayButton.addEventListener('click', async function () {
        store.commit('setLoading', true)
        store.commit('booking/setLoading', true)

        const updatedInfo = await payingNow()
        if (!updatedInfo) return

        const updatedPaymentRequest = payments.paymentRequest({
          countryCode: updatedInfo.countryCode,
          currencyCode: bookingData.data.payment.currency,
          total: {
            amount: updatedInfo.formattedAmount.toString(),
            label: 'Total',
          },
        })

        // Use new payment request for new Google Pay instance if pay full amount checked/unchecked
        const updatedGooglePay = await payments.googlePay(updatedPaymentRequest)

        const token = await squareTokenize(updatedGooglePay)
        if (!token) {
          store.commit('setLoading', false)
          store.commit('booking/setLoading', false)

          return
        }

        await createSquarePayment(token)
      })
    }
  } catch (e) {
    console.log(e)
  }
}

const squareTokenize = async (payments) => {
  try {
    const totalAmount = await payingNow()
    const {token, status, errors} = await payments.tokenize({
        amount: totalAmount.formattedAmount.toString(),
        billingContact: {
          familyName: bookingData.data.bookings[0].customer.lastName,
          givenName: bookingData.data.bookings[0].customer.firstName,
          email: bookingData.data.bookings[0].customer.email,
          phone: bookingData.data.bookings[0].customer.phone,
        },
        customerInitiated: true,
        sellerKeyedIn: false,
        currencyCode: bookingData.data.payment.currency,
        intent: 'CHARGE',
      }
    )

    if (status === 'OK') {
      return token;
    } else if (status === 'Invalid' && errors.length > 0) {
      const messages = errors.map((err) => err.message)
      emits('payment-error',  messages.join(', '))
      useScrollTo(paymentStepRef.value, paymentStepRef.value, 20, 300)
      return ''
    }
  } catch (e) {
    console.log(e)
  }
}

const createSquarePayment = async (token) => {
  if (!token) {
    return
  }
  useCreateBooking(
    store,
    useBookingData(
      store,
      null,
      false,
      {
        locationId: squareLocationId,
        sourceId: token,
        idempotencyKey: window.crypto.randomUUID(),
      },
      null
    ),
    function (response) {
      successBooking(response)
    },
    (response) => {
      errorBooking(response)
    }
  )
}


function successBooking (response) {
  useCreateBookingSuccess(
    store,
    response,
    function () {
      nextStep()
    }
  )
}

function errorBooking (error) {
  useCreateBookingError(
    store,
    error.response.data,
    () => {
      emits('payment-error', getErrorMessage())
    }
  )
}
</script>

<script>
export default {
  name: 'PaymentSquare'
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    .am-fs__square-loading {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 100%;

      .el-skeleton {
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 10px;
        width: 100%;

        &__item {
          width: 100%;
          height: 40px;
        }
      }
    }

    .am-fs__payment-square {
      display: flex;
      flex-direction: column;
      gap: 6px;

      span {
        display: inline-flex;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.42857;
        color: #6772E5;
      }

      &__google-pay {
        #google-pay-button {
          width: 100%;
          div {
            button {
              width: 100%;
              display: flex;
              justify-content: center;
              align-items: center;
            }
          }
        }
      }

      #card-container {
        .sq-card-iframe-container {
          border: 1px solid #d9d9d9;
        }
      }

      .am-fs__payment-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin: 16px 0;
        text-align: center;
      }

      .am-fs__payment-divider::before,
      .am-fs__payment-divider::after {
        content: "";
        flex-grow: 1;
        height: 1px;
        background-color: var(--am-c-pay-text-op60);
        margin: 0 8px;
      }

      .am-divider-text {
        font-size: 14px;
        color: var(--am-c-pay-text-op60);
        text-transform: uppercase;
        line-height: 1.33333;
        font-weight: 500;
      }
    }
  }
}
</style>