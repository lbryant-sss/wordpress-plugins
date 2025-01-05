import {settings} from "../../../plugins/settings";

function getEmployeeServicePrice (store, providerId, serviceId) {
    let employeeService = store.getters['entities/getEmployeeService'](providerId, serviceId)

    let duration = store.getters['booking/getBookingDuration'] ? store.getters['booking/getBookingDuration'] : employeeService.duration

    if (employeeService.customPricing &&
      employeeService.customPricing.enabled &&
      duration &&
      duration in employeeService.customPricing.durations
    ) {
        return employeeService.customPricing.durations[duration].price
    }

    return employeeService.price
}

function sortForEmployeeSelection (store, employeesIds, serviceId) {
    switch (settings.appointments.employeeSelection) {
        case 'roundRobin': {
            let lastBookedProviderId = store.getters['booking/getLastBookedProviderId']
            employeesIds = employeesIds.map(e => parseInt(e)).sort((a,b) => a-b)
            for (let employeeId of employeesIds) {
                if (parseInt(employeesIds[0]) > parseInt(lastBookedProviderId)) {
                    break
                }
                employeesIds.push(employeesIds.shift())

            }
            return employeesIds
        }
        case 'lowestPrice':
            return employeesIds.sort((emp1, emp2) => {
                let price1 = getEmployeeServicePrice(store, emp1, serviceId)
                let price2 = getEmployeeServicePrice(store, emp2, serviceId)
                if (price1 < price2) {
                    return -1
                } else if (price1 === price2) {
                    return emp1 < emp2 ? -1 : 1
                } else {
                    return 1
                }
            })
        case 'highestPrice':
            return employeesIds.sort((emp1, emp2) => {
                let price1 = getEmployeeServicePrice(store, emp1, serviceId)
                let price2 = getEmployeeServicePrice(store, emp2, serviceId)
                if (price1 < price2) {
                    return 1
                } else if (price1 === price2) {
                    return emp1 < emp2 ? -1 : 1
                } else {
                    return -1
                }
            })
        case 'random': default:
            return employeesIds
    }
}

function checkLimitPerEmployee (employeesIds, bookingIndex, bookings, booking, appCount, chosenEmployees, serviceId) {
    let filteredEmployeeIds = []
    for (let employeeId of employeesIds) {
        let count = appCount && appCount[employeeId] && appCount[employeeId][booking.date] ? appCount[employeeId][booking.date] : 0

        let otherServiceBookings = chosenEmployees.filter(e => e.providerId === employeeId && e.date === booking.date && e.serviceId !== serviceId && !e.existingApp)
        let otherBookings        = bookings.filter((e, index) => e.providerId === employeeId && e.date === booking.date && bookingIndex !== index && !e.existingApp)

        if (otherBookings.length + otherServiceBookings.length + count < settings.roles.limitPerEmployee.numberOfApp) {
            filteredEmployeeIds.push(employeeId)
        }
    }
    if (filteredEmployeeIds.length === 0) {
        return {'employeeIds': filteredEmployeeIds, 'bookingFailed' : bookingIndex}
    }
    return {'employeeIds': filteredEmployeeIds, 'bookingFailed' : null}
}

export {
    sortForEmployeeSelection,
    checkLimitPerEmployee
}
