import Vue from "vue";
import Vuex from "vuex";

// store modules
import notification from "./store/notification.js";
import system from "./store/system.js";
import translation from "./store/translation.js";
import user from "./store/user.js";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    breadcrumb: [],
    isLoading: false,
    title: null,
    view: null,
    search: false,
  },
  mutations: {
    SET_BREADCRUMB(state, breadcrumb) {
      state.breadcrumb = breadcrumb;
    },
    SET_SEARCH(state, search) {
      if (search === true) {
        search = {};
      }

      state.search = search;
    },
    SET_TITLE(state, title) {
      state.title = title;
    },
    SET_VIEW(state, view) {
      state.view = view;
    },
    START_LOADING(state) {
      state.isLoading = true;
    },
    STOP_LOADING(state) {
      state.isLoading = false;
    }
  },
  actions: {
    breadcrumb(context, breadcrumb) {
      context.commit("SET_BREADCRUMB", breadcrumb);
    },
    isLoading(context, loading) {
      context.commit(loading === true ? "START_LOADING" : "STOP_LOADING");
    },
    search(context, search) {
      context.commit('SET_SEARCH', search);
    },
    title(context, title) {
      context.commit("SET_TITLE", title);
      document.title = title + " | " + context.state.system.info.title;
    },
    view(context, view) {
      context.commit("SET_VIEW", view);
    }
  },
  modules: {
    notification: notification,
    system: system,
    translation: translation,
    user: user
  }
});
