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

                {name: 'article_list', path: 'article/list', component: require('./pages/Article/Index')},
                {name: 'article_create', path: 'article/create', component: require('./pages/Article/Create')},
                {name: 'article_update', path: 'article/update/:id', component: require('./pages/Article/Update')},

                {name: 'user_list', path: 'user/list', component: require('./pages/User/Index')},
                {name: 'user_create', path: 'user/create', component: require('./pages/User/Create')},
                {name: 'user_update', path: 'user/update/:id', component: require('./pages/User/Update')},

                {
                    name: 'account_report_list',
                    path: 'account_report/list',
                    component: require('./pages/AccountReport/Index')
                },
                {
                    name: 'account_report_update',
                    path: 'account_report/update/:id',
                    component: require('./pages/AccountReport/Update')
                },

                {name: 'account_list', path: 'account/list', component: require('./pages/Account/Index')},
                {name: 'account_create', path: 'account/create', component: require('./pages/Account/Create')},
                {name: 'account_update', path: 'account/update/:id', component: require('./pages/Account/Update')},

                {name: 'recharge_list', path: 'recharge/list', component: require('./pages/Recharge/Index')},
                {name: 'recharge_create', path: 'recharge/create', component: require('./pages/Recharge/Create')},

                {path: 'amount-bill/list', component: require('./pages/AmountBill/Index')},
                {path: 'search-bill/list', component: require('./pages/SearchBill/Index')},

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