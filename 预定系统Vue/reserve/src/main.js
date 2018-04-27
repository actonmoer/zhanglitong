// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import axios from 'axios'
// import AMap from 'vue-amap';


// AMap.initAMapApiLoader({
//   // 申请的高德key
//   key: 'e09cda1de191e326ed532b0b22e7bd38',
//   // 插件集合
//   plugin: ['']
// });

Vue.config.productionTip = false
Vue.prototype.$http = axios

Vue.prototype.$ajax = function(data){
	// const API = "http://119.23.234.49:81/";
	const API = "https://www.zhanglitong.com/";
	axios({
		method : data.type,
		url : API + data.url,
		data : data.data,
		headers: {
			"Content-Type" : data.ContentType || "application/x-www-form-urlencoded; charset=UTF-8"
		}
	})
	.then((res) => {
        // var response = JSON.parse(res.request.responseText);
        // data.success(response.data);
        console.log(res.data);
	})
	.catch(data.error);

}

// console.log(iScroll)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
