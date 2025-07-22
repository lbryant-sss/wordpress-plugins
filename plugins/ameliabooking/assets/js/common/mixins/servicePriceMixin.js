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

    getPersonServicePrice (service, persons) {
      if (this.isPersonPricingEnabled(service.customPricing)) {
        return ' (' + this.getFormattedPrice(this.getBookingPersonPrice(service, persons), !this.$root.settings.payments.hideCurrencySymbolFrontend) + ')'
      }

      return ''
    },

    getBookingServicePrice (service, duration, persons) {
      if (this.isDurationPricingEnabled(service.customPricing)) {
        return this.getBookingDurationPrice(service, duration)
      }

      if (this.isPersonPricingEnabled(service.customPricing)) {
        return this.getBookingPersonPrice(service, persons)
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
    }
  }
}
