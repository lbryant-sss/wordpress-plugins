import priceMixin from '../../../js/common/mixins/priceMixin'

export default {
  mixins: [
    priceMixin
  ],

  methods: {
    isDurationPricingEnabled (customPricing) {
      return customPricing !== null &&
        (customPricing.enabled === 'duration' || customPricing.enabled === true) &&
        'durations' in customPricing
    },

    isPersonPricingEnabled (customPricing) {
      return customPricing !== null && (customPricing.enabled === 'person') &&
        'persons' in customPricing
    },

    isPeriodPricingEnabled (customPricing) {
      return customPricing !== null &&
        (customPricing.enabled === 'period') &&
        'periods' in customPricing &&
        !this.$root.licence.isLite &&
        !this.$root.licence.isStarter &&
        !this.$root.licence.isBasic
    },

    getBookingServicePrice (service, duration, persons, providerId, bookingStart) {
      if (this.isDurationPricingEnabled(service.customPricing)) {
        return this.getBookingDurationPrice(service, duration)
      }

      if (this.isPersonPricingEnabled(service.customPricing)) {
        return this.getBookingPersonPrice(service, persons)
      }

      if (this.isPeriodPricingEnabled(service.customPricing) && providerId && bookingStart) {
        return this.getBookingPeriodPrice(service, parseInt(providerId), bookingStart)
      }

      return service.price
    },

    getBookingDurationPrice (service, duration) {
      if (duration && service.customPricing.durations.filter(i => i.duration === duration).length) {
        return service.customPricing.durations.find(i => i.duration === duration).price
      }

      return service.price
    },

    getBookingPersonPrice (service, persons) {
      let sortedRanges = service.customPricing.persons.map(i => i.range).sort((a, b) => b.range - a.range)

      if (persons && sortedRanges.length) {
        let filteredRanges = sortedRanges.filter(i => i >= persons)

        let range = filteredRanges.length === 0
          ? sortedRanges[sortedRanges.length - 1]
          : filteredRanges[0]

        return service.customPricing.persons.find(i => i.range === range).price
      }

      return service.price
    },

    getBookingPeriodPrice (service, providerId, bookingStart) {
      let start = bookingStart.split(' ')

      if (start[0] in this.pricedCalendarTimeSlots) {
        if (start[1].substr(0, 5) in this.pricedCalendarTimeSlots[start[0]]) {
          let slots = this.pricedCalendarTimeSlots[start[0]][start[1].substr(0, 5)].filter(i => i.e === providerId)

          return slots.length ? slots[0].p : service.price
        }
      }

      return service.price
    }
  }
}
