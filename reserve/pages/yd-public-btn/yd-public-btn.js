let u = require('../../utils/api.js');

Component({
  properties: {
    // 这里定义了innerText属性，属性值可以在组件使用时指定
    innername:{
      type: String,
      value: ''
    }
  },
  data: {
    // 这里是一些组件内部数据
    fixStatic: true,
    flag: true
  },
  methods: {
    // 这里是一个自定义方法
    navclick: function(event){
      // flag = !flag;
      this.data.flag = !this.data.flag;
      this.setData({
        fixStatic: this.data.flag
      });
    },
    clickUrl: function(e){
      wx.getStorage({
        key: '3r_session',
        success: function(){
          wx.navigateTo({
            url: '/pages/yd-discountcard/yd-discountcard?userid=xxx'
          });
        },
        fail: function(){
          wx.switchTab({
            url: '/pages/yd-home/yd-home'
          });
        }
      });
      
    },
    clickReturn: function(e){
      this.triggerEvent('myevent');
    }
  }
})