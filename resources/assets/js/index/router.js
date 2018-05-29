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
                {name: 'index', path: '', component: require('./pages/Index')},
                {path: 'search', redirect: '/'},
                {name: 'checkTb', path: 'check_tb', component: require('./pages/CheckTb')},

                {name: 'register', path: 'register', component: require('./pages/Register')},
                {name: 'login', path: 'login', component: require('./pages/Login')},
                {name: 'forgetPassword', path: 'forget/password', component: require('./pages/ForgetPassword')},
                {name: 'userProfile', path: 'user/profile', component: require('./pages/UserProfile')},
                {name: 'userMerchant', path: 'user/merchant', component: require('./pages/UserMerchant')},

                {name: 'articleList', path: 'article/list/:id', component: require('./pages/ArticleList')},
                {name: 'articleDetail', path: 'article/:id', component: require('./pages/ArticleDetail'), props: true},

                {name: 'recharge', path: 'recharge', component: require('./pages/Recharge')},
                {name: 'rechargeList', path: 'recharge/list', component: require('./pages/RechargeList')},
                {name: 'amountList', path: 'amount/list', component: require('./pages/AmountList')},
                {name: 'inviterLink', path: 'inviter/link', component: require('./pages/InviterLink')},

                {name: 'notificationList', path: 'notification/list', component: require('./pages/NotificatioinList')},
            ]
        }
    ]
});

router.beforeEach((to, from, next) => {
    if (store.state.user) {
        if (['login', 'forgetPassword'].indexOf(to.name) > -1) {
            next({name: 'index'});
        } else {
            next();
        }
    } else {
        if ([
            'index',
            'recharge',
            'rechargeList',
            'amountList',
            'inviterLink',
        ].indexOf(to.name) > -1) {
            next({'name': 'articleDetail', params: {id: laravel.index_blog_article}});
        } else {
            next();
        }
    }
});

export default router