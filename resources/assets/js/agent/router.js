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
                {name: 'index', path: '', component: require('./pages/Index')},

                {name: 'userList', path: 'user/list', component: require('./pages/User/Index')},
                {name: 'userCreate', path: 'user/create', component: require('./pages/User/Create')},
                {name: 'userUpdate', path: 'user/update/:id', component: require('./pages/User/Update')},

                {name: 'reportList', path: 'report/list', component: require('./pages/AccountReport/Index')},
                {name: 'reportUpdate', path: 'report/update/:id', component: require('./pages/AccountReport/Update')},
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