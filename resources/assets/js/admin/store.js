import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

const store = window.store = new Vuex.Store({
    state: {
        taxonomy: laravel.taxonomy,
        user: laravel.user,
        roles: [],
        permissions: [],
    },
    mutations: {
        taxonomy(state) {
            axios.get(api.taxonomyAllData).then(function (res) {
                state.taxonomy = res.data.data;
            });
        },
        user(state, payload) {
            axios.get(api.adminAdminInfo).then(function (res) {
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
        },
        roles(state, payload) {
            axios.get(api.adminRoleAll, {params: payload}).then(function (res) {
                state.roles = res.data.data;
            });
        },
        permissions(state, payload) {
            axios.get(api.adminPermissionAll, {params: payload}).then(function (res) {
                state.permissions = res.data.data;
            });
        },
    }
});

export default store