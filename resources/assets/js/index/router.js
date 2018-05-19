import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './store'

Vue.use(VueRouter);

const router = window.router = new VueRouter({
    routes: [
        {
            path: '/',
            component: require('./pages/App'),
            children: [
                {
                    path: '', component: require('./pages/Index'),
                    children: [
                        {name: 'index', path: '', component: require('./pages/ReportData')},
                        {name: 'searchResult', path: 'search', component: require('./pages/SearchResult')},
                    ]
                },
                {name: 'register', path: 'register', component: require('./pages/Register')},
                {name: 'login', path: 'login', component: require('./pages/Login')},
                {name: 'forgetPassword', path: 'forget/password', component: require('./pages/ForgetPassword')},
                {name: 'userProfile', path: 'user/profile', component: require('./pages/UserProfile')},
                {name: 'userMerchant', path: 'user/merchant', component: require('./pages/UserMerchant')},

                {name: 'articleList', path: 'article/list/:id', component: require('./pages/ArticleList')},
                {name: 'articleDetail', path: 'article/:id', component: require('./pages/ArticleDetail'), props: true},

                {name: 'recharge', path: 'recharge', component: require('./pages/Recharge')},
                {name: 'rechargeList', path: 'recharge/list', component: require('./pages/RechargeList')},

                {name: 'notificationList', path: 'notification/list', component: require('./pages/NotificatioinList')},
            ]
        }
    ]
});

router.beforeEach((to, from, next) => {
    if (store.state.user) {
        switch (to.name) {
            case 'login':
            case 'register':
            case'forgetPassword':
                next({name: 'index'});
                break;
            default:
                next();
                break;
        }
    } else {
        switch (to.name) {
            case 'index':
            case 'searchResult':
                next({'name': 'articleDetail', params: {id: laravel.index_blog_article}});
                break;
            default:
                next();
                break;
        }
    }
});

export default router