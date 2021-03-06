/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import '../bootstrap'
import store from './store'
import router from './router'
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
            app.$message.error('用户未登录,请重新登录或3秒后自动跳转登录页面');
            setTimeout(function () {
                location.reload();
            }, 3000);
        } else if (error.response.status === 419) {//csrf token invalid
            app.$message.error('token失效,手动刷新或3秒后自动刷新页面');
            setTimeout(function () {
                location.reload();
            }, 3000);
        } else {
            let message = error.response.data.message ? error.response.data.message : error.response.statusText;
            app.$message.error(message);
        }
    }
    return Promise.reject(error);
});

window.app = new Vue({
    el: '#app',
    template: '<router-view></router-view>',
    router,
    store,
});