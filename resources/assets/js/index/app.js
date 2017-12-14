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
            {path: '', component: require('./pages/Index.vue')},
            {path: 'article/:id', component: require('./pages/Article.vue')},
            {path: 'article/list/:id', component: require('./pages/ArticleList.vue')},
        ]
    }
];

const router = new VueRouter({
    routes // （缩写）相当于 routes: routes
});

window.store = new Vuex.Store({
    state: {
        page: laravel,
        taxonomy: laravel.taxonomy,
    },
    mutations: {
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
});