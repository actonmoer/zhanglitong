let commonjs = require("../../utils/util.js");

Page({
  //  页面的初始数据
  data: {
    rid                 : '', 
    persons             : '',
    mobile              : '',
    contact_name        : '',
    arrive_time         : '',
    reserve_type        : '',
    commonid            : '',
    invitation_title    : '',
    invitation_content  : '',
    is_menu             : true,
    addtime             : commonjs.formatTime(new Date()),
    cname               : '',
    cuisines            : [],
    reserve_price       : '',
    limitTime           : '',
    truename            : '',
    user                : '',
    orderId             : '',
    company             : '',
    hasInvitation       : false
  },

  // 生命周期函数--监听页面加载
  onLoad: function (options) {
    wx.hideLoading();
    this.setData({
      company    : options.name,
      reserve_type    : options.rtype,
      cname           : options.cname,
      reserve_price   : options.reserve_price,
      arrive_time     : options.time,
      rid             : options.rid,
      commonid        : options.commonid
    })
    this.limitTime();
  },

  //获取数据传后台
  bindName: function (event) {
    this.setData({
      contact_name: event.detail.value
    })
  },
  bindNumber: function (event) {
    this.setData({
      mobile: event.detail.value
    })
  },
  bindCount: function (event) {
    this.setData({
      persons: event.detail.value
    })
  },
  bindTitle: function (event) {
    this.setData({
      invitation_title: event.detail.value
    })
  },
  bindContent: function (event) {
    this.setData({
      invitation_content: event.detail.value
    })
  },
  swicthMenu: function (event) {
    this.setData({
      is_menu: event.detail.value
      })
  },

  //是否填写邀请函
  swicthInvitation: function(e) {
    console.log(e)
    this.setData({
      hasInvitation: e.detail.value
    })
  },

  //确认支付
  comfirmPay: function () {
    let that = this,
        reg = /^1\d{10}$/,
        param = that.data;

    //获取storage
    let getcuisines = wx.getStorageSync('cartData') ?
        wx.getStorageSync('cartData')[1] : ' ';
    that.setData({
      cuisines : getcuisines,
      user     : wx.getStorageSync('3r_session')
    });

    if (param.contact_name == '') {
      that.modal('请输入你的姓名！');
    } else if (param.mobile == '' || !reg.test(param.mobile)) {
      that.modal('请输入你正确的联系方式！');
    } else if (param.persons == '') {
      that.modal('请输入到点人数！');
    }
    // else if (param.invitation_title == '') {
    //   that.modal('请输入邀请函标题！');
    // } else if (param.invitation_content == '') {
    //   that.modal('请输入邀请内容！');
    // } 
    else {
      wx.showToast({
        title: '正在下单',
        icon: 'loading',
        duration: 2000,
        success : function(){
          that.confirm();  
        }
      })
    }
  },

  //模态框
  modal: function (cont) {
    wx.showModal({
      title: '提示',
      content: cont,
      showCancel: false,
      success: function (res) {
        if (res.confirm) {
          console.log('确定')
        }
      }
    })
  },

  //提交订单
  confirm: function () {
    let session = this.data.user;
    this.addOrder(session);
  },

  //统一下单
  addOrder: function (session){
    let that = this;
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=add_order&3r_session=' + session,
      data: that.data,
      method: 'POST',
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        let order;
        console.log(res)
        if (res.data.code == 200) {
          order = res.data.url.split('=')[2];
          that.setData({
            orderId: order
          })
          // commonjs.getAutograph(session, order);
          wx.navigateTo({
            url: '../yd-choose-pay/yd-choose-pay?order=' + order + '&session=' + session + '&price=' + that.data.reserve_price  
          })         
        }
      },
      fail: function (res) {
        console.log("网络错误，请稍后再试！")
      }
    })
  },

  //请求吃饭的限制时间
  limitTime: function () {
    let that = this;
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=get_limit_time_slot',
      data: {
        rid: that.data.rid,
      },
      method: 'POST',
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        if (res.data.code === 200) {
          that.setData({
            limitTime: res.data.slot
          })
        }
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