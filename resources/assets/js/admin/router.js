import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter);

const router = new VueRouter({
    routes: [
        {path: '/login', component: require('./pages/Login.vue')},
        {
            path: '/admin',
            component: require('./App.vue'),
            alias: '/',
            children: [
                {path: '', component: require('./pages/Dashboard')},
                {path: 'taxonomy', component: require('./pages/Taxonomy')},
                {path: 'tag', component: require('./pages/Tag')},
                {path: 'article', component: require('./pages/Article')},
                {path: 'recharge', component: require('./pages/Recharge')},
                {path: 'user', component: require('./pages/User.vue')},
                {path: 'account/report', component: require('./pages/AccountReport')},
                {path: 'account', component: require('./pages/Account')},

                {path: 'amount-bill/list', component: require('./pages/AmountBill')},
                {path: 'search-bill/list', component: require('./pages/SearchBill')},

                {path: 'site/index-page', component: require('./pages/IndexPage')},
                {path: 'site/pop-window', component: require('./pages/PopWindow')},
                {path: 'system/site', component: require('./pages/Site')},
                {path: 'system/admin', component: require('./pages/Admin')},
                {path: 'message', component: require('./pages/Message')},
            ]
        }
    ]
});

export default router