import '../bootstrap'
import store from './store'
import router from './router'
import './element-ui'
import 'jquery-qrcode'
import Vue from 'vue'

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
let requestUrls = {};
axios.interceptors.request.use(function (config) {
    console.log(config.url);
    if (requestUrls[config.url]) {
        let msg = '请求提交中，请不要频繁提交';
        Vue.prototype.$message.warning(msg);
        throw new Error(msg);
    } else {
        requestUrls[config.url] = true;
        setTimeout(function () {
            delete requestUrls[config.url];
        }, 1000);
        return config;
    }
}, function (error) {
    return Promise.reject(error)
});

axios.interceptors.response.use(function (response) {
    let errorMessage = null;

    if (response.data === '') {
        errorMessage = 'data is empty string';
    } else if (response.data.code !== 0) {
        errorMessage = response.data.message;
    }

    if (errorMessage) {
        Vue.prototype.$message.error({
            message: errorMessage,
            duration: 5000,
        });
        throw new Error(errorMessage);
    }
    return response;
}, function (error) {
    if (error.response) {
        if (error.response.status === 401) {//Unauthorized
            Vue.prototype.$message.error('用户未登录,请重新登录或3秒后自动跳转登录页面');
            setTimeout(function () {
                location.reload();
            }, 3000);
        } else if (error.response.status === 419) {//csrf token invalid
            Vue.prototype.$message.error('token失效,手动刷新或3秒后自动刷新页面');
            setTimeout(function () {
                location.reload();
            }, 3000);
        } else {
            let errorMessage = error.response.data.message ? error.response.data.message : error.response.statusText;
            Vue.prototype.$message.error(errorMessage);
        }
    }
    return Promise.reject(error);
});

Vue.component('my-nav', require('./components/Nav'));
Vue.component('my-breadcrumb', require('./components/Breadcrumb'));
Vue.component('my-copyright', require('./components/Copyright'));
Vue.component('pop-window', require('./components/PopWindow'));

Vue.component('my-logo', require('./components/Logo'));
Vue.component('my-notice', require('./components/Notice'));
Vue.component('article-data', require('./components/ArticleData'));
Vue.component('my-sms', require('./components/Sms'));

Vue.component('top-ad', require('./components/TopAd'));
Vue.component('middle-ad', require('./components/MiddleAd'));
Vue.component('bottom-ad', require('./components/BottomAd'));

window.app = new Vue({
    el: '#app',
    template: '<router-view></router-view>',
    router,
    store,
});