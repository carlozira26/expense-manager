import "@babel/polyfill";
import Vue from 'vue'
import App from './App.vue'
import router from './router';
import 'material-design-icons-iconfont/dist/material-design-icons.css';
import '@fortawesome/fontawesome-free/css/all.css';
import mixins from './mixins';
import vuetify from './plugins/vuetify';

Vue.mixin(mixins);

Vue.config.productionTip = false

new Vue({
  router,
  vuetify,
  render: h => h(App)
}).$mount('#app')
