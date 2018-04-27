Page({ 
  //页面的初始数据 
  data: {
    orders : '',
    orderStatus : 0,
    arrStatus : [],
    isLogin: true
  },

  //监听小程序显示
  onShow: function (options) {
    wx.hideLoading();
    let userSession = wx.getStorageSync('3r_session');
    this.getOrders(userSession);
  },
  
  //点击导航
  orderStatus:function(event){ 
    let that     = this,
        orders   = that.data.orders,
        arrOrder = [], 
        status   = event.currentTarget.dataset.status;

    that.setData({
      arrStatus: ''
    })
   
    for (let i = 0, iLength = that.data.orders.length;i < iLength;i++){
      if (status == 0){
        arrOrder.push(orders[i]);
      }else if (orders[i].status == status){
        arrOrder.push(orders[i]);
      }
    }

    //点击订单导航从第一条开始
    setTimeout(function(){
      that.setData({
        orderStatus: status,
        arrStatus: arrOrder
      })
    },50)
    
  },
  
  //订单详情
  orderDetail:function(event){
    let that = this;
    let orderid = event.currentTarget.dataset.id;
    wx.navigateTo({
      url: './yd-orderDetail/yd-orderDetail?order_id=' + orderid,
    })
  },

  //没登录时去登录
  toLogin :function(event){
    wx.switchTab({
      url: '../yd-home/yd-home'
    })
  },

  //获取订单列表
  getOrders: function (userSession) {
    let that = this;
    wx.request({
      url: "https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=get_orders&3r_session=" + userSession,
      method: "POST",
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        if(res.data.code == 200){
          that.setData({
            orders: res.data.data,
            arrStatus: res.data.data,
            isLogin : true
          })
        } else if (res.data.code == 100){
          that.setData({
            orders: '',
            isLogin : false
          })
        }
      },
      fail: function (res) {
        console.log("网络错误，请稍后再试！")
      }
    })
  },

  
  //用户点击右上角分享
  onShareAppMessage: function (res) {
    
  }
})