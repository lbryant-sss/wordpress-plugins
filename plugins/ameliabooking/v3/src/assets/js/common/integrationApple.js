import httpClient from "../../../plugins/axios";
import { useAuthorizationHeaderObject } from "../public/panel";

function useAppleSync (store) {
  store.commit('auth/setAppleLoading', true)

  httpClient.get(
    '/apple/calendar-list/' + store.getters['employee/getId'],
    useAuthorizationHeaderObject(store)
  ).then((response) => {
    store.commit('auth/setAppleCalendars', response.data.data?.calendarList ? response.data.data.calendarList : [])

    if (store.getters['auth/getAppleCalendars'].map(i => i.id).indexOf(store.getters['employee/getAppleCalendarId']) === -1) {
      store.commit('employee/setAppleCalendarId', '')
    }
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('auth/setAppleLoading', false)
  })
}

export {
  useAppleSync,
}
