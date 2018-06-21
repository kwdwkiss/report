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
        user(state) {
            axios.get(api.adminInfo).then(function (res) {
                state.user = res.data.data;
            });
        }
    }
});

export default store