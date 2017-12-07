require('../bootstrap');

import Index from './pages/Index'

Vue.component('app-index', Index);

window.app = new Vue({
    el: '#app',
    template: '<app-index></app-index>'
});