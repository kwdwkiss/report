require('../bootstrap');

import VueRouter from 'vue-router'

Vue.use(VueRouter);

import Vuex from 'vuex'

Vue.use(Vuex);

import './mint-ui'

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
            {path: '', component: require('./pages/Index.vue')},
            {path: 'user', component: require('./pages/User.vue')},
            {path: 'article', component: require('./pages/Article.vue')},
        ]
    }
];

const router = new VueRouter({
    routes // （缩写）相当于 routes: routes
});

const store = window.store = new Vuex.Store({
    state: {
        user: null,
        page: laravel,
        taxonomy: laravel.taxonomy,
        searchResult: {
            account_reports: [],
            account: {},
            type: 0,//type 0-不显示 1-显示无记录 2-显示记录列表 3-显示账号信息 4-显示骗子
        },
    },
    mutations: {
        searchResult(state, data) {
            axios.post(api.indexSearch, data).then(function (res) {
                state.searchResult = res.data.data;
            });
        },
    }
});

window.app = new Vue({
    el: '#app',
    template: '<router-view></router-view>',
    router,
    store,
});