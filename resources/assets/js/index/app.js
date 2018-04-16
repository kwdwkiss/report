require('../bootstrap');

import VueRouter from 'vue-router'

Vue.use(VueRouter);

import Vuex from 'vuex'

Vue.use(Vuex);

import './element-ui'

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
axios.interceptors.response.use(function (response) {
    let errorMessage = null;

    if (response.data === '') {
        errorMessage = 'data is empty string';
    } else if (response.data.code !== 0) {
        errorMessage = response.data.message;
    }

    if (errorMessage) {
        app.$message.error(errorMessage);
        throw new Error(errorMessage);
    }
    return response;
}, function (error) {
    if (error.response) {
        if (error.response.status === 401) {//Unauthorized
            //app.$router.push('/login');
        } else if (error.response.status === 419) {//csrf token invalid
            location.reload();
        } else {
            let errorMessage = error.response.data.message ? error.response.data.message : error.response.statusText;
            app.$message.error(errorMessage);
        }
    }
    return Promise.reject(error);
});

const routes = [
    {
        path: '/',
        component: require('./pages/App.vue'),
        children: [
            {
                path: '',
                component: require('./pages/Index.vue'),
                children: [
                    {path: '', component: require('./pages/ReportData')},
                    {path: 'search', component: require('./pages/SearchResult.vue')},
                ]
            },
            {path: 'article/:id', component: require('./pages/Article.vue')},
            {path: 'article/list/:id', component: require('./pages/ArticleList.vue')},
        ]
    }
];

const router = new VueRouter({
    routes // （缩写）相当于 routes: routes
});

const store = window.store = new Vuex.Store({
    state: {
        page: laravel,
        taxonomy: laravel.taxonomy,
        searchResult: {
            account_reports: [],
            account: {},
            type: 0,//type 0-不显示 1-显示无记录 2-显示记录列表 3-显示账号信息 4-显示骗子
        },
        user: null,
        notification: {data: [], meta: {}, links: {}},
        unreadNotification: {data: [], meta: {}, links: {}},
        recharge: {data: [], meta: {}, links: {}},
    },
    mutations: {
        searchResult(state, data) {
            state.searchResult = data;
        },
        taxonomy(state) {
            axios.get(api.taxonomyAllData).then(function (res) {
                state.taxonomy = res.data.data;
            })
        },
        user(state) {
            axios.get(api.userInfo).then(function (res) {
                state.user = res.data.data;
            }).catch(function () {
                state.user = null;
            });
        },
        notification(state, payload) {
            axios.get(api.userNotificationList, {params: payload}).then(function (res) {
                state.notification = res.data;
            });
        },
        unreadNotification(state, payload) {
            axios.get(api.userUnreadNotificationList).then(function (res) {
                state.unreadNotification = res.data;
                if (res.data.meta.total && payload.callback) {
                    payload.callback();
                }
            });
        },
        recharge(state, payload) {
            axios.get(api.userRechargeList, {params: payload}).then(function (res) {
                state.recharge = res.data;
            });
        }
    }
});
store.commit('user');

window.app = new Vue({
    el: '#app',
    template: '<router-view></router-view>',
    router,
    store,
});