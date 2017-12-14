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
    },
    mutations: {
        searchResult(state, data) {
            state.searchResult = data;
        },
        taxonomy(state) {
            axios.get(api.taxonomyAllData).then(function (res) {
                state.taxonomy = res.data.data;
            })
        }
    }
});

window.app = new Vue({
    el: '#app',
    template: '<router-view></router-view>',
    router,
    store,
});