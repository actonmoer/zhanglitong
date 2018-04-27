let passwordArr = [];

Page({
  data: {
    session: '',
    order: '',
    status: 'weixin',
    isWeixin: true,
    password: '',
    actual_fee: '0.00',
    isFocus: true,//控制input 聚焦
    wallets_password: '',
    avatarImg: ''
  },

  //  监听页面加载
  onLoad: function (options) {
    wx.hideLoading();
    this.setData({
      session: options.session,
      order: options.order,
      price: options.price
    })

    var value = wx.getStorageSync('userInfo')
    this.setData({
      avatarImg: value.avatarUrl
    })
  },

  //单选按钮组
  radioChange: function (event) {
    this.setData({
      status: event.detail.value
    })
  },

  //确认支付
  payname: function () {
    let status = this.data.status,
      session = this.data.session,
      order = this.data.order;
    if (status == 'weixin') {
      this.getAutograph(session, order);  //微信支付
    } else {
      this.setData({
        isWeixin: false
      })
    }
  },

  //获取MD5签名算法，取到的值给支付用
  getAutograph: function (session, order) {
    let that = this;
    wx.request({
      url: 'https://www.zhanglitong.com/wechatapp/pay/payfee.php',
      header: {
        'Content-Type': 'application/json'
      },
      data: {
        order: order,
        session: session
      },
      method: 'POST',
      success: function (res) {
        //调用支付函数
        that.pay(res.data);
      },
      fail: function (res) {
        console.log(res.data)
      }
    });
  },

  //调起小程序支付
  pay: function (param) {
    let that = this;
    let cutOrderid = that.data.order.split("-")[1];
    console.log(cutOrderid);
    wx.requestPayment({
      'timeStamp': param.timeStamp,
      'nonceStr': param.nonceStr,
      'package': param.package,
      'signType': 'MD5',
      'paySign': param.paySign,
      'success': function (res) {
        if (res.errMsg == "requestPayment:ok") {
          wx.navigateTo({
            url: '../yd-order/yd-orderDetail/yd-orderDetail?&order_id=' + cutOrderid
          })
          wx.removeStorageSync('cartData');
        }
      },
      'fail': function (res) {
        console.log('fail');
      }
    });
  },

  //聚焦input
  set_Focus() {
    console.log('isFocus', this.data.isFocus)
    this.setData({
      isFocus: true
    })
  },

  //失去焦点
  set_notFocus() {
    this.setData({
      isFocus: false
    })
  },

  //关闭钱包输入密码遮罩
  close_wallets_password() {
    this.setData({
      isFocus: false,//失去焦点
      isWeixin: true,
    })
  },

  //获取钱包密码
  set_wallets_password(e) {
    this.setData({
      wallets_password: e.detail.value
    });
    console.log(this.data.wallets_password);
    if (this.data.wallets_password.length == 6) {
      wx.showToast({
        title: '正在支付',
        icon: 'loading',
        duration: 2000,
        success: () => {
          this.paybalance();
        }
      })
    }
  },

  //余额支付
  paybalance: function () {
    let that = this;
    wx.request({
      url: 'https://www.zhanglitong.com/wechatapp/app_wallet.php',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
        "password": that.data.wallets_password,
        'money': that.data.price,
        'action': 'show_record',
        'module': 'reserve',
        "3r_session": that.data.session,
        "out_trade_no": that.data.order
      },
      method: 'POST',
      success: function (res) {
        if (res.data.code == 200) {
          that.setData({
            isWeixin: true
          })
          let cutOrderid = that.data.order.split("-")[1];
          wx.showToast({
            title: '支付成功！',
            icon: 'success',
            duration: 2000,
            success: function () {
              setTimeout(function () {
                wx.redirectTo({
                  url: '../yd-order/yd-orderDetail/yd-orderDetail?&order_id=' + cutOrderid
                })
              }, 3000)
            }
          })
        } else if (res.data.code == -200) {
          setTimeout(function () {
            wx.showModal({
              title: '提示',
              content: res.data.data,
              showCancel: false,
              success: function (res) {
                if (res.confirm) {
                  console.log('用户点击确定')
                }
              }
            })
          }, 2000)
        }
      },
      fail: function (res) {
        console.log(res.data)
      }
    });
  },

  // 用户点击右上角分享
  onShareAppMessage: function (res) {
    
  }
})