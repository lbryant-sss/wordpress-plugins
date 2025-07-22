import servicePriceMixin from './servicePriceMixin'

export default {
  mixins: [
    servicePriceMixin
  ],

  methods: {
    getArrayCustomPricing (service) {
      let data = service.customPricing

      let customPricing = null

      if (typeof data === 'undefined' || data === null || data === '') {
        customPricing = {enabled: null, durations: {}, persons: {}}
      } else if (typeof data === 'object') {
        if (Array.isArray(data.durations)) {
          if (!('persons' in data)) {
            data.persons = []
          }

          if (data.enabled === true) {
            data.enabled = 'duration'
          }

          return data
        } else {
          customPricing = JSON.parse(JSON.stringify(data))
        }
      } else {
        customPricing = JSON.parse(data)
      }

      if (customPricing.enabled === true) {
        customPricing.enabled = 'duration'
      }

      customPricing.persons = 'persons' in customPricing ? customPricing.persons : []

      let persons = []

      if (Object.keys(customPricing.persons).length) {
        persons.push({
          start: 1,
          range: Object.keys(customPricing.persons)[0] - 1,
          price: service.price,
          rules: []
        })

        Object.keys(customPricing.persons).forEach((person, index) => {
          persons.push({
            start: parseInt(person),
            range: index !== Object.keys(customPricing.persons).length - 1
              ? Object.keys(customPricing.persons)[index + 1] - 1
              : service.maxCapacity,
            price: customPricing.persons[person].price,
            rules: customPricing.persons[person].rules
          })
        })
      }

      let durations = []

      Object.keys(customPricing.durations).forEach((duration) => {
        durations.push({
          duration: parseInt(duration),
          price: customPricing.durations[duration].price,
          rules: customPricing.durations[duration].rules
        })
      })

      return {
        enabled: customPricing.enabled,
        durations: durations,
        persons: persons
      }
    },

    getJsonCustomPricing (service) {
      let durations = {}

      let customPricing = service.customPricing

      if (customPricing && customPricing.durations.filter(i => i.duration).length > 0) {
        customPricing.durations.forEach((item) => {
          durations[item.duration] = {
            price: item.price,
            rules: item.rules
          }
        })
      }

      let persons = {}

      if (customPricing && customPricing.persons.length) {
        customPricing.persons.forEach((item, index) => {
          if (index !== 0) {
            persons[customPricing.persons[index - 1].range + 1] = {
              price: item.price,
              rules: item.rules
            }
          }
        })
      }

      let enabled = null

      if (customPricing && this.isDurationPricingEnabled(customPricing)) {
        enabled = 'duration'
      } else if (customPricing && this.isPersonPricingEnabled(customPricing)) {
        enabled = 'person'
      }

      return JSON.stringify({enabled: enabled, durations: durations, persons: persons})
    }
  }
}
