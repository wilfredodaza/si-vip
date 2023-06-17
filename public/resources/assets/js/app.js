import Vue from 'vue';



Vue.component('MonthComponent', require('./components/MonthComponent.vue').default);
Vue.component('WalletComponent', require('./components/WalletComponent.vue').default);
Vue.component('ProductComponent', require('./components/ProductComponent.vue').default);
Vue.component('CustomerComponent', require('./components/CustomerComponent.vue').default);
Vue.component('SellerComponent', require('./components/SellerComponent.vue').default);
Vue.component('SettingComponent', require('./components/SettingComponent.vue').default);
Vue.component('ChatComponent', require('./components/ChatComponent.vue').default);
Vue.component('RetentionComponent', require('./components/RetentionComponent.vue').default);


const app = new Vue({
    el: '#main',

});