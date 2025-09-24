import { createStore } from "vuex";
import createPersistedState from "vuex-persistedstate";

const state = {
    user: null
};

const store = createStore({
    state,
    getters: {
        user: (state) => {
            return state.user;
        }
    },
    actions: {
        user: (context, user) => {
            context.commit('user', user);
        }
    },
    mutations: {
        user: (state, user) => {
            state.user = user;
        }
    },
    plugins: [createPersistedState()]
});

export default store;
