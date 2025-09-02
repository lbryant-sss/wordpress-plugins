import taxesMixin from '../../../js/common/mixins/taxesMixin'
import servicePriceMixin from '../../../js/common/mixins/servicePriceMixin'

export default {
  mixins: [
    servicePriceMixin,
    taxesMixin
  ],

  data () {
    return {}
  },

  methods: {
    getAppointmentService (appointment) {
      let provider = this.getProviderById(appointment.providerId)

      let providerService = provider.serviceList.find(service => service.id === appointment.serviceId)

      return providerService || this.getServiceById(appointment.serviceId)
    },

    getAppointmentPrice (service, bookings, formatPrice = true) {
      let totalBookings = 0

      bookings.filter(i => i.packageCustomerService === null).forEach((booking) => {
        if (['approved', 'pending'].includes(booking.status)) {
          totalBookings += this.getBookingPrice(booking, false, booking.price, booking.aggregatedPrice, service.id)
        }
      })

      return formatPrice ? this.getFormattedPrice(
        totalBookings >= 0 ? totalBookings : 0,
        !this.$root.settings.payments.hideCurrencySymbolFrontend
      ) : (totalBookings >= 0 ? totalBookings : 0)
    },

    getBookingPrice (booking, isNewBooking, bookingPrice, aggregatedPrice, entityId = null) {
      let priceData = {
        price: !isNewBooking ? booking.price : bookingPrice,
        aggregatedPrice: aggregatedPrice,
        id: !isNewBooking ? entityId : null
      }

      if (!isNewBooking) {
        priceData.tax = booking.tax
      }

      let amountData = this.getAppointmentPriceAmount(
        priceData,
        booking.extras.filter(i => 'selected' in i ? i.selected : true),
        booking.persons,
        booking.coupon,
        false
      )

      return amountData.discount > amountData.total ? 0 : amountData.total - amountData.discount + amountData.tax
    },

    getPackagePrice (pack, key) {
      let coupon = pack.bookings[key].packageCustomerService.packageCustomer.coupon
      let total = pack.bookings[key].packageCustomerService.packageCustomer.price
      let discountTotal = (total / 100 * (coupon ? coupon.discount : 0)) + (coupon ? coupon.deduction : 0)

      return discountTotal > total ? 0 : total - discountTotal
    }
  }
}
