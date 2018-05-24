import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

const store = window.store = new Vuex.Store({
    state: {
        page: laravel,
        taxonomy: laravel.taxonomy,
        breadcrumb: {},
        searchResult: {
            account_reports: [],
            account: {},
            type: 0,//type 0-不显示 1-显示无记录 2-显示记录列表 3-显示账号信息 4-显示骗子
        },
        user: laravel.user,
        notification: {data: [], meta: {}, links: {}},
        unreadNotification: laravel.unreadNotification,
        recharge: {data: [], meta: {}, links: {}},
        amount: {data: [], meta: {}, links: {}},
    },
    mutations: {
        breadcrumb(state, payload) {
            state.breadcrumb = _.assign(state.breadcrumb, payload);
        },
        searchResult(state, payload) {
            axios.post(api.indexSearch, payload).then(function (res) {
                state.searchResult = res.data.data;
                if (payload && payload.callback instanceof Function) {
                    payload.callback();
                }
            });
        },
        taxonomy(state) {
            axios.get(api.taxonomyAllData).then(function (res) {
                state.taxonomy = res.data.data;
            })
        },
        user(state, payload) {
            axios.get(api.userInfo).then(function (res) {
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
        notification(state, payload) {
            axios.get(api.userNotificationList, {params: payload}).then(function (res) {
                state.notification = res.data;
            });
        },
        unreadNotification(state, payload) {
            axios.get(api.userUnreadNotificationCount).then(function (res) {
                state.unreadNotification = res.data;
                if (payload && payload.callback instanceof Function) {
                    payload.callback();
                }
            });
        },
        recharge(state, payload) {
            axios.get(api.userRechargeList, {params: payload}).then(function (res) {
                state.recharge = res.data;
            });
        },
        amount(state, payload) {
            axios.get(api.userAmountList, {params: payload}).then(function (res) {
                state.amount = res.data;
            });
        },
    }
});

export default store