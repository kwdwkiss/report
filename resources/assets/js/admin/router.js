import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './store'

Vue.use(VueRouter);

const router = new VueRouter({
    routes: [
        {name: 'login', path: '/login', component: require('./pages/Login.vue')},
        {
            path: '/admin',
            component: require('./App.vue'),
            alias: '/',
            children: [
                {name: 'index', path: '', component: require('./pages/Dashboard')},
                {name: 'statementList', path: 'statement/list', component: require('./pages/Statement/Index')},
                {name: 'behaviorLogList', path: 'behavior-log/list', component: require('./pages/BehaviorLog/Index')},

                {name: 'userList', path: 'user/list', component: require('./pages/User/Index')},
                {name: 'userCreate', path: 'user/create', component: require('./pages/User/Create')},
                {name: 'userUpdate', path: 'user/update/:id', component: require('./pages/User/Update')},
                {name: 'reportList', path: 'report/list', component: require('./pages/AccountReport/Index')},
                {name: 'reportUpdate', path: 'report/update/:id', component: require('./pages/AccountReport/Update')},
                {name: 'accountList', path: 'account/list', component: require('./pages/Account/Index')},
                {name: 'accountCreate', path: 'account/create', component: require('./pages/Account/Create')},
                {name: 'accountUpdate', path: 'account/update/:id', component: require('./pages/Account/Update')},

                {name: 'rechargeList', path: 'recharge/list', component: require('./pages/Recharge/Index')},
                {name: 'rechargeCreate', path: 'recharge/create', component: require('./pages/Recharge/Create')},
                {name: 'amountBillList', path: 'amount-bill/list', component: require('./pages/AmountBill/Index')},
                {name: 'searchBillList', path: 'search-bill/list', component: require('./pages/SearchBill/Index')},

                {
                    name: 'userAuthBillList',
                    path: 'user-auth-bill/list',
                    component: require('./pages/UserAuthBill/Index')
                },
                {
                    name: 'userAuthBillCreate',
                    path: 'user-auth-bill/create',
                    component: require('./pages/UserAuthBill/Create')
                },

                {name: 'siteIndexPage', path: 'site/index-page', component: require('./pages/IndexPage')},
                {name: 'sitePopWindow', path: 'site/pop-window', component: require('./pages/PopWindow')},
                {name: 'messageList', path: 'message/list', component: require('./pages/Message')},
                {name: 'queueStatus', path: 'queue/status', component: require('./pages/QueueStatus')},

                {name: 'systemSite', path: 'system/site', component: require('./pages/Site')},

                {name: 'adminIndex', path: 'system/admin', component: require('./pages/Admin/Index')},
                {name: 'adminCreate', path: 'system/create', component: require('./pages/Admin/Create')},
                {name: 'adminUpdate', path: 'system/update/:id', component: require('./pages/Admin/Update')},

                {name: 'roleIndex', path: 'role/index', component: require('./pages/Role/Index')},
                {name: 'roleUpdate', path: 'role/update/:id', component: require('./pages/Role/Update')},

                {name: 'permissionIndex', path: 'permission/index', component: require('./pages/Permission/Index')},

                {name: 'taxonomyList', path: 'taxonomy/list', component: require('./pages/Taxonomy')},
                {name: 'articleList', path: 'article/list', component: require('./pages/Article/Index')},
                {name: 'articleCreate', path: 'article/create', component: require('./pages/Article/Create')},
                {name: 'articleUpdate', path: 'article/update/:id', component: require('./pages/Article/Update')},

                {name: 'wechatServer', path: 'wechat/server', component: require('./pages/Wechat/Server')},
                {name: 'wechatMenu', path: 'wechat/menu', component: require('./pages/Wechat/Menu')},

                {name: 'vbotJobIndex', path: 'vbot_job/index', component: require('./pages/VbotJob/Index')},

                {name: 'excelIndex', path: 'excel/index', component: require('./pages/Excel/Index')},

                {name: 'productIndex', path: 'product/index', component: require('./pages/Product/Index')},

                {name: 'userProductIndex', path: 'user_product/index', component: require('./pages/UserProduct/Index')},

                {name: 'productBillIndex', path: 'product_bill/index', component: require('./pages/ProductBill/Index')},
            ]
        }
    ]
});

router.beforeEach((to, from, next) => {
    if (!store.state.user && to.name !== 'login') {
        next({name: 'login'});
    } else {
        next();
    }
});

export default router