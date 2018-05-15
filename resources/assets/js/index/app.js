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
                path: '', component: require('./pages/Index.vue'),
                children: [
                    { name: 'index', path: '', component: require('./pages/ReportData') },
                    { name: 'search_result', path: 'search', component: require('./pages/SearchResult.vue') },
                ]
            },
            { name: 'article_detail', path: 'article/:id', component: require('./pages/ArticleDetail.vue'), props: true },
            { name: 'article_list', path: 'article/list/:id', component: require('./pages/ArticleList.vue') },
        ]
    }
];

const router = new VueRouter({ routes: routes });

const store = window.store = new Vuex.Store({
    state: {
        page: laravel,
        taxonomy: laravel.taxonomy,
        searchResult: {
            account_reports: [],
            account: {},
            type: 0,//type 0-不显示 1-显示无记录 2-显示记录列表 3-显示账号信息 4-显示骗子
        },
        user: laravel.user,
        notification: { data: [], meta: {}, links: {} },
        unreadNotification: { data: [], meta: {}, links: {} },
        recharge: { data: [], meta: {}, links: {} },
    },
    mutations: {
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
            axios.get(api.userNotificationList, { params: payload }).then(function (res) {
                state.notification = res.data;
            });
        },
        unreadNotification(state, payload) {
            axios.get(api.userUnreadNotificationList).then(function (res) {
                state.unreadNotification = res.data;
                if (res.data.meta.total && payload && payload.callback instanceof Function) {
                    payload.callback();
                }
            });
        },
        recharge(state, payload) {
            axios.get(api.userRechargeList, { params: payload }).then(function (res) {
                state.recharge = res.data;
            });
        }
    }
});

router.beforeEach((to, from, next) => {
    if (store.state.user) {
        next();
    } else {
        //refuse
        if (['index', 'search_result'].indexOf(to.name) > -1) {
            next({ 'name': 'article_detail', params: { id: laravel.index_blog_article } });
        } else {
            next();
        }
    }
});

Vue.component('my-nav', require('./components/Nav'));
Vue.component('my-copyright', require('./components/Copyright'));
Vue.component('pop-window', require('./components/PopWindow'));

Vue.component('my-logo', require('./components/Logo'));
Vue.component('my-notice', require('./components/Notice'));
Vue.component('article-data', require('./components/ArticleData'));

Vue.component('top-ad', require('./components/TopAd'));
Vue.component('middle-ad', require('./components/MiddleAd'));
Vue.component('bottom-ad', require('./components/BottomAd'));

window.app = new Vue({
    el: '#app',
    template: '<router-view></router-view>',
    router,
    store,
});