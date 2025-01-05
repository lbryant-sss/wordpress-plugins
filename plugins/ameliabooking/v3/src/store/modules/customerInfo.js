import httpClient from "../../plugins/axios";

export default {
  namespaced: true,

  state: () => ({
    id: null,
    externalId: null,
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    countryPhoneIso : '',
    loggedUser: false,
  }),

  getters: {
    getCustomerId (state) {
      return state.id
    },

    getCustomerExternalId (state) {
      return state.externalId
    },

    getCustomerFirstName (state) {
      return state.firstName
    },

    getCustomerLastName (state) {
      return state.lastName
    },

    getCustomerEmail (state) {
      return state.email
    },

    getCustomerPhone (state) {
      return state.phone
    },

    getCustomerCountryPhoneIso (state) {
      return state.countryPhoneIso
    },

    getLoggedUser (state) {
      return state.loggedUser
    },

    getAllData (state) {
      return {
        id: state.id,
        externalId: state.externalId,
        firstName: state.firstName,
        lastName: state.lastName,
        email: state.email,
        phone: state.phone,
        countryPhoneIso : state.countryPhoneIso,
        loggedUser: state.loggedUser
      }
    }
  },

  mutations: {
    setCustomerId (state, payload) {
      state.id = payload
    },

    setCustomerExternalId (state, payload) {
      state.externalId = payload
    },

    setCustomerFirstName (state, payload) {
      state.firstName = payload
    },

    setCustomerLastName (state, payload) {
      state.lastName = payload
    },

    setCustomerEmail (state, payload) {
      state.email = payload
    },

    setCustomerPhone (state, payload) {
      state.phone = payload
    },

    setCustomerCountryPhoneIso (state, payload) {
      state.countryPhoneIso = payload
    },

    setLoggedUser (state, payload) {
      state.loggedUser = payload
    },

    setCurrentUser (state, payload) {
      state.id = payload.id
      state.externalid = payload.externalid
      state.firstName = payload.firstName
      state.lastName = payload.lastName
      state.email = payload.email
      state.phone = payload.phone ? payload.phone : ''
      state.countryPhoneIso = payload.countryPhoneIso ? payload.countryPhoneIso : ''
    },

    setAllData (state, payload) {
      state.id = payload.id
      state.externalId = payload.externalId
      state.firstName = payload.firstName
      state.lastName = payload.lastName
      state.email = payload.email
      state.phone = payload.phone
      state.countryPhoneIso = payload.countryPhoneIso
      state.loggedUser = payload.loggedUser
    }
  },

  actions: {
    requestCurrentUserData ({ commit }) {
      commit('setLoading', true, {root: true})
      if (!('ameliaUser' in window) || !window.ameliaUser) {
        httpClient.get(
          '/users/current'
        ).then((response) => {
          if (response.data.data.user) {
            window.ameliaUser = response.data.data.user ? response.data.data.user : null

            commit('setCurrentUser', window.ameliaUser)
            commit('setLoggedUser', true)
          }
          commit('setLoading', false, {root: true})
        }).catch(() => {
          commit('setLoading', false, {root: true})
        })
      } else {
        let ameliaApiInterval = setInterval(
          () => {
            if ('ameliaUser' in window) {
              clearInterval(ameliaApiInterval)

              commit('setCurrentUser', window.ameliaUser)
              commit('setLoggedUser', true)
            }
            commit('setLoading', false, {root: true})
          },
          1000
        )
      }
    }
  }
}