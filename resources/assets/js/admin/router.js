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

                {name: 'siteIndexPage', path: 'site/index-page', component: require('./pages/IndexPage')},
                {name: 'sitePopWindow', path: 'site/pop-window', component: require('./pages/PopWindow')},
                {name: 'messageList', path: 'message/list', component: require('./pages/Message')},
                {name: 'queueStatus', path: 'queue/status', component: require('./pages/QueueStatus')},

                {name: 'systemSite', path: 'system/site', component: require('./pages/Site')},
                {name: 'systemAdmin', path: 'system/admin', component: require('./pages/Admin')},
                {name: 'taxonomyList', path: 'taxonomy/list', component: require('./pages/Taxonomy')},
                {name: 'articleList', path: 'article/list', component: require('./pages/Article/Index')},
                {name: 'articleCreate', path: 'article/create', component: require('./pages/Article/Create')},
                {name: 'articleUpdate', path: 'article/update/:id', component: require('./pages/Article/Update')},

                {name: 'wechatServer', path: 'wechat/server', component: require('./pages/Wechat/Server')},
                {name: 'wechatMenu', path: 'wechat/menu', component: require('./pages/Wechat/Menu')},
            ]
        }
    ]
});

export default router