import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './store'

Vue.use(VueRouter);

const router = new VueRouter({
    routes: [
        {
            path: '/',
            component: require('./pages/App'),
            children: [
                {name: 'index', path: '', component: require('./pages/Index')},
                {path: 'search', redirect: '/'},
                {name: 'customerService', path: 'customer_service', component: require('./pages/CustomerService')},
                {name: 'checkTb', path: 'check_tb', component: require('./pages/CheckTb')},
                {name: 'checkPdd', path: 'check_pdd', component: require('./pages/CheckPdd')},
                {name: 'checkJd', path: 'check_jd', component: require('./pages/CheckJd')},
                {name: 'oneKeyExcel', path: 'one_key_excel', component: require('./pages/OneKeyExcel')},
                {name: 'searchOrder', path: 'search_order', component: require('./pages/SearchOrder')},

                {name: 'emulator_tb', path: '/emulator_tb', component: require('./pages/EmulatorTb')},
                {name: 'emulator_pdd', path: '/emulator_pdd', component: require('./pages/EmulatorPdd')},
                {name: 'emulator_pdd_mms', path: '/emulator_pdd_mms', component: require('./pages/EmulatorPddMms')},

                {name: 'articleList', path: 'article/list/:id', component: require('./pages/ArticleList')},
                {name: 'articleDetail', path: 'article/:id', component: require('./pages/ArticleDetail'), props: true},

                {name: 'recharge', path: 'recharge', component: require('./pages/Recharge')},
                {name: 'rechargeTest', path: 'recharge/test', component: require('./pages/RechargeTest')},
                {name: 'rechargeList', path: 'recharge/list', component: require('./pages/RechargeList')},
                {name: 'amountList', path: 'amount/list', component: require('./pages/AmountList')},
                {name: 'inviterLink', path: 'inviter/link', component: require('./pages/InviterLink')},

                {name: 'register', path: 'register', component: require('./pages/Register')},
                {name: 'login', path: 'login', component: require('./pages/Login')},
                {name: 'forgetPassword', path: 'forget/password', component: require('./pages/ForgetPassword')},
                {name: 'userProfile', path: 'user/profile', component: require('./pages/UserProfile')},
                {name: 'userMerchant', path: 'user/merchant', component: require('./pages/UserMerchant')},
                {name: 'userReport', path: 'user/report', component: require('./pages/UserReport')},
                {name: 'notificationList', path: 'notification/list', component: require('./pages/NotificatioinList')},
                {name: 'excelList', path: 'excel/list', component: require('./pages/ExcelList')},
                {name: 'vbotIndex', path: 'vbot/index', component: require('./pages/VbotIndex')},
                {name: 'vbotDetail', path: 'vbot/:id', component: require('./pages/VbotDetail')},
                {
                    name: 'userAuthBillIndex',
                    path: 'user-auth-bill/index',
                    component: require('./pages/UserAuthBillList')
                },
            ]
        },
        {name: 'tb_my_rate', path: 'check_account/tb_my_rate', component: require('./pages/check_account/TbMyRate')},
        {
            name: 'tb_raise_naughty',
            path: 'check_account/tb_raise_naughty',
            component: require('./pages/check_account/TbRaiseNaughty')
        },
    ]
});

router.beforeEach((to, from, next) => {
    //站长之家异步统计api
    if (typeof _czc !== 'undefined' && from.matched.length !== 0) {
        _czc.push(['_trackPageview', to.path]);
    }
    if (store.state.user) {
        if (['login', 'forgetPassword'].indexOf(to.name) > -1) {
            next({name: 'index'});
        }  else {
            next();
        }
    } else {
        if ([
            'index',
            'recharge',
            'rechargeList',
            'amountList',
            'inviterLink',
            'userProfile',
            'userMerchant',
            'excelList',
            'vbotIndex'
        ].indexOf(to.name) > -1) {
            next({name: 'login'});
        } else {
            next();
        }
    }
});

router.afterEach((to, from) => {
    if (store.state.unreadNotification > 0) {
        Vue.prototype.$message.error('你有未读通知，请到：个人中心->系统通知，查看并且阅读！')
    }
});

export default router