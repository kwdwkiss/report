/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../bootstrap');

import VueRouter from 'vue-router'

Vue.use(VueRouter);

import Vuex from 'vuex'

Vue.use(Vuex);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import 'element-ui/lib/theme-chalk/index.css'
import ElementUI from 'element-ui'

Vue.use(ElementUI);

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
            app.$router.push('/login');
        } else if (error.response.status === 419) {//csrf token invalid
            location.reload();
        } else {
            let message = error.response.data.message ? error.response.data.message : error.response.statusText;
            app.$message.error(message);
        }
    }
    return Promise.reject(error);
});

const routes = [
    {path: '/login', component: require('./pages/Login.vue')},
    {
        path: '/admin',
        component: require('./App.vue'),
        alias: '/',
        children: [
            {path: '', component: require('./pages/Dashboard.vue')},
            {path: 'taxonomy', component: require('./pages/Taxonomy.vue')},
            {path: 'tag', component: require('./pages/Tag.vue')},
            {path: 'article', component: require('./pages/Article.vue')},
            {path: 'user', component: require('./pages/User.vue')},
            {path: 'account/report', component: require('./pages/AccountReport.vue')},
            {path: 'account', component: require('./pages/Account.vue')},
            {path: 'index/page', component: require('./pages/IndexPage')},
            {path: 'system/site', component: require('./pages/Site.vue')},
            {path: 'system/admin', component: require('./pages/Admin.vue')},
        ]
    }
];

const router = new VueRouter({
    routes // （缩写）相当于 routes: routes
});

const store = window.store = new Vuex.Store({
    state: {
        taxonomy: laravel.taxonomy,
    },
    mutations: {
        taxonomy(state) {
            axios.get(api.taxonomyAllData).then(function (res) {
                state.taxonomy = res.data.data;
            });
        }
    }
});

window.app = new Vue({
    el: '#app',
    template: '<router-view></router-view>',
    router,
    store,
});