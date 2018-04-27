let commonjs = require("../../../utils/util.js")

Page({
  //页面的初始数据
  data: {
    orderDetail: '',
    orderId: '',
    rid: '',
    reservePrice: '',
    reason: '',
    isRefund: false,
    oweBalance: ''
  },

  // 监听页面加载
  onLoad: function (options) {
    wx.hideLoading();
    this.setData({
      orderId: options.order_id
    })
    this.getOrder();
  },

  //定金未支付订单去支付
  toPay: function (event) {
    let that = this,
      session = wx.getStorageSync('3r_session'),
      order = 'reserve_resraurant-' + that.data.orderId;
    //调用微信支付
    // commonjs.getAutograph(session, order);
    wx.navigateTo({
      url: '../yd-choose-pay/yd-choose-pay?order=' + order + '&session=' + session + '&price=' + that.data.reservePrice
    })
  },

  //已到店订单去支付
  arrivePay: function (event) {
    let that = this,
      session = wx.getStorageSync('3r_session'),
      order = 'reserve_resraurant-' + this.data.orderId;
    //调用微信支付
    // commonjs.getAutograph(session, order);
    wx.navigateTo({
      url: '../../yd-choose-pay/yd-choose-pay?order=' + order + '&session=' + session + '&price=' + that.data.oweBalance
    })
  },

  //获取订单列表
  getOrder: function () {
    let that = this;
    let userSession = wx.getStorageSync('3r_session');
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?3r_session=' + userSession + '&for_ajax=get_orders_info',
      data: {
        "order_id": that.data.orderId,
      },
      method: "POST",
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        console.log(res.data)
        if (res.data.code == 200) {
          that.setData({
            orderDetail: res.data.data,
            rid: res.data.data.restaurant_id,
            reservePrice: res.data.data.cash,
            oweBalance: res.data.data.owe_balance
          })
        }
      },
      fail: function (res) {
        console.log("网络错误，请稍后再试！")
      }
    })
  },

  //获取退款理由
  blurReason: function (e) {
    this.setData({
      reason: e.detail.value
    })
  },

  //申请退款
  refundMoney: function (event) {
    this.setData({
      isRefund: true
    })
  },

  //确认退款申请
  refundConfirm: function () {
    this.getRefund();
    this.setData({
      isRefund: false
    })
  },

  //客人到点通知商家
  arriveShop: function () {
    console.log(this.data.orderDetail)
    let orderDetail = this.data.orderDetail,
      data = orderDetail.desk.length != 0 ? ('桌子:' + orderDetail.desk[0].desk)
        : ('房间:' + orderDetail.room[0].room_name);
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=arrive_print',
      data: {
        title: data
      },
      method: "GET",
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        console.log(res.data);
        if (res.data.code == 200) {
          wx.showModal({
            title: '提示',
            content: '已提醒商家，请耐心等待您的美食！',
            showCancel: true,
            success: function (res) {
              if (res.confirm) {

              }
            }
          })
        }
      },
      fail: function (res) {
        console.log("网络错误，请稍后再试！")
      }
    })
  },

  //取消退款申请
  refundCancel: function () {
    this.setData({
      isRefund: false
    })
  },

  //请求申请退款数据
  getRefund: function () {
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=refunds',
      data: {
        "order_id": this.data.orderId,
        "refund_reason": this.data.reason
      },
      method: "POST",
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        if (res.data.code == 200) {
          wx.showModal({
            title: '提示',
            content: res.data.msg,
            success: function (res) {
              if (res.confirm) {
                wx.switchTab({
                  url: '../yd-order',
                })
              }
            }
          })
        }
      },
      fail: function (res) {
        console.log("网络错误，请稍后再试！")
      }
    })
  },

  //邀请函
  invitation: function () {
    wx.navigateTo({
      url: '../../yd-invitation/yd-invitation?rid=' + this.data.rid + '&reserve_price=' + this.data.reservePrice + '&order_id=' + this.data.orderId,
    })
  },

  //加菜
  addMenu: function () {
    wx.removeStorageSync('cartData');
    wx.navigateTo({
      url: '../../yd-menu/yd-menu?rid=' + this.data.rid + '&type=2&oid=' + this.data.orderId + '&name=' + this.data.orderDetail.company
    })
  },

  //呼叫服务员
  callWaiter: function (event) {
    console.log(this.data.orderDetail);
    let orderDetail = this.data.orderDetail,
      commonid = '',
      reserve_type = '';

    if (orderDetail.desk.length != 0) {
      reserve_type = 'desk'
      commonid = orderDetail.desk[0].id
    } else {
      reserve_type = 'room'
      commonid = orderDetail.room[0].id
    }
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/call_small_two.php?rid=324&commonid=76&reserve_type=desk',
      data: {
        rid: this.data.rid,
        commonid: commonid,
        reserve_type: reserve_type
      },
      method: "GET",
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        if (res.data.code == 200) {
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
        }
        console.log(res.data);
      },
      fail: function (res) {
        console.log("网络错误，请稍后再试！")
      }
    })
  },

  // 用户点击右上角分享
  onShareAppMessage: function (res) {

  }
})