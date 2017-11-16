// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store'
import MintUi from 'mint-ui'
import 'mint-ui/lib/style.min.css'
import Axios from 'axios'
import qs from 'qs'
import VueTimeago from 'vue-timeago'
Axios.defaults.withCredentials = true

var captUrl = ''
var sourceUrl = ''
if (process.env.NODE_ENV === 'development') {
  Axios.defaults.baseURL = 'http://localhost/project/fenda/thinkphp5/public'
  captUrl = 'http://localhost/project/fenda/thinkphp5/public/api/capt/setCaptcha'
  sourceUrl = 'http://localhost/project/fenda/thinkphp5/public/static/api'
} else {
  Axios.defaults.baseURL = 'http://www.aoyuankj.com/fenda/public/index.php'
  captUrl = 'http://www.aoyuankj.com/fenda/public/index.php/api/capt/setCaptcha'
  sourceUrl = 'http://www.aoyuankj.com/fenda/public/static/api/'
}

// 跨域请求post会提交option,同时数据要变成json字符串www.aoyuankj.com
var myInterceptor = Axios.interceptors.request.use((config) => {
  config.data = qs.stringify(config.data)
  return config
}, function (error) {
  return Promise.reject(error)
})

Vue.use(MintUi)
Vue.prototype.$http = Axios
Vue.prototype.$myInterceptor = myInterceptor
Vue.prototype.$captUrl = captUrl
Vue.prototype.$sourceUrl = sourceUrl
Vue.config.productionTip = false
router.beforeEach(function (to, from, next) {
  if (to.meta.title) {
    document.title = to.meta.title
  }
  window.scrollTo(0, 0)
  next()
})
Vue.use(VueTimeago, {
  name: 'timeago', // component name, `timeago` by default
  locale: 'zh-CN',
  locales: {
    // you will need json-loader in webpack 1
    'zh-CN': require('vue-timeago/locales/zh-CN.json')
  }
})
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
})
