import moment from "moment";
import { useCookies } from "vue3-cookies";
import httpClient from "../../plugins/axios";

export default {
  namespaced: true,

  state: () => ({
    email: '',
    password: '',
    newPassword: '',
    confirmPassword: '',
    authenticated: false,
    token: null,
    profile: null,
    profileDeleted: false,
    loggedOut: false
  }),

  getters: {
    getEmail (state) {
      return state.email
    },

    getPassword (state) {
      return state.password
    },

    getNewPassword (state) {
      return state.newPassword
    },

    getConfirmPassword (state) {
      return state.confirmPassword
    },

    getAuthenticated (state) {
      return state.authenticated
    },

    getToken (state) {
      return state.token
    },

    getProfile (state) {
      return state.profile
    },

    getProfileDeleted (state) {
      return state.profileDeleted
    },

    getLoggedOut (state) {
      return state.loggedOut
    },
  },

  mutations: {
    setEmail (state, payload) {
      state.email = payload
    },

    setPassword (state, payload) {
      state.password = payload
    },

    setNewPassword (state, payload) {
      state.newPassword = payload
    },

    setConfirmPassword (state, payload) {
      state.confirmPassword = payload
    },

    setAuthenticated (state, payload) {
      state.authenticated = payload
    },

    setToken (state, payload) {
      state.token = payload
    },

    setProfile (state, payload) {
      state.profile = payload

      if (state.profile.phone === null) {
        state.profile.phone = ''
      }

      if (state.profile.birthday) {
        state.profile.birthday = moment(payload.birthday.date).format('YYYY-MM-DD')
      }
    },

    setProfileFirstName (state, payload) {
      state.profile.firstName = payload
    },

    setProfileLastName (state, payload) {
      state.profile.lastName = payload
    },

    setProfileEmail (state, payload) {
      state.profile.email = payload
    },

    setProfilePhone (state, payload) {
      state.profile.phone = payload
    },

    setProfileCountryPhoneIso (state, payload) {
      state.profile.countryPhoneIso = payload
    },

    setProfileBirthday (state, payload) {
      state.profile.birthday = payload
    },

    setProfileDeleted (state, payload) {
      state.profileDeleted = payload
    },

    setLoggedOut (state, payload) {
      state.loggedOut = payload
    },
  },

  actions: {
    logout ({commit}) {
      const vueCookies = useCookies()['cookies']
      commit('setToken', null)
      commit('setPassword', '')
      vueCookies.remove('ameliaToken')
      commit('setAuthenticated', false)
      commit('setLoggedOut', true)

      try {
        httpClient.post(
            '/users/logout',
            {},
            {}
        )


      } catch (error) {
        console.log(error)
      }
    }
  }
}
