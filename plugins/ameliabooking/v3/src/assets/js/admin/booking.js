function useParams (store, cabinetType) {
  return {
    source: 'cabinet-' + cabinetType.value,
    timeZone: store.getters['cabinet/getTimeZone'],
  }
}

function useCustomFieldsData(bookings) {
  let result = []

  bookings.forEach((booking) => {
    if (['approved', 'pending'].includes(booking.status) && booking.customFields) {
      let customFields = JSON.parse(booking.customFields)

      Object.keys(customFields).forEach((customFieldId) => {
        result.push({
          label: customFields[customFieldId].label,
          value: customFields[customFieldId].value
        })
      })
    }
  })

  return result
}

function useExtrasData(bookings, bookable) {
  let result = []

  bookings.forEach((booking) => {
    if (['approved', 'pending'].includes(booking.status)) {
      booking.extras.forEach((bookingExtra) => {
        result.push(Object.assign(bookingExtra, bookable.extras.find(i => i.id === bookingExtra.extraId)))
      })
    }
  })

  return result
}

export {
  useParams,
  useCustomFieldsData,
  useExtrasData,
}
