import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

const store = window.store = new Vuex.Store({
    state: {
        taxonomy: laravel.taxonomy,
        user: laravel.user,
    },
    mutations: {
        taxonomy(state) {
            axios.get(api.taxonomyAllData).then(function (res) {
                state.taxonomy = res.data.data;
            });
        },
        user(state, payload) {
            axios.get(api.adminInfo).then(function (res) {
                state.user = res.data.data;
                if (payload && payload.callback instanceof Function) {
                    payload.callback();
                }
            }).catch(function () {
                state.user = null;
                if (payload && payload.callback instanceof Function) {
                    payload.callback();
                }
            });
        }
    }
});

export default store