
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
// Elements-UI
// import 'element-ui/lib/theme-chalk/index.css';
import Vue from 'vue';
import ElementUI from 'element-ui';
require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.use(ElementUI);

Vue.component('sidebar', require('./components/Sidebar.vue'));

Vue.component('friendchat', require('./components/Chatbox.vue'));

Vue.component('group-threads', require('./components/GroupThread.vue'));

Vue.component('block-thread', require('./components/BlockThread.vue'));

Vue.component('profile', require('./components/Profile.vue'));

Vue.component('search', require('./components/HomeSearch.vue'));

const app = new Vue({
    el: '#app'
});