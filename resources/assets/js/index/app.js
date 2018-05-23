import '../bootstrap'
import store from './store'
import router from './router'
import './element-ui'
import 'jquery-qrcode'

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
            //app.$router.push('/login');
        } else if (error.response.status === 419) {//csrf token invalid
            location.reload();
        } else {
            let errorMessage = error.response.data.message ? error.response.data.message : error.response.statusText;
            app.$message.error(errorMessage);
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